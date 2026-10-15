<?php

namespace App\Services;

use App\Models\ProductVariant;
use Illuminate\Contracts\Session\Session;
use Illuminate\Validation\ValidationException;

// Cart is stored in the session as [variant_id => quantity]. Prices always come from the DB.
class CartService
{
    private const SESSION_KEY = 'cart';

    public function __construct(private Session $session) {}

    public function add(ProductVariant $variant, int $quantity): void
    {
        $this->ensurePurchasable($variant);

        $items = $this->items();
        $newQuantity = ($items[$variant->id] ?? 0) + $quantity;
        $this->ensureInStock($variant, $newQuantity);

        $items[$variant->id] = $newQuantity;
        $this->session->put(self::SESSION_KEY, $items);
    }

    public function update(ProductVariant $variant, int $quantity): void
    {
        $items = $this->items();

        if (! isset($items[$variant->id])) {
            throw ValidationException::withMessages(['variant' => 'This item is not in your cart.']);
        }

        $this->ensurePurchasable($variant);
        $this->ensureInStock($variant, $quantity);

        $items[$variant->id] = $quantity;
        $this->session->put(self::SESSION_KEY, $items);
    }

    public function remove(ProductVariant $variant): void
    {
        $items = $this->items();
        unset($items[$variant->id]);
        $this->session->put(self::SESSION_KEY, $items);
    }

    public function clear(): void
    {
        $this->session->forget(self::SESSION_KEY);
    }

    public function items(): array
    {
        return $this->session->get(self::SESSION_KEY, []);
    }

    public function count(): int
    {
        return array_sum($this->items());
    }

    public function summary(): array
    {
        $items = $this->items();

        $variants = ProductVariant::with('product')
            ->whereIn('id', array_keys($items))
            ->get()
            ->filter(fn (ProductVariant $variant) => $variant->product?->is_active);

        // remove items that were deleted or deactivated after being added
        if ($variants->count() !== count($items)) {
            $items = array_intersect_key($items, $variants->keyBy('id')->all());
            $this->session->put(self::SESSION_KEY, $items);
        }

        $lines = $variants
            ->map(fn (ProductVariant $variant) => [
                'variant_id' => $variant->id,
                'product_name' => $variant->product->name,
                'product_type' => $variant->product->type->label(),
                'origin' => $variant->product->origin,
                'image' => $variant->product->image ? asset($variant->product->image) : null,
                'weight_grams' => $variant->weight_grams,
                'weight_label' => $variant->weightLabel(),
                'unit_price' => $variant->price,
                'quantity' => $items[$variant->id],
                'line_total' => $variant->price * $items[$variant->id],
                'stock' => $variant->stock,
            ])
            ->sortBy([['product_name', 'asc'], ['weight_grams', 'asc']])
            ->values();

        return [
            'items' => $lines->all(),
            'count' => $lines->sum('quantity'),
            'subtotal' => $lines->sum('line_total'),
        ];
    }

    public function ensurePurchasable(ProductVariant $variant): void
    {
        if (! $variant->product?->is_active) {
            throw ValidationException::withMessages(['variant' => 'This product is no longer available.']);
        }
    }

    public function ensureInStock(ProductVariant $variant, int $quantity): void
    {
        if ($quantity > $variant->stock) {
            throw ValidationException::withMessages([
                'quantity' => $variant->stock > 0
                    ? "Only {$variant->stock} of {$variant->product->name} ({$variant->weightLabel()}) left in stock."
                    : "{$variant->product->name} ({$variant->weightLabel()}) is out of stock.",
            ]);
        }
    }
}
