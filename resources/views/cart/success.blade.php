@extends('layouts.app')

@section('title', 'Order confirmed')

@push('head')
    <meta name="robots" content="noindex, nofollow">
@endpush

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card card-body shadow-sm text-center p-4 p-md-5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-check-circle-fill text-success mx-auto mb-3" viewBox="0 0 16 16" aria-hidden="true">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                    </svg>

                    <h1 class="h3">Order confirmed</h1>
                    <p class="text-body-secondary mb-4">
                        Thanks for your purchase! We've received your order.
                    </p>

                    <dl class="row text-start border rounded mx-0 py-3 mb-4">
                        <dt class="col-6 fw-normal text-body-secondary">Order number</dt>
                        <dd class="col-6 text-end fw-semibold mb-2">{{ $order->order_number }}</dd>

                        <dt class="col-6 fw-normal text-body-secondary">Items</dt>
                        <dd class="col-6 text-end mb-2">{{ $order->items_count }}</dd>

                        <dt class="col-6 fw-normal text-body-secondary">Total paid</dt>
                        <dd class="col-6 text-end fw-semibold mb-0">{{ $order->formattedTotal() }}</dd>
                    </dl>

                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                        <a href="{{ route('homepage') }}" class="btn btn-dark px-4">Return to home</a>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-dark px-4">Continue shopping</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
