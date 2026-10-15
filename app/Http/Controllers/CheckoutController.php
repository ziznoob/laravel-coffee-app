<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(private CheckoutService $checkout) {}

    public function store(): JsonResponse
    {
        $order = $this->checkout->checkout();

        session()->flash('order_id', $order->id);

        return response()->json([
            'order_number' => $order->order_number,
            'redirect' => route('cart.success'),
        ], 201);
    }

    public function success(): View|RedirectResponse
    {
        $order = Order::withCount('items')->find(session('order_id'));

        // no order was just placed (e.g. page refreshed)
        if (! $order) {
            return redirect()->route('homepage');
        }

        return view('cart.success', compact('order'));
    }
}
