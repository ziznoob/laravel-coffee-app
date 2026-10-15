@extends('layouts.app')

@section('title', 'Orders')

@section('content')
    <div class="container py-4">
        <div class="d-flex align-items-baseline justify-content-between mb-4">
            <h1 class="h3 mb-0">Orders</h1>
            <p class="small text-body-secondary mb-0">{{ $orders->count() }} {{ Str::plural('order', $orders->count()) }}</p>
        </div>

        @if ($errors->has('status'))
            <div class="alert alert-danger" role="alert">{{ $errors->first('status') }}</div>
        @endif

        @forelse ($orders as $order)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <span class="fw-semibold">{{ $order->order_number }}</span>
                        <span class="badge {{ $order->status->badgeClass() }} ms-2">{{ $order->status->label() }}</span>
                        <div class="small text-body-secondary">{{ $order->created_at->format('d M Y, g:i A') }}</div>
                    </div>

                    <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="d-flex gap-2">
                        @csrf
                        @method('PATCH')
                        <label for="status-{{ $order->id }}" class="visually-hidden">Status for order {{ $order->order_number }}</label>
                        <select id="status-{{ $order->id }}" name="status" class="form-select form-select-sm">
                            @foreach (\App\Enums\OrderStatus::cases() as $status)
                                <option value="{{ $status->value }}" @selected($order->status === $status)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-dark btn-sm">Update</button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="ps-3">Product</th>
                                <th scope="col">Size</th>
                                <th scope="col" class="text-end">Unit price</th>
                                <th scope="col" class="text-end">Qty</th>
                                <th scope="col" class="text-end pe-3">Line total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td class="ps-3">{{ $item->variant->product->name }}</td>
                                    <td>{{ $item->variant->weightLabel() }}</td>
                                    <td class="text-end">{{ number_format($item->unit_price / 100, 2) }}</td>
                                    <td class="text-end">{{ $item->quantity }}</td>
                                    <td class="text-end pe-3">{{ number_format($item->lineTotal() / 100, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Total</th>
                                <th class="text-end pe-3">{{ $order->formattedTotal() }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @empty
            <div class="card card-body shadow-sm text-center py-5 text-body-secondary">
                No orders yet.
            </div>
        @endforelse
    </div>
@endsection
