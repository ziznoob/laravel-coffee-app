@extends('layouts.app')

@section('title', 'Cart')

@push('head')
    <meta name="robots" content="noindex, nofollow">
@endpush

@section('content')
    <div class="container py-4">
        <div data-react="Cart"
            data-props="{{ json_encode([
                'initialCart' => $cart,
                'itemsUrl' => route('cart.items.store'), //pass the url into the component
                'clearUrl' => route('cart.clear'),
                'checkoutUrl' => route('cart.checkout'),
                'shopUrl' => route('products.index'),
            ]) }}">
            <p class="text-body-secondary">Loading your cart…</p>
            <noscript>
                <p class="text-danger">Please enable JavaScript to manage your cart.</p>
            </noscript>
        </div>
    </div>
@endsection

@push('scripts')
    @viteReactRefresh
    @vite('resources/js/react-app.jsx')
@endpush
