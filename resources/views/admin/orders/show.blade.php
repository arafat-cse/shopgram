@extends('layouts.admin')
@section('title', 'Order '.$order->order_number)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Order #{{ $order->order_number }}</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-outline-secondary btn-sm" target="_blank"><i class="bi bi-printer"></i> Invoice</a>
        <a href="{{ route('admin.orders.invoice.pdf', $order) }}" class="btn btn-outline-danger btn-sm"><i class="bi bi-file-pdf"></i> PDF</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        {{-- Items --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">Order Items</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr><th>Product</th><th>Variant</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td class="small">{{ $item->product_name }}</td>
                            <td class="text-muted small">{{ $item->variant_info ? collect($item->variant_info)->filter()->implode(', ') : '-' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>৳{{ number_format($item->unit_price, 0) }}</td>
                            <td>৳{{ number_format($item->total_price, 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Update Status --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">Update Status</div>
            <div class="card-body">
                <form action="{{ route('admin.orders.status.update', $order) }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-5">
                            <select name="status" class="form-select">
                                @foreach(['pending','confirmed','processing','packed','shipped','out_for_delivery','delivered','cancelled','returned','refunded'] as $s)
                                <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="note" class="form-control" placeholder="Optional note...">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Assign Courier --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">Assign Courier</div>
            <div class="card-body">
                <form action="{{ route('admin.orders.courier.assign', $order) }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-5">
                            <select name="courier_id" class="form-select">
                                <option value="">Select Courier</option>
                                @foreach($couriers as $c)
                                <option value="{{ $c->id }}" {{ $order->courier_id === $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="courier_tracking_number" class="form-control" placeholder="Tracking number" value="{{ $order->courier_tracking_number }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success w-100">Assign</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Status History --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Status Timeline</div>
            <div class="card-body">
                @foreach($order->statusHistories as $h)
                <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                    <div class="text-muted small" style="min-width:140px">{{ $h->created_at->format('d M Y H:i') }}</div>
                    <div>
                        <x-order-status-badge :status="$h->status" />
                        @if($h->note)<p class="text-muted small mb-0 mt-1">{{ $h->note }}</p>@endif
                        @if($h->updatedBy)<small class="text-muted">by {{ $h->updatedBy->name }}</small>@endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-bold">Order Summary</div>
            <div class="card-body small">
                <div class="d-flex justify-content-between mb-1"><span>Subtotal</span><span>৳{{ number_format($order->subtotal,0) }}</span></div>
                @if($order->discount_amount > 0)<div class="d-flex justify-content-between mb-1 text-success"><span>Discount</span><span>-৳{{ number_format($order->discount_amount,0) }}</span></div>@endif
                <div class="d-flex justify-content-between mb-1"><span>Shipping</span><span>৳{{ number_format($order->shipping_charge,0) }}</span></div>
                <hr class="my-1">
                <div class="d-flex justify-content-between fw-bold"><span>Total</span><span>৳{{ number_format($order->total,0) }}</span></div>
                <div class="mt-2"><strong>Payment:</strong> {{ strtoupper($order->payment_method) }}<br>
                <strong>Status:</strong> <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'secondary' }}">{{ ucfirst($order->payment_status) }}</span></div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-bold">Customer</div>
            <div class="card-body small">
                <strong>{{ $order->user->name }}</strong><br>
                {{ $order->user->email }}<br>{{ $order->user->phone }}
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Shipping Address</div>
            <div class="card-body small">
                @php $addr = $order->shipping_address @endphp
                {{ $addr['name'] ?? '' }}<br>{{ $addr['phone'] ?? '' }}<br>{{ $addr['address_line'] ?? '' }}<br>{{ ($addr['city'] ?? '') . ', ' . ($addr['district'] ?? '') }}
            </div>
        </div>
    </div>
</div>
@endsection
