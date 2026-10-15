<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private CartService $cart) {}

    public function index(): View
    {
        return view('cart.index', ['cart' => $this->cart->summary()]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'variant_id' => ['required', 'uuid', 'exists:product_variants,id'],
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:99'],
        ]);

        $variant = ProductVariant::with('product')->findOrFail($data['variant_id']);
        $this->cart->add($variant, $data['quantity'] ?? 1);

        if ($request->expectsJson()) {
            return response()->json($this->cart->summary(), 201);
        }

        return back()->with('status', "Added {$variant->product->name} ({$variant->weightLabel()}) to your cart.");
    }

    public function update(Request $request, ProductVariant $variant): JsonResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $this->cart->update($variant->load('product'), $data['quantity']);

        return response()->json($this->cart->summary());
    }

    public function destroy(ProductVariant $variant): JsonResponse
    {
        $this->cart->remove($variant);

        return response()->json($this->cart->summary());
    }

    public function clear(): JsonResponse
    {
        $this->cart->clear();

        return response()->json($this->cart->summary());
    }
}
