<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function __construct(private CartService $cart) {}

    public function checkout(): Order
    {
        $items = $this->cart->items();

        if ($items === []) {
            throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);
        }

        $order = DB::transaction(function () use ($items) {
            // lock rows so two checkouts can't oversell
            $variants = ProductVariant::with('product')
                ->whereIn('id', array_keys($items))
                ->lockForUpdate() 
                ->get()
                ->keyBy('id');

            foreach ($items as $variantId => $quantity) {
                $variant = $variants->get($variantId);

                if (! $variant) {
                    throw ValidationException::withMessages(['cart' => 'An item in your cart is no longer available.']);
                }

                $this->cart->ensurePurchasable($variant);
                $this->cart->ensureInStock($variant, $quantity);
            }

            $order = Order::create([
                'total' => collect($items)->sum(fn ($quantity, $variantId) => $variants[$variantId]->price * $quantity),
            ]);

            foreach ($items as $variantId => $quantity) {
                $variant = $variants[$variantId];

                $order->items()->create([
                    'product_variant_id' => $variant->id,
                    'quantity' => $quantity,
                    'unit_price' => $variant->price, // price at time of purchase
                ]);

                $variant->decrement('stock', $quantity);
            }

            return $order;
        });

        $this->cart->clear();

        return $order;
    }
}
