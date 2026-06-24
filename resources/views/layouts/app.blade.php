<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $siteName ?? 'ShopGram')</title>
    <meta name="description" content="@yield('meta_description', 'ShopGram - Best Online Shopping')">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --primary: #e91e63; --primary-dark: #c2185b; }
        body { font-family: 'Segoe UI', sans-serif; padding-bottom: 70px; }
        @media (min-width: 992px) { body { padding-bottom: 0; } }
        .navbar-brand { font-weight: 700; color: var(--primary) !important; font-size: 1.5rem; }
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }
        .text-primary { color: var(--primary) !important; }
        .product-card { transition: transform .2s, box-shadow .2s; border: 1px solid #eee; }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }
        .product-card .badge-discount { position: absolute; top: 8px; left: 8px; background: var(--primary); }
        .price-sale { color: var(--primary); font-weight: 700; }
        .price-original { text-decoration: line-through; color: #999; font-size: .85em; }
        .mobile-nav { display: flex; position: fixed; bottom: 0; left: 0; right: 0; background: #fff; border-top: 1px solid #eee; z-index: 1000; padding: 8px 0; }
        .mobile-nav a { flex: 1; text-align: center; color: #555; font-size: .7rem; text-decoration: none; }
        .mobile-nav a.active, .mobile-nav a:hover { color: var(--primary); }
        .mobile-nav i { display: block; font-size: 1.3rem; }
        @media (min-width: 992px) { .mobile-nav { display: none; } }
        .cart-badge { position: relative; }
        .cart-badge .badge { position: absolute; top: -8px; right: -8px; font-size: .65rem; padding: 2px 5px; }
        .track-order-link { display: inline-flex; flex-direction: column; align-items: center; gap: 1px; color: #0f172a; font-size: .72rem; line-height: 1.05; text-decoration: none; white-space: nowrap; }
        .track-order-link i { font-size: 1.25rem; line-height: 1; color: #0f172a; }
        .track-order-link:hover, .track-order-link:hover i { color: var(--primary); }
        .shop-secondary-nav { position: sticky; top: var(--shop-header-height, 72px); z-index: 998; background: #fff; border-bottom: 1px solid #edf0f3; box-shadow: 0 4px 14px rgba(15,23,42,.04); transition: transform .28s ease, opacity .28s ease; will-change: transform; }
        .shop-secondary-nav.nav-hidden { transform: translateY(-100%); opacity: 0; pointer-events: none; }
        .shop-secondary-nav .nav-link { color: #1f2937; font-weight: 600; font-size: .92rem; padding: .82rem .8rem; transition: color .2s ease, background-color .2s ease; }
        .shop-secondary-nav .nav-link:hover, .shop-secondary-nav .nav-link.active { color: var(--primary); }
        .shop-secondary-nav .dropdown-menu { border: 1px solid #edf0f3; box-shadow: 0 16px 36px rgba(15,23,42,.12); }
        .shop-category-menu { width: min(760px, calc(100vw - 32px)); max-height: 420px; overflow-y: auto; padding: 1rem; }
        .shop-category-group { break-inside: avoid; margin-bottom: .85rem; }
        .shop-category-parent { display: block; color: #111827; font-weight: 700; font-size: .9rem; text-decoration: none; margin-bottom: .35rem; }
        .shop-category-child { display: block; color: #6b7280; font-size: .84rem; text-decoration: none; padding: .14rem 0; }
        .shop-category-parent:hover, .shop-category-child:hover { color: var(--primary); }
        .shop-mobile-menu { border-top: 1px solid #f1f3f5; }
        .shop-mobile-menu .nav-link { padding: .65rem 0; }
        .shop-mobile-quick { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .55rem; padding: .75rem 0 1rem; }
        .shop-mobile-quick a { border: 1px solid #eef0f3; border-radius: 6px; padding: .55rem .65rem; color: #1f2937; text-decoration: none; font-size: .86rem; }
        .shop-mobile-quick a:hover { color: var(--primary); border-color: rgba(233,30,99,.3); }
        @media (min-width: 992px) {
            .shop-mobile-menu { display: none !important; }
            .shop-secondary-nav .navbar-toggler { display: none; }
        }
        .toast-container { position: fixed; top: 1rem; right: 1rem; z-index: 9999; }
        .section-title { font-weight: 700; position: relative; padding-bottom: .5rem; margin-bottom: 1.5rem; }
        .section-title::after { content: ''; position: absolute; bottom: 0; left: 0; width: 50px; height: 3px; background: var(--primary); }
        .site-footer { background: #20252a; color: #f8fafc; }
        .site-footer h5,
        .site-footer h6 { color: #fff; }
        .site-footer p,
        .site-footer .text-muted,
        .site-footer a.text-muted { color: #d5dbe3 !important; }
        .site-footer a { transition: color .2s ease, padding-left .2s ease; }
        .site-footer a:hover { color: #fff !important; padding-left: 3px; }
        .site-footer .form-control { border-color: #fff; }
        .site-footer .border-secondary { border-color: rgba(255,255,255,.18) !important; }
    </style>
    @stack('styles')
</head>
<body>

{{-- Top Navigation --}}
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">{{ $siteName ?? 'ShopGram' }}</a>

        {{-- Search --}}
        <form class="d-none d-lg-flex flex-grow-1 mx-4" action="{{ route('search.index') }}" method="GET">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search products..." value="{{ request('q') }}" autocomplete="off" id="search-input">
                <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
            </div>
        </form>

        {{-- Nav Icons --}}
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('order.tracking') }}" class="track-order-link">
                <i class="bi bi-geo-alt"></i>
                <span>Track Order</span>
            </a>
            <a href="{{ route('cart.index') }}" class="text-dark cart-badge">
                <i class="bi bi-cart3 fs-5"></i>
                @if($cartCount > 0)<span class="badge bg-danger rounded-pill">{{ $cartCount }}</span>@endif
            </a>
            <a href="{{ auth()->check() ? route('customer.wishlist.index') : route('login') }}" class="text-dark cart-badge">
                <i class="bi bi-heart fs-5"></i>
                @if($wishlistCount > 0)<span class="badge bg-danger rounded-pill">{{ $wishlistCount }}</span>@endif
            </a>
            @auth
            <div class="dropdown">
                <a class="text-dark dropdown-toggle" href="#" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle fs-5"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}">Dashboard</a></li>
                    <li><a class="dropdown-item" href="{{ route('customer.orders.index') }}">My Orders</a></li>
                    <li><a class="dropdown-item" href="{{ route('customer.profile.edit') }}">Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="dropdown-item text-danger">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
            @else
            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary">Login</a>
            @endauth

            <button class="navbar-toggler border-0 d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </div>

    {{-- Mobile Search --}}
    <div class="container d-lg-none mt-2">
        <form action="{{ route('search.index') }}" method="GET">
            <div class="input-group">
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Search..." value="{{ request('q') }}">
                <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>
</nav>

{{-- Secondary Navigation --}}
<nav class="shop-secondary-nav" id="shopSecondaryNav">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between">
            <button class="navbar-toggler border-0 px-0 d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#shopNavMenu" aria-controls="shopNavMenu" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-4"></i>
            </button>

            <div class="d-none d-lg-flex align-items-center gap-1">
                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('category.*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">Categories</a>
                    <div class="dropdown-menu shop-category-menu">
                        <div class="row g-3">
                            @foreach(($navCategories ?? collect()) as $navCategory)
                                <div class="col-md-4 shop-category-group">
                                    <a class="shop-category-parent" href="{{ route('category.show', $navCategory->slug) }}">{{ $navCategory->name }}</a>
                                    @foreach($navCategory->children as $childCategory)
                                        <a class="shop-category-child" href="{{ route('category.show', $childCategory->slug) }}">{{ $childCategory->name }}</a>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">Shop</a>
                <a class="nav-link" href="{{ route('home') }}#offers">Offers</a>
                <a class="nav-link" href="{{ route('home') }}#new-arrivals">New Arrivals</a>
                <a class="nav-link" href="{{ route('home') }}#best-sellers">Best Sellers</a>
                <a class="nav-link" href="{{ route('home') }}#brands">Brands</a>
                <a class="nav-link {{ request()->routeIs('contact.*') ? 'active' : '' }}" href="{{ route('contact.index') }}">Contact</a>
            </div>
        </div>

        <div class="collapse shop-mobile-menu" id="shopNavMenu">
            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
            <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">Shop</a>
            <a class="nav-link" href="{{ route('home') }}#offers">Offers</a>
            <a class="nav-link" href="{{ route('home') }}#new-arrivals">New Arrivals</a>
            <a class="nav-link" href="{{ route('home') }}#best-sellers">Best Sellers</a>
            <a class="nav-link" href="{{ route('home') }}#brands">Brands</a>
            <a class="nav-link {{ request()->routeIs('contact.*') ? 'active' : '' }}" href="{{ route('contact.index') }}">Contact</a>
            <div class="dropdown">
                <a class="nav-link dropdown-toggle {{ request()->routeIs('category.*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">Categories</a>
                <div class="dropdown-menu shop-category-menu">
                    @foreach(($navCategories ?? collect()) as $navCategory)
                        <div class="shop-category-group">
                            <a class="shop-category-parent" href="{{ route('category.show', $navCategory->slug) }}">{{ $navCategory->name }}</a>
                            @foreach($navCategory->children as $childCategory)
                                <a class="shop-category-child" href="{{ route('category.show', $childCategory->slug) }}">{{ $childCategory->name }}</a>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="shop-mobile-quick">
                <a href="{{ route('order.tracking') }}"><i class="bi bi-geo-alt me-1"></i>Track Order</a>
                <a href="{{ route('cart.index') }}"><i class="bi bi-cart3 me-1"></i>Cart</a>
                <a href="{{ auth()->check() ? route('customer.wishlist.index') : route('login') }}"><i class="bi bi-heart me-1"></i>Wishlist</a>
                <a href="{{ auth()->check() ? route('customer.dashboard') : route('login') }}"><i class="bi bi-person me-1"></i>Account</a>
            </div>
        </div>
    </div>
</nav>

{{-- Flash Messages --}}
<x-toast />

{{-- Main Content --}}
<main>
    @yield('content')
</main>

{{-- Footer --}}
<footer class="site-footer py-5 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3">
                <h5 class="fw-bold">{{ $siteName ?? 'ShopGram' }}</h5>
                <p class="text-muted small">Your trusted online shopping destination in Bangladesh.</p>
            </div>
            <div class="col-lg-3">
                <h6 class="fw-bold">Quick Links</h6>
                <ul class="list-unstyled small">
                    <li><a href="{{ route('home') }}" class="text-muted text-decoration-none">Home</a></li>
                    <li><a href="{{ route('products.index') }}" class="text-muted text-decoration-none">Products</a></li>
                    <li><a href="{{ route('contact.index') }}" class="text-muted text-decoration-none">Contact Us</a></li>
                </ul>
            </div>
            <div class="col-lg-3">
                <h6 class="fw-bold">Policies</h6>
                <ul class="list-unstyled small">
                    <li><a href="{{ route('page.show', 'privacy-policy') }}" class="text-muted text-decoration-none">Privacy Policy</a></li>
                    <li><a href="{{ route('page.show', 'return-policy') }}" class="text-muted text-decoration-none">Return Policy</a></li>
                    <li><a href="{{ route('page.show', 'terms-and-conditions') }}" class="text-muted text-decoration-none">Terms & Conditions</a></li>
                </ul>
            </div>
            <div class="col-lg-3">
                <h6 class="fw-bold">Newsletter</h6>
                <form action="{{ route('newsletter.subscribe') }}" method="POST">
                    @csrf
                    <div class="input-group input-group-sm">
                        <input type="email" name="email" class="form-control" placeholder="Your email">
                        <button class="btn btn-primary">Subscribe</button>
                    </div>
                </form>
            </div>
        </div>
        <hr class="border-secondary">
        <p class="text-center text-muted small mb-0">&copy; {{ date('Y') }} {{ $siteName ?? 'ShopGram' }}. All rights reserved.</p>
    </div>
</footer>

{{-- Mobile Bottom Nav --}}
<nav class="mobile-nav d-lg-none">
    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
        <i class="bi bi-house"></i>Home
    </a>
    <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
        <i class="bi bi-grid"></i>Shop
    </a>
    <a href="{{ route('cart.index') }}" class="{{ request()->routeIs('cart.*') ? 'active' : '' }} position-relative">
        <i class="bi bi-cart3"></i>Cart
        @if($cartCount > 0)<span class="badge bg-danger rounded-pill" style="position:absolute;top:0;right:20%">{{ $cartCount }}</span>@endif
    </a>
    <a href="{{ auth()->check() ? route('customer.wishlist.index') : route('login') }}">
        <i class="bi bi-heart"></i>Wishlist
    </a>
    <a href="{{ auth()->check() ? route('customer.dashboard') : route('login') }}" class="{{ request()->routeIs('customer.*') ? 'active' : '' }}">
        <i class="bi bi-person"></i>Account
    </a>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<x-confirm-delete />
<script>
document.addEventListener('DOMContentLoaded', function () {
    const topHeader = document.querySelector('.navbar.sticky-top');
    const secondaryNav = document.getElementById('shopSecondaryNav');
    if (!topHeader || !secondaryNav) {
        return;
    }

    let lastScrollY = window.scrollY;
    const threshold = 8;

    const updateHeaderOffset = function () {
        document.documentElement.style.setProperty('--shop-header-height', topHeader.offsetHeight + 'px');
    };

    const handleScroll = function () {
        const currentScrollY = window.scrollY;
        const delta = currentScrollY - lastScrollY;

        if (Math.abs(delta) < threshold) {
            return;
        }

        if (currentScrollY <= topHeader.offsetHeight) {
            secondaryNav.classList.remove('nav-hidden');
        } else if (delta > 0) {
            secondaryNav.classList.add('nav-hidden');
        } else {
            secondaryNav.classList.remove('nav-hidden');
        }

        lastScrollY = currentScrollY;
    };

    updateHeaderOffset();
    window.addEventListener('resize', updateHeaderOffset);
    window.addEventListener('scroll', handleScroll, { passive: true });
});
</script>
@stack('scripts')
</body>
</html>
