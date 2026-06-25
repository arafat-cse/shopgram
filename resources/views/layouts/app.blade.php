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

{{-- First-visit promotional popup --}}
<div class="modal fade" id="promoPopupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:860px;">
        <div class="modal-content border-0 overflow-hidden" style="border-radius:20px;">

            <div id="promoLoaded">

                {{-- Full-bleed image card — banner style --}}
                <div class="position-relative" id="promoCard" style="cursor:pointer; user-select:none;">

                    {{-- Image / placeholder --}}
                    <img id="promoImg" src="" alt="" loading="lazy"
                         style="width:100%; height:540px; object-fit:cover; display:block;">
                    <div id="promoImgPh"
                         style="width:100%; height:540px; display:none; align-items:center; justify-content:center;
                                background:linear-gradient(135deg,#1a0533 0%,#6b0f6b 50%,#c2185b 100%); font-size:6rem;">
                        🛍️
                    </div>

                    {{-- Dark gradient overlay --}}
                    <div style="position:absolute; inset:0;
                                background:linear-gradient(to top, rgba(0,0,0,.82) 0%, rgba(0,0,0,.28) 45%, rgba(0,0,0,.08) 100%);
                                pointer-events:none;"></div>

                    {{-- Close X —top right --}}
                    <button data-bs-dismiss="modal"
                            style="position:absolute; top:14px; right:14px; width:34px; height:34px;
                                   border-radius:50%; border:0; background:rgba(0,0,0,.45);
                                   color:#fff; font-size:1.1rem; line-height:1; cursor:pointer;
                                   display:flex; align-items:center; justify-content:center;
                                   backdrop-filter:blur(4px); transition:background .15s; z-index:10;"
                            onmouseover="this.style.background='rgba(0,0,0,.75)'"
                            onmouseout="this.style.background='rgba(0,0,0,.45)'">&times;</button>

                    {{-- Discount badge — top left --}}
                    <div id="promoDiscBadge"
                         style="display:none; position:absolute; top:14px; left:14px;
                                background:linear-gradient(135deg,#e91e63,#ad1457);
                                color:#fff; font-weight:800; font-size:1.05rem;
                                padding:6px 14px; border-radius:999px;
                                box-shadow:0 4px 16px rgba(233,30,99,.5); z-index:10;"></div>

                    {{-- Progress bar --}}
                    <div style="position:absolute; top:0; left:0; right:0; height:3px; background:rgba(255,255,255,.2); z-index:10;">
                        <div id="promoProgress" style="height:100%; width:0%;
                             background:linear-gradient(90deg,#e91e63,#f5821f);
                             transition:width .12s linear;"></div>
                    </div>

                    {{-- Bottom overlay content --}}
                    <div style="position:absolute; bottom:0; left:0; right:0; padding:28px 24px 20px; color:#fff; z-index:5;">

                        {{-- Eyebrow --}}
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                            <span style="background:rgba(233,30,99,.85); backdrop-filter:blur(4px);
                                         font-size:.72rem; font-weight:700; letter-spacing:.06em;
                                         text-transform:uppercase; padding:4px 12px; border-radius:999px;">
                                ⚡ Special Offer
                            </span>
                            <span id="promoSavePill" style="display:none; background:rgba(56,142,60,.85);
                                  backdrop-filter:blur(4px); font-size:.72rem; font-weight:700;
                                  letter-spacing:.04em; text-transform:uppercase; padding:4px 12px; border-radius:999px;"></span>
                        </div>

                        {{-- Product name --}}
                        <div id="promoName"
                             style="font-size:clamp(1.2rem,3.5vw,1.6rem); font-weight:800;
                                    line-height:1.2; margin-bottom:10px;
                                    text-shadow:0 2px 12px rgba(0,0,0,.5);"></div>

                        {{-- Price row --}}
                        <div style="display:flex; align-items:baseline; gap:10px; margin-bottom:16px;">
                            <span id="promoPrice"
                                  style="font-size:clamp(1.5rem,4vw,2rem); font-weight:900; color:#fff; line-height:1;"></span>
                            <span id="promoOldPrice"
                                  style="display:none; font-size:1.05rem; color:rgba(255,255,255,.6); text-decoration:line-through;"></span>
                            <span id="promoSaveAmt"
                                  style="display:none; font-size:.82rem; background:rgba(255,255,255,.15);
                                         backdrop-filter:blur(3px); padding:3px 10px; border-radius:999px; font-weight:600;"></span>
                        </div>

                        {{-- CTA button + dots row --}}
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
                            <a id="promoCta" href="#"
                               style="display:inline-flex; align-items:center; gap:8px;
                                      padding:11px 28px; border-radius:12px;
                                      background:linear-gradient(135deg,#e91e63,#c2185b);
                                      color:#fff; font-size:1rem; font-weight:700;
                                      text-decoration:none; box-shadow:0 6px 22px rgba(233,30,99,.5);
                                      transition:transform .15s, box-shadow .15s; white-space:nowrap;"
                               onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 10px 30px rgba(233,30,99,.6)'"
                               onmouseout="this.style.transform='';this.style.boxShadow='0 6px 22px rgba(233,30,99,.5)'">
                                Shop Now &rarr;
                            </a>

                            {{-- Dot navigation --}}
                            <div id="promoDots" style="display:flex; gap:6px; align-items:center;"></div>
                        </div>

                        {{-- Skip link --}}
                        <div style="margin-top:12px;">
                            <button data-bs-dismiss="modal"
                                    style="border:0; background:none; color:rgba(255,255,255,.55);
                                           font-size:.78rem; cursor:pointer; padding:0; letter-spacing:.02em;">
                                Skip for now &times;
                            </button>
                        </div>
                    </div>

                    {{-- Side arrows --}}
                    <button onclick="promoGo(-1)"
                            style="position:absolute; left:12px; top:50%; transform:translateY(-50%);
                                   width:36px; height:36px; border-radius:50%; border:0;
                                   background:rgba(255,255,255,.2); backdrop-filter:blur(4px);
                                   color:#fff; font-size:1rem; cursor:pointer;
                                   display:flex; align-items:center; justify-content:center;
                                   transition:background .15s; z-index:10;"
                            onmouseover="this.style.background='rgba(255,255,255,.4)'"
                            onmouseout="this.style.background='rgba(255,255,255,.2)'">&#8592;</button>
                    <button onclick="promoGo(1)"
                            style="position:absolute; right:12px; top:50%; transform:translateY(-50%);
                                   width:36px; height:36px; border-radius:50%; border:0;
                                   background:rgba(255,255,255,.2); backdrop-filter:blur(4px);
                                   color:#fff; font-size:1rem; cursor:pointer;
                                   display:flex; align-items:center; justify-content:center;
                                   transition:background .15s; z-index:10;"
                            onmouseover="this.style.background='rgba(255,255,255,.4)'"
                            onmouseout="this.style.background='rgba(255,255,255,.2)'">&#8594;</button>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
#promoImgPh { display: flex; }
#promoImg[src=""] + #promoImgPh { display: flex; }
.promo-dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: rgba(255,255,255,.45); cursor: pointer;
    transition: background .2s, transform .2s; border: 0;
}
.promo-dot.active { background: #e91e63; transform: scale(1.4); }
</style>

<script>
(function () {
    const STORAGE_KEY = 'sg_promo_seen_v1';
    if (localStorage.getItem(STORAGE_KEY)) return;

    let slides = [], current = 0, autoTimer = null, progTimer = null, progVal = 0;
    const AUTO_MS = 5000;

    function fmt(n) { return '৳' + parseFloat(n).toLocaleString('en-BD'); }

    function render(idx) {
        current = ((idx % slides.length) + slides.length) % slides.length;
        const p = slides[current];

        // Image
        const img = document.getElementById('promoImg');
        const ph  = document.getElementById('promoImgPh');
        if (p.thumbnail) {
            img.src = p.thumbnail; img.alt = p.name;
            img.style.display = 'block'; ph.style.display = 'none';
        } else {
            img.style.display = 'none'; ph.style.display = 'flex';
        }

        // Discount badge top-left
        const badge = document.getElementById('promoDiscBadge');
        if (p.sale_price && p.regular_price > 0) {
            const pct = Math.round(((p.regular_price - p.sale_price) / p.regular_price) * 100);
            badge.textContent = '-' + pct + '%';
            badge.style.display = 'block';
        } else { badge.style.display = 'none'; }

        // Name
        document.getElementById('promoName').textContent = p.name;

        // Price
        document.getElementById('promoPrice').textContent = fmt(p.sale_price || p.regular_price);
        const oldEl  = document.getElementById('promoOldPrice');
        const saveEl = document.getElementById('promoSaveAmt');
        const savePill = document.getElementById('promoSavePill');
        if (p.sale_price && p.regular_price > p.sale_price) {
            const saved = p.regular_price - p.sale_price;
            const pct   = Math.round((saved / p.regular_price) * 100);
            oldEl.textContent = fmt(p.regular_price); oldEl.style.display = 'inline';
            saveEl.textContent = 'Save ' + pct + '%'; saveEl.style.display = 'inline';
            savePill.textContent = '🎉 Save ' + fmt(saved); savePill.style.display = 'inline';
        } else {
            oldEl.style.display = 'none';
            saveEl.style.display = 'none';
            savePill.style.display = 'none';
        }

        // CTA
        document.getElementById('promoCta').href = p.url;

        // Dots
        const dotsEl = document.getElementById('promoDots');
        dotsEl.innerHTML = '';
        slides.forEach((_, i) => {
            const d = document.createElement('button');
            d.className = 'promo-dot' + (i === current ? ' active' : '');
            d.addEventListener('click', (e) => { e.stopPropagation(); resetAuto(); render(i); });
            dotsEl.appendChild(d);
        });

        // Progress bar
        clearInterval(progTimer); progVal = 0;
        const bar = document.getElementById('promoProgress');
        bar.style.transition = 'none'; bar.style.width = '0%';
        setTimeout(() => {
            bar.style.transition = 'width .12s linear';
            progTimer = setInterval(() => {
                progVal += 100 / (AUTO_MS / 120);
                bar.style.width = Math.min(progVal, 100) + '%';
            }, 120);
        }, 50);
    }

    function resetAuto() {
        clearInterval(autoTimer);
        autoTimer = setInterval(() => render(current + 1), AUTO_MS);
    }

    window.promoGo = function(dir) { resetAuto(); render(current + dir); };

    fetch('/api/promoted-products')
        .then(r => r.json())
        .then(data => {
            if (!data.length) return;
            slides = data;
            render(0);
            resetAuto();
            const modal = new bootstrap.Modal(document.getElementById('promoPopupModal'), { backdrop: true });
            modal.show();
            document.getElementById('promoPopupModal').addEventListener('hidden.bs.modal', () => {
                clearInterval(autoTimer); clearInterval(progTimer);
                localStorage.setItem(STORAGE_KEY, '1');
            });
        })
        .catch(() => {});
}());
</script>
</body>
</html>
