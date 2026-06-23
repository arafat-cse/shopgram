@extends('layouts.customer')
@section('title', 'My Dashboard')
@section('customer_content')
<h4 class="fw-bold mb-4">My Dashboard</h4>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-2 fw-bold text-primary">{{ $stats['orders'] }}</div>
            <small class="text-muted">Total Orders</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-2 fw-bold text-warning">{{ $stats['pending'] }}</div>
            <small class="text-muted">Pending</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-2 fw-bold text-success">{{ $stats['delivered'] }}</div>
            <small class="text-muted">Delivered</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-2 fw-bold text-danger">{{ $stats['wishlist'] }}</div>
            <small class="text-muted">Wishlist</small>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-bold d-flex justify-content-between">
        Recent Orders
        <a href="{{ route('customer.orders.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Order #</th><th>Date</th><th>Total</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                <tr>
                    <td class="fw-semibold">{{ $order->order_number }}</td>
                    <td class="text-muted small">{{ $order->created_at->format('d M Y') }}</td>
                    <td>৳{{ number_format($order->total, 0) }}</td>
                    <td><x-order-status-badge :status="$order->status" /></td>
                    <td><a href="{{ route('customer.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">View</a></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
