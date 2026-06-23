@extends('layouts.customer')
@section('title', 'Order '.$order->order_number)
@section('customer_content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Order #{{ $order->order_number }}</h4>
    <x-order-status-badge :status="$order->status" />
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">Order Items</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr><th>Product</th><th>Variant</th><th>Qty</th><th>Price</th><th>Total</th></tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td class="text-muted small">{{ $item->variant_info ? implode(', ', array_filter($item->variant_info)) : '-' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>৳{{ number_format($item->unit_price, 0) }}</td>
                            <td>৳{{ number_format($item->total_price, 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Order Timeline</div>
            <div class="card-body">
                @foreach($order->statusHistories as $history)
                <div class="d-flex gap-3 mb-3">
                    <div class="text-muted small" style="min-width:130px">{{ $history->created_at->format('d M Y H:i') }}</div>
                    <div>
                        <x-order-status-badge :status="$history->status" />
                        @if($history->note)<div class="text-muted small mt-1">{{ $history->note }}</div>@endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-bold">Order Summary</div>
            <div class="card-body">
                <div class="d-flex justify-content-between small mb-1"><span>Subtotal</span><span>৳{{ number_format($order->subtotal, 0) }}</span></div>
                @if($order->discount_amount > 0)<div class="d-flex justify-content-between small mb-1 text-success"><span>Discount</span><span>-৳{{ number_format($order->discount_amount, 0) }}</span></div>@endif
                <div class="d-flex justify-content-between small mb-1"><span>Shipping</span><span>৳{{ number_format($order->shipping_charge, 0) }}</span></div>
                <hr class="my-2">
                <div class="d-flex justify-content-between fw-bold"><span>Total</span><span class="text-danger">৳{{ number_format($order->total, 0) }}</span></div>
                <div class="mt-2 small"><span class="text-muted">Payment:</span> <strong>{{ strtoupper($order->payment_method) }}</strong> — <x-order-status-badge :status="$order->payment_status" /></div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Shipping Address</div>
            <div class="card-body small">
                @php $addr = $order->shipping_address @endphp
                <p class="mb-0">{{ $addr['name'] ?? '' }}<br>{{ $addr['phone'] ?? '' }}<br>{{ $addr['address_line'] ?? '' }}<br>{{ $addr['city'] ?? '' }}, {{ $addr['district'] ?? '' }}</p>
            </div>
        </div>

        @if($order->courier && $order->courier_tracking_number)
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body small">
                <strong>Courier:</strong> {{ $order->courier->name }}<br>
                <strong>Tracking:</strong> {{ $order->courier_tracking_number }}
                @if($link = $order->courier->getTrackingLink($order->courier_tracking_number))
                <a href="{{ $link }}" target="_blank" class="btn btn-sm btn-outline-primary d-block mt-2">Track Package</a>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
