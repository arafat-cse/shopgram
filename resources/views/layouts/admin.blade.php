<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ $siteName ?? 'ShopGram' }} Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --admin-primary: #2c3e50; --admin-accent: #e91e63; }
        body { background: #f4f6f9; }
        .sidebar { width: 250px; min-height: 100vh; background: var(--admin-primary); position: fixed; top: 0; left: 0; z-index: 100; transition: transform .3s; overflow-y: auto; }
        .sidebar-brand { padding: 1rem 1.2rem; border-bottom: 1px solid rgba(255,255,255,.1); color: #fff; font-weight: 700; font-size: 1.2rem; text-decoration: none; display: block; }
        .sidebar-brand span { color: var(--admin-accent); }
        .sidebar .nav-link { color: rgba(255,255,255,.75); padding: .5rem 1.2rem; font-size: .9rem; border-radius: 0; display: flex; align-items: center; gap: .6rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,.1); }
        .sidebar .nav-section { color: rgba(255,255,255,.4); font-size: .7rem; text-transform: uppercase; letter-spacing: .1em; padding: .8rem 1.2rem .3rem; }
        .main-content { margin-left: 250px; min-height: 100vh; }
        .top-bar { background: #fff; border-bottom: 1px solid #eee; padding: .7rem 1.5rem; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 99; }
        .page-content { padding: 1.5rem; }
        .stat-card { border: none; border-radius: 12px; }
        .stat-card .icon { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 99; }
        .sidebar-overlay.show { display: block; }
    </style>
    @stack('styles')
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

{{-- Sidebar --}}
<aside class="sidebar" id="sidebar">
    <a class="sidebar-brand" href="{{ route('admin.dashboard') }}">Shop<span>Gram</span> Admin</a>
    <nav class="py-2">
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="nav-section">Catalog</div>
        <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> Products
        </a>
        <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <i class="bi bi-diagram-3"></i> Categories
        </a>
        <a href="{{ route('admin.brands.index') }}" class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
            <i class="bi bi-award"></i> Brands
        </a>
        <a href="{{ route('admin.inventory.index') }}" class="nav-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
            <i class="bi bi-archive"></i> Inventory
        </a>

        <div class="nav-section">Sales</div>
        <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <i class="bi bi-cart3"></i> Orders
        </a>
        <a href="{{ route('admin.payments.index') }}" class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
            <i class="bi bi-credit-card"></i> Payments
        </a>
        <a href="{{ route('admin.coupons.index') }}" class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
            <i class="bi bi-ticket-perforated"></i> Coupons
        </a>

        <div class="nav-section">Delivery</div>
        <a href="{{ route('admin.shipping-zones.index') }}" class="nav-link {{ request()->routeIs('admin.shipping-zones.*') ? 'active' : '' }}">
            <i class="bi bi-geo-alt"></i> Shipping Zones
        </a>
        <a href="{{ route('admin.couriers.index') }}" class="nav-link {{ request()->routeIs('admin.couriers.*') ? 'active' : '' }}">
            <i class="bi bi-truck"></i> Couriers
        </a>

        <div class="nav-section">Users</div>
        <a href="{{ route('admin.customers.index') }}" class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Customers
        </a>
        <a href="{{ route('admin.admin-users.index') }}" class="nav-link {{ request()->routeIs('admin.admin-users.*') ? 'active' : '' }}">
            <i class="bi bi-person-badge"></i> Admin Users
        </a>
        <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
            <i class="bi bi-shield-check"></i> Roles
        </a>

        <div class="nav-section">Content</div>
        <a href="{{ route('admin.banners.index') }}" class="nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
            <i class="bi bi-image"></i> Banners
        </a>
        <a href="{{ route('admin.pages.index') }}" class="nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
            <i class="bi bi-file-text"></i> Pages
        </a>
        <a href="{{ route('admin.newsletter.index') }}" class="nav-link {{ request()->routeIs('admin.newsletter.*') ? 'active' : '' }}">
            <i class="bi bi-envelope"></i> Newsletter
        </a>

        <div class="nav-section">Support</div>
        <a href="{{ route('admin.reviews.index') }}" class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
            <i class="bi bi-star"></i> Reviews
        </a>
        <a href="{{ route('admin.tickets.index') }}" class="nav-link {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}">
            <i class="bi bi-headset"></i> Tickets
        </a>

        <div class="nav-section">Reports</div>
        <a href="{{ route('admin.reports.sales') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart"></i> Reports
        </a>

        <div class="nav-section">System</div>
        <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i> Settings
        </a>
        <a href="{{ route('home') }}" class="nav-link" target="_blank">
            <i class="bi bi-box-arrow-up-right"></i> View Site
        </a>
    </nav>
</aside>

<div class="main-content">
    {{-- Top Bar --}}
    <div class="top-bar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-light d-lg-none" onclick="toggleSidebar()">
                <i class="bi bi-list fs-5"></i>
            </button>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="dropdown">
                <a href="#" class="dropdown-toggle text-dark text-decoration-none d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle fs-5"></i>
                    <span class="d-none d-md-inline small">{{ auth()->user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item small" href="{{ route('home') }}" target="_blank">View Site</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="dropdown-item small text-danger">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    <div class="px-4 pt-3">
        <x-alert />
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
    </div>

    <div class="page-content">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('show');
    document.getElementById('sidebarOverlay').classList.toggle('show');
}
</script>
@stack('scripts')
</body>
</html>
