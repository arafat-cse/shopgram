@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row g-4">
        {{-- Sidebar --}}
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-4">
                    <div class="mb-2">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/'.auth()->user()->avatar) }}" class="rounded-circle" width="70" height="70" style="object-fit:cover">
                        @else
                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width:70px;height:70px;font-size:1.8rem">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                    <small class="text-muted">{{ auth()->user()->email }}</small>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('customer.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                    <a href="{{ route('customer.orders.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('customer.orders.*') ? 'active' : '' }}">
                        <i class="bi bi-bag me-2"></i>My Orders
                    </a>
                    <a href="{{ route('order.tracking') }}" class="list-group-item list-group-item-action {{ request()->routeIs('order.tracking') ? 'active' : '' }}">
                        <i class="bi bi-geo me-2"></i>Track Order
                    </a>
                    <a href="{{ route('customer.wishlist.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('customer.wishlist.*') ? 'active' : '' }}">
                        <i class="bi bi-heart me-2"></i>Wishlist
                    </a>
                    <a href="{{ route('customer.addresses.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('customer.addresses.*') ? 'active' : '' }}">
                        <i class="bi bi-geo-alt me-2"></i>Addresses
                    </a>
                    <a href="{{ route('customer.tickets.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('customer.tickets.*') ? 'active' : '' }}">
                        <i class="bi bi-headset me-2"></i>Support
                    </a>
                    <a href="{{ route('customer.returns.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('customer.returns.*') ? 'active' : '' }}">
                        <i class="bi bi-arrow-return-left me-2"></i>Returns
                    </a>
                    <a href="{{ route('customer.profile.edit') }}" class="list-group-item list-group-item-action {{ request()->routeIs('customer.profile.*', 'customer.password.*') ? 'active' : '' }}">
                        <i class="bi bi-person-gear me-2"></i>Profile
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="list-group-item list-group-item-action text-danger border-0 w-100 text-start">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="col-lg-9">
            <x-alert />
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @yield('customer_content')
        </div>
    </div>
</div>
@endsection
