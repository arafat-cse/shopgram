@extends('layouts.customer')
@section('title', 'My Orders')
@section('customer_content')
<h4 class="fw-bold mb-4">My Orders</h4>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Order #</th><th>Date</th><th>Items</th><th>Total</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td class="fw-semibold">{{ $order->order_number }}</td>
                    <td class="text-muted small">{{ $order->created_at->format('d M Y') }}</td>
                    <td>{{ $order->items()->count() }}</td>
                    <td>৳{{ number_format($order->total, 0) }}</td>
                    <td><x-order-status-badge :status="$order->status" /></td>
                    <td><a href="{{ route('customer.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">Details</a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $orders->links() }}</div>
</div>
@endsection
