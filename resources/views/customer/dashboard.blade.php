@extends('layouts.customer')
@section('title', 'My Dashboard')
@section('customer_content')

@php
    $hour = now()->format('G');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">{{ $greeting }}, {{ explode(' ', auth()->user()->name)[0] }}!</h4>
        <small class="text-muted">Member since {{ auth()->user()->created_at->format('M Y') }}</small>
    </div>
</div>

{{-- Profile completion nudge --}}
@if($needsPhone || $needsAddress)
<div class="alert alert-warning d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4" role="alert">
    <div>
        <i class="bi bi-exclamation-triangle-fill me-1"></i>
        @if($needsPhone && $needsAddress)
            Add your phone number and delivery address to place orders smoothly.
        @elseif($needsPhone)
            Please add your phone number to complete your profile.
        @else
            Add a delivery address so you can check out faster.
        @endif
    </div>
    <a href="{{ $needsPhone ? route('customer.profile.edit') : route('customer.addresses.index') }}"
       class="btn btn-sm btn-warning fw-semibold">Complete Now</a>
</div>
@endif

<div class="row g-3 mb-4">
    {{-- Coin balance — prominent --}}
    <div class="col-12 col-md-6 col-lg-4">
        <a href="{{ route('customer.coins.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100" style="background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:14px;">
                <div class="card-body p-4 text-white d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-white-50 small mb-1">Coin Balance</div>
                        <div class="fw-bold" style="font-size:1.9rem;line-height:1;">
                            <i class="bi bi-coin"></i> {{ number_format(auth()->user()->coins_balance) }}
                        </div>
                        <div class="small text-white-50 mt-1">Tap to redeem or view history</div>
                    </div>
                    <i class="bi bi-chevron-right fs-4 text-white-50"></i>
                </div>
            </div>
        </a>
    </div>

    {{-- Total spent --}}
    <div class="col-6 col-md-3 col-lg-4">
        <div class="card border-0 shadow-sm h-100 text-center py-3 d-flex justify-content-center">
            <div class="fs-3 fw-bold text-primary">৳{{ number_format($stats['total_spent'], 0) }}</div>
            <small class="text-muted">Total Spent</small>
        </div>
    </div>

    {{-- Active orders --}}
    <div class="col-6 col-md-3 col-lg-4">
        <a href="{{ route('customer.orders.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 text-center py-3">
                <div class="fs-3 fw-bold text-warning">{{ $stats['active'] }}</div>
                <small class="text-muted">Active Orders</small>
            </div>
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-dark">{{ $stats['orders'] }}</div>
            <small class="text-muted">Total Orders</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-success">{{ $stats['delivered'] }}</div>
            <small class="text-muted">Delivered</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('customer.wishlist.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm text-center py-3 position-relative">
                <div class="fs-4 fw-bold text-danger">{{ $wishlistCount }}</div>
                <small class="text-muted">Wishlist</small>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('customer.tickets.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-4 fw-bold text-info">{{ $openTicketCount }}</div>
                <small class="text-muted">Open Tickets</small>
            </div>
        </a>
    </div>
</div>

{{-- Quick actions --}}
<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="{{ route('order.tracking') }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-geo me-1"></i>Track Order</a>
    <a href="{{ route('customer.addresses.index') }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-geo-alt me-1"></i>Addresses</a>
    <a href="{{ route('customer.returns.index') }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-arrow-return-left me-1"></i>Returns</a>
    <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-bag-plus me-1"></i>Continue Shopping</a>
</div>

<div class="card border-0 shadow-sm mb-4">
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
                    <td class="fw-semibold">
                        {{ $order->order_number }}
                        @if($order->is_coin_redemption)
                            <span class="badge bg-warning text-dark ms-1" title="Paid with coins"><i class="bi bi-coin"></i></span>
                        @endif
                    </td>
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

@if($recentlyViewedProducts->count())
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-bold">Recently Viewed</div>
    <div class="card-body">
        <div class="row g-3">
            @foreach($recentlyViewedProducts as $product)
            <div class="col-6 col-md-4 col-lg-2">
                <x-product-card :product="$product" />
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@endsection
