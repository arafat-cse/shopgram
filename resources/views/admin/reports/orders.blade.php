@extends('layouts.admin')
@section('title', 'Orders Report')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Orders Report</h4>
    <a href="{{ route('admin.reports.export', 'orders') }}" class="btn btn-outline-success btn-sm">Export CSV</a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">From</label>
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $from }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">To</label>
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $to }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach(['pending','confirmed','processing','packed','shipped','out_for_delivery','delivered','cancelled','returned','refunded'] as $s)
                    <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead class="table-light">
                <tr><th>Order #</th><th>Customer</th><th>Status</th><th>Payment</th><th>Total</th><th>Date</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td class="small fw-semibold">{{ $order->order_number }}</td>
                    <td class="small">{{ $order->user->name ?? '-' }}</td>
                    <td><x-order-status-badge :status="$order->status" /></td>
                    <td><span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning text-dark' }}">{{ ucfirst($order->payment_status) }}</span></td>
                    <td class="small fw-semibold">৳{{ number_format($order->total, 0) }}</td>
                    <td class="small text-muted">{{ $order->created_at->format('d M Y') }}</td>
                    <td><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-xs btn-outline-primary" style="font-size:11px;padding:2px 8px;">View</a></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">No orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $orders->withQueryString()->links() }}</div>
</div>
@endsection
