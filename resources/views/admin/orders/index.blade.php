@extends('layouts.admin')
@section('title', 'Orders')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Orders</h4>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Order # or customer name" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    @foreach(['pending','confirmed','processing','packed','shipped','out_for_delivery','delivered','cancelled','returned','refunded'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Order #</th><th>Customer</th><th>Date</th><th>Total</th><th>Payment</th><th>Status</th><th>Messages</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td class="fw-semibold small">{{ $order->order_number }}</td>
                    <td class="small">{{ $order->user->name ?? '-' }}</td>
                    <td class="text-muted small">{{ $order->created_at->format('d M Y') }}</td>
                    <td class="small">৳{{ number_format($order->total, 0) }}</td>
                    <td><span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'failed' ? 'danger' : 'secondary') }}">{{ ucfirst($order->payment_status) }}</span></td>
                    <td><x-order-status-badge :status="$order->status" /></td>
                    <td>
                        @php($latestMessage = $order->latestCustomerMessage)
                        @if($latestMessage)
                            <a href="{{ route('admin.orders.show', $order) }}#orderChat" class="text-decoration-none d-inline-flex align-items-start gap-2">
                                <span class="btn btn-sm btn-outline-primary position-relative">
                                    <i class="bi bi-chat-dots"></i>
                                    @if($order->unread_customer_messages_count > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ $order->unread_customer_messages_count }}
                                        </span>
                                    @endif
                                </span>
                                <span class="small text-dark">
                                    <span class="d-block fw-semibold">{{ $latestMessage->user->name ?? $order->user->name ?? 'Customer' }}</span>
                                    <span class="d-block text-muted text-truncate" style="max-width:180px">
                                        {{ $latestMessage->message ?: ($latestMessage->attachment_type === 'image' ? 'Sent an image' : 'Sent a file') }}
                                    </span>
                                </span>
                            </a>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">No orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $orders->links() }}</div>
</div>
@endsection
