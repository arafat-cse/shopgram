@extends('layouts.app')

@push('styles')
<style>
.cust-nav-pill {
    background: #f1f5f9;
    color: #475569;
}
.cust-nav-pill--active {
    background: #0d6efd;
    color: #fff;
}
.cust-nav-pill:hover:not(.cust-nav-pill--active) {
    background: #e2e8f0;
    color: #1e293b;
}
</style>
@endpush

@section('content')
<div class="container py-3 py-lg-4">

    {{-- ═══════════════════════════════════════════════════
         MOBILE ONLY: Compact profile + horizontal nav
    ═══════════════════════════════════════════════════ --}}
    <div class="d-lg-none mb-3">

        {{-- Profile strip --}}
        <div class="card border-0 shadow-sm mb-2" style="border-radius:14px">
            <div class="card-body py-2 px-3 d-flex align-items-center gap-3">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('storage/'.auth()->user()->avatar) }}"
                         class="rounded-circle flex-shrink-0"
                         width="44" height="44" style="object-fit:cover">
                @else
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0 fw-bold"
                         style="width:44px;height:44px;font-size:1.2rem">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
                <div class="flex-grow-1" style="min-width:0">
                    <div class="fw-semibold text-truncate" style="font-size:.92rem;line-height:1.2">
                        {{ auth()->user()->name }}
                    </div>
                    <small class="text-muted text-truncate d-block" style="font-size:.75rem">
                        {{ auth()->user()->phone ?? auth()->user()->email ?? 'Customer' }}
                    </small>
                </div>
                <a href="{{ route('customer.profile.edit') }}"
                   class="btn btn-sm btn-outline-secondary flex-shrink-0 px-2">
                    <i class="bi bi-pencil-square"></i>
                </a>
            </div>
        </div>

        {{-- Horizontal scrollable icon nav --}}
        <div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden">
            <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;scrollbar-width:none">
                <div class="d-flex px-2 py-2 gap-1" style="white-space:nowrap">

                    @php
                        $mobileNav = [
                            ['route' => 'customer.dashboard',      'icon' => 'bi-speedometer2',      'label' => 'Home',    'match' => 'customer.dashboard'],
                            ['route' => 'customer.orders.index',   'icon' => 'bi-bag-check',          'label' => 'Orders',  'match' => 'customer.orders.*'],
                            ['route' => 'order.tracking',          'icon' => 'bi-geo',                'label' => 'Track',   'match' => 'order.tracking'],
                            ['route' => 'customer.wishlist.index', 'icon' => 'bi-heart',              'label' => 'Wishlist','match' => 'customer.wishlist.*'],
                            ['route' => 'customer.addresses.index','icon' => 'bi-geo-alt',            'label' => 'Address', 'match' => 'customer.addresses.*'],
                            ['route' => 'customer.tickets.index',  'icon' => 'bi-headset',            'label' => 'Support', 'match' => 'customer.tickets.*'],
                            ['route' => 'customer.returns.index',  'icon' => 'bi-arrow-return-left',  'label' => 'Returns', 'match' => 'customer.returns.*'],
                            ['route' => 'customer.profile.edit',   'icon' => 'bi-person-gear',        'label' => 'Profile', 'match' => 'customer.profile.*'],
                        ];
                    @endphp

                    @foreach($mobileNav as $nav)
                        @php $active = request()->routeIs($nav['match']); @endphp
                        <a href="{{ route($nav['route']) }}"
                           class="d-inline-flex flex-column align-items-center gap-1 text-decoration-none px-3 py-2 flex-shrink-0 cust-nav-pill {{ $active ? 'cust-nav-pill--active' : '' }}"
                           style="border-radius:12px;min-width:62px;font-size:.7rem;font-weight:600;transition:all .15s">
                            <i class="bi {{ $nav['icon'] }}" style="font-size:1.25rem"></i>
                            {{ $nav['label'] }}
                        </a>
                    @endforeach

                    {{-- Logout --}}
                    <form action="{{ route('logout') }}" method="POST" class="d-inline flex-shrink-0">
                        @csrf
                        <button type="submit"
                                class="d-inline-flex flex-column align-items-center gap-1 border-0 px-3 py-2"
                                style="border-radius:12px;min-width:62px;font-size:.7rem;font-weight:600;
                                       background:#fff0f0;color:#dc3545;cursor:pointer">
                            <i class="bi bi-box-arrow-right" style="font-size:1.25rem"></i>
                            Logout
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         DESKTOP: Sidebar + Content (lg and up)
    ═══════════════════════════════════════════════════ --}}
    <div class="row g-4">

        {{-- Sidebar — desktop only --}}
        <div class="col-lg-3 d-none d-lg-block">
            <div class="card border-0 shadow-sm sticky-top" style="top:80px">
                <div class="card-body text-center py-4">
                    <div class="mb-2">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/'.auth()->user()->avatar) }}"
                                 class="rounded-circle" width="70" height="70" style="object-fit:cover">
                        @else
                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center"
                                 style="width:70px;height:70px;font-size:1.8rem">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <h6 class="mb-0 fw-semibold">{{ auth()->user()->name }}</h6>
                    <small class="text-muted">{{ auth()->user()->phone ?? auth()->user()->email }}</small>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('customer.dashboard') }}"
                       class="list-group-item list-group-item-action {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                    <a href="{{ route('customer.orders.index') }}"
                       class="list-group-item list-group-item-action {{ request()->routeIs('customer.orders.*') ? 'active' : '' }}">
                        <i class="bi bi-bag me-2"></i>My Orders
                    </a>
                    <a href="{{ route('customer.orders.index') }}"
                       class="list-group-item list-group-item-action position-relative" id="messageIcon">
                        <i class="bi bi-chat-dots me-2"></i>Messages
                        <span class="badge bg-danger rounded-pill d-none float-end" id="messageBadge"
                              style="font-size:.65rem;padding:2px 5px;">0</span>
                    </a>
                    <a href="{{ route('order.tracking') }}"
                       class="list-group-item list-group-item-action {{ request()->routeIs('order.tracking') ? 'active' : '' }}">
                        <i class="bi bi-geo me-2"></i>Track Order
                    </a>
                    <a href="{{ route('customer.wishlist.index') }}"
                       class="list-group-item list-group-item-action {{ request()->routeIs('customer.wishlist.*') ? 'active' : '' }}">
                        <i class="bi bi-heart me-2"></i>Wishlist
                    </a>
                    <a href="{{ route('customer.addresses.index') }}"
                       class="list-group-item list-group-item-action {{ request()->routeIs('customer.addresses.*') ? 'active' : '' }}">
                        <i class="bi bi-geo-alt me-2"></i>Addresses
                    </a>
                    <a href="{{ route('customer.tickets.index') }}"
                       class="list-group-item list-group-item-action {{ request()->routeIs('customer.tickets.*') ? 'active' : '' }}">
                        <i class="bi bi-headset me-2"></i>Support
                    </a>
                    <a href="{{ route('customer.returns.index') }}"
                       class="list-group-item list-group-item-action {{ request()->routeIs('customer.returns.*') ? 'active' : '' }}">
                        <i class="bi bi-arrow-return-left me-2"></i>Returns
                    </a>
                    <a href="{{ route('customer.profile.edit') }}"
                       class="list-group-item list-group-item-action {{ request()->routeIs('customer.profile.*', 'customer.password.*') ? 'active' : '' }}">
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

        {{-- Main content --}}
        <div class="col-12 col-lg-9">
            <x-alert />
            @yield('customer_content')
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const messageBadge = document.getElementById('messageBadge');
    if (!messageBadge) return;

    function fetchMessageCount() {
        fetch('/api/chat/total-unread', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.count > 0) {
                messageBadge.textContent = data.count > 99 ? '99+' : data.count;
                messageBadge.classList.remove('d-none');
            } else {
                messageBadge.classList.add('d-none');
            }
        })
        .catch(() => {});
    }

    fetchMessageCount();
    setInterval(fetchMessageCount, 30000);
});
</script>
@endpush
@endsection
