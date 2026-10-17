@extends('layouts.admin')
@section('title', 'Invoice & Labels')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Invoice &amp; Labels</h4>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Order # or customer name" value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Confirmed Orders</option>
                    @foreach(['confirmed','processing','packed','shipped','out_for_delivery','delivered'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('admin.invoice-labels.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Order #</th><th>Customer</th><th>Date</th><th>Total</th><th>Payment</th><th>Status</th><th>Print</th></tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td class="fw-semibold small">{{ $order->order_number }}</td>
                    <td class="small">{{ $order->user->name ?? '-' }}</td>
                    <td class="text-muted small">{{ $order->created_at->format('d M Y') }}</td>
                    <td class="small">৳{{ number_format($order->total, 0) }}</td>
                    <td>
                        <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'failed' ? 'danger' : 'secondary') }}">
                            {{ $order->payment_status === 'paid' ? 'Paid' : strtoupper($order->payment_method) }}
                        </span>
                    </td>
                    <td><x-order-status-badge :status="$order->status" /></td>
                    <td class="text-nowrap">
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary" title="View Order"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Print Invoice"><i class="bi bi-printer"></i></a>
                        <a href="{{ route('admin.orders.invoice.pdf', $order) }}" class="btn btn-sm btn-outline-danger" title="Download Invoice PDF"><i class="bi bi-file-pdf"></i></a>
                        <a href="{{ route('admin.orders.shipping-label', $order) }}" target="_blank" class="btn btn-sm btn-outline-success" title="Print Shipping Label"><i class="bi bi-box-seam"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">No confirmed orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $orders->links() }}</div>
</div>
@endsection
