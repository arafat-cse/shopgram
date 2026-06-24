@extends('layouts.admin')
@section('title', 'Customer Details')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Customer Details</h4>
        <div class="text-muted small">{{ $customer->name }}</div>
    </div>
    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Total Orders</div>
                <div class="fs-4 fw-bold">{{ $customer->orders_count }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Delivered Orders</div>
                <div class="fs-4 fw-bold text-success">{{ $customer->delivered_orders_count }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Pending Orders</div>
                <div class="fs-4 fw-bold text-warning">{{ $customer->pending_orders_count }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Delivered Total</div>
                <div class="fs-4 fw-bold">{{ $currencySymbol ?? 'Tk' }}{{ number_format($totalSpent, 0) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">Profile</div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if($customer->avatar)
                        <img src="{{ asset('storage/'.$customer->avatar) }}" class="rounded-circle" width="58" height="58" style="object-fit:cover" alt="{{ $customer->name }}">
                    @else
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width:58px;height:58px;font-size:1.4rem">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <div class="fw-semibold">{{ $customer->name }}</div>
                        <span class="badge bg-{{ $customer->status === 'active' ? 'success' : 'danger' }}">{{ ucfirst($customer->status) }}</span>
                    </div>
                </div>

                <div class="small mb-2"><strong>Email:</strong> {{ $customer->email }}</div>
                <div class="small mb-2"><strong>Phone:</strong> {{ $customer->phone ?? '-' }}</div>
                <div class="small mb-3"><strong>Joined:</strong> {{ $customer->created_at->format('d M Y h:i A') }}</div>

                <form action="{{ route('admin.customers.toggle-status', $customer) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-{{ $customer->status === 'active' ? 'outline-danger' : 'outline-success' }}">
                        {{ $customer->status === 'active' ? 'Block Customer' : 'Activate Customer' }}
                    </button>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Addresses</div>
            <div class="card-body">
                @forelse($customer->addresses as $address)
                    <div class="border-bottom pb-3 mb-3 small">
                        <div class="fw-semibold text-capitalize">{{ $address->label }} @if($address->is_default)<span class="badge bg-primary">Default</span>@endif</div>
                        <div>{{ $address->name ?? $customer->name }}</div>
                        <div>{{ $address->phone ?? $customer->phone }}</div>
                        <div class="text-muted">{{ $address->address_line }}, {{ $address->city }}, {{ $address->district }}</div>
                    </div>
                @empty
                    <div class="text-muted small">No addresses found.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>Orders</strong>
                <span class="badge bg-secondary">{{ $customer->orders_count }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customer->orders as $order)
                            <tr>
                                <td class="small fw-semibold">{{ $order->order_number }}</td>
                                <td class="small text-muted">{{ $order->created_at->format('d M Y') }}</td>
                                <td><span class="badge bg-light text-dark">{{ $order->items->sum('quantity') }}</span></td>
                                <td class="small">{{ $currencySymbol ?? 'Tk' }}{{ number_format($order->total, 0) }}</td>
                                <td><span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'failed' ? 'danger' : 'secondary') }}">{{ ucfirst($order->payment_status) }}</span></td>
                                <td><x-order-status-badge :status="$order->status" /></td>
                                <td><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted">No orders found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Support Tickets</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Subject</th><th>Status</th><th>Priority</th><th>Date</th><th></th></tr></thead>
                    <tbody>
                        @forelse($customer->tickets as $ticket)
                            <tr>
                                <td class="small">{{ $ticket->subject }}</td>
                                <td><span class="badge bg-secondary">{{ ucfirst($ticket->status) }}</span></td>
                                <td class="small">{{ ucfirst($ticket->priority) }}</td>
                                <td class="small text-muted">{{ $ticket->created_at->format('d M Y') }}</td>
                                <td><a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">No tickets found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
