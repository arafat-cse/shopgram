@extends('layouts.admin')
@section('title', 'Dashboard')
@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard</li>
@endsection
@section('content')

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-currency-dollar"></i></div>
                <div>
                    <div class="fw-bold fs-5">৳{{ number_format($stats['total_sales'], 0) }}</div>
                    <small class="text-muted">Total Sales</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="icon bg-success bg-opacity-10 text-success"><i class="bi bi-cart3"></i></div>
                <div>
                    <div class="fw-bold fs-5">{{ $stats['total_orders'] }}</div>
                    <small class="text-muted">Total Orders</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock"></i></div>
                <div>
                    <div class="fw-bold fs-5">{{ $stats['pending_orders'] }}</div>
                    <small class="text-muted">Pending Orders</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="icon bg-info bg-opacity-10 text-info"><i class="bi bi-people"></i></div>
                <div>
                    <div class="fw-bold fs-5">{{ $stats['total_customers'] }}</div>
                    <small class="text-muted">Customers</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        {{-- Recent Orders --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold d-flex justify-content-between">
                Recent Orders
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Order #</th><th>Customer</th><th>Total</th><th>Status</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                        <tr>
                            <td class="fw-semibold small">{{ $order->order_number }}</td>
                            <td class="small">{{ $order->user->name ?? '-' }}</td>
                            <td class="small">৳{{ number_format($order->total, 0) }}</td>
                            <td><x-order-status-badge :status="$order->status" /></td>
                            <td><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-xs btn-outline-secondary btn-sm py-0">View</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Low Stock --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold text-warning">
                <i class="bi bi-exclamation-triangle"></i> Low Stock
            </div>
            <div class="list-group list-group-flush">
                @forelse($lowStockItems as $p)
                <a href="{{ route('admin.inventory.index') }}" class="list-group-item list-group-item-action small">
                    <div class="d-flex justify-content-between">
                        <span>{{ Str::limit($p->name, 30) }}</span>
                        <span class="badge bg-warning text-dark">{{ $p->stock_quantity }}</span>
                    </div>
                </a>
                @empty
                <div class="list-group-item text-muted small text-center">All stock levels OK</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
