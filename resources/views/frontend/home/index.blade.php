@extends('layouts.app')
@section('title', 'ShopGram - Online Shopping Bangladesh')

@push('styles')
<style>
    :root {
        --sg-orange: #f5821f;
        --sg-orange-dark: #e56f0d;
        --sg-soft: #f8f5f1;
        --sg-ink: #101828;
        --sg-muted: #667085;
    }

    body { background: var(--sg-soft); }
    .navbar, footer { background: #fff !important; }
    footer { color: var(--sg-ink) !important; border-top: 1px solid #ebe5dd; }
    footer .text-muted, footer a.text-muted { color: #6b7280 !important; }
    footer .border-secondary { border-color: #e7ded4 !important; }

    .home-shell {
        background: var(--sg-soft);
        color: var(--sg-ink);
        padding: 18px 0 38px;
    }

    .home-wrap {
        width: min(1710px, calc(100% - 48px));
        margin: 0 auto;
    }

    .hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(320px, 1fr);
        gap: 20px;
        align-items: stretch;
    }

    .hero-card {
        position: relative;
        min-height: 394px;
        overflow: hidden;
        border-radius: 6px;
        background: #1f2719;
    }

    .hero-card img {
        width: 100%;
        height: 100%;
        min-height: 394px;
        object-fit: cover;
        display: block;
    }

    .hero-fallback {
        min-height: 394px;
        height: 100%;
        background:
            radial-gradient(circle at 76% 52%, rgba(255,255,255,.9) 0 4%, transparent 4.3%),
            radial-gradient(circle at 68% 53%, rgba(252,247,221,.95) 0 5.5%, transparent 5.8%),
            radial-gradient(circle at 59% 54%, rgba(251,188,5,.85) 0 4.8%, transparent 5.1%),
            linear-gradient(180deg, rgba(179,217,240,.92) 0%, rgba(209,233,236,.75) 38%, rgba(217,222,190,.85) 60%, rgba(218,180,127,.95) 100%);
    }

    .promo-fallback {
        min-height: 394px;
        height: 100%;
        background:
            radial-gradient(ellipse at 30% 22%, rgba(185, 216, 95, .96) 0 9%, transparent 9.4%),
            radial-gradient(ellipse at 47% 24%, rgba(218, 229, 130, .96) 0 8%, transparent 8.4%),
            radial-gradient(ellipse at 64% 19%, rgba(164, 205, 76, .95) 0 9%, transparent 9.4%),
            linear-gradient(120deg, rgba(12,17,11,.5), rgba(13,19,12,.88)),
            url("{{ asset('images/no-image.png') }}");
        background-color: #17200f;
        background-size: auto, auto, auto, cover, 72px;
        background-position: center;
    }

    .hero-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 42px;
        text-align: center;
        color: #fff;
        background: linear-gradient(90deg, rgba(0,0,0,.08), rgba(0,0,0,.28));
        pointer-events: none;
    }

    .hero-copy {
        max-width: 650px;
        margin-left: auto;
        text-shadow: 0 2px 12px rgba(0,0,0,.35);
    }

    .hero-copy .eyebrow,
    .promo-copy .eyebrow {
        font-size: clamp(1.15rem, 2vw, 2rem);
        line-height: 1.1;
        font-weight: 500;
    }

    .hero-copy h1,
    .promo-copy h2 {
        margin: 0;
        font-size: clamp(2.25rem, 5vw, 5.2rem);
        line-height: .98;
        font-weight: 800;
    }

    .hero-copy .hero-cta,
    .promo-copy .hero-cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 250px;
        margin-top: 18px;
        padding: 10px 30px 13px;
        border: 2px solid rgba(220, 219, 149, .7);
        border-radius: 12px;
        background: rgba(17, 18, 10, .76);
        color: #fff;
        font-size: clamp(1.25rem, 2.5vw, 2.4rem);
        line-height: 1;
        font-weight: 700;
    }

    .promo-card .hero-overlay {
        justify-content: flex-end;
        background: linear-gradient(90deg, rgba(0,0,0,.05), rgba(0,0,0,.55));
    }

    .promo-copy {
        max-width: 340px;
        margin-left: auto;
        text-align: right;
        text-shadow: 0 2px 12px rgba(0,0,0,.45);
    }

    .promo-copy h2 { font-size: clamp(1.95rem, 3vw, 3.25rem); }
    .promo-copy .eyebrow { font-size: clamp(1rem, 1.5vw, 1.55rem); }
    .promo-copy .hero-cta { min-width: 180px; font-size: clamp(1rem, 1.5vw, 1.45rem); padding: 9px 20px 11px; }

    .carousel-indicators {
        right: auto;
        left: 24px;
        bottom: 18px;
        margin: 0;
        justify-content: flex-start;
    }

    .carousel-indicators [data-bs-target] {
        width: 9px;
        height: 9px;
        border: 0;
        border-radius: 999px;
        background: #fff;
        opacity: .95;
    }

    .carousel-indicators .active { background: var(--sg-orange); }

    .hero-control {
        width: 40px;
        height: 40px;
        top: 50%;
        transform: translateY(-50%);
        background: #fff;
        color: var(--sg-orange);
        opacity: 1;
    }

    .hero-control:hover { color: var(--sg-orange-dark); background: #fff; }
    .hero-control.carousel-control-prev { left: 0; }
    .hero-control.carousel-control-next { right: 0; }

    .section-heading {
        margin: 22px 0 28px;
        text-align: center;
        font-size: clamp(1.4rem, 2vw, 1.9rem);
        font-weight: 500;
    }

    .category-section { position: relative; padding: 0 28px 30px; }
    .category-slider {
        overflow: hidden;
        scroll-behavior: smooth;
    }

    .category-strip {
        display: flex;
        gap: 24px;
        align-items: start;
        overflow-x: auto;
        scroll-behavior: smooth;
        scroll-snap-type: x mandatory;
        scrollbar-width: none;
        padding: 0 2px 4px;
    }

    .category-strip::-webkit-scrollbar {
        display: none;
    }

    .category-item {
        flex: 0 0 calc((100% - 168px) / 8);
        min-width: 118px;
        display: block;
        color: #2b3138;
        text-align: center;
        text-decoration: none;
        scroll-snap-align: start;
    }

    .category-image {
        width: 144px;
        max-width: 100%;
        aspect-ratio: 1;
        margin: 0 auto 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 1px 0 rgba(16,24,40,.02);
    }

    .category-image img {
        width: 82%;
        height: 82%;
        object-fit: contain;
    }

    .category-icon {
        width: 76%;
        height: 76%;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #fff7e8, #fefefe);
        color: var(--sg-orange);
        font-size: 3.2rem;
    }

    .category-item span {
        display: block;
        font-size: 1rem;
        font-weight: 400;
    }

    .side-arrow {
        position: absolute;
        top: 51%;
        width: 36px;
        height: 36px;
        border: 0;
        border-radius: 50%;
        background: var(--sg-orange);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transform: translateY(-50%);
        cursor: pointer;
        z-index: 2;
        transition: background .2s, opacity .2s, transform .2s;
    }

    .side-arrow:hover { background: var(--sg-orange-dark); }
    .side-arrow.left { left: -4px; }
    .side-arrow.right { right: -4px; background: #f8c99d; }
    .side-arrow.right:hover { background: var(--sg-orange); }

    .products-section {
        background: #fff;
        padding: 18px 0 24px;
        margin: 0 calc((100vw - 100%) / -2);
    }

    .products-section .home-wrap { width: min(1640px, calc(100% - 96px)); }
    .products-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .selling-card {
        position: relative;
        display: grid;
        grid-template-columns: 240px minmax(0, 1fr);
        column-gap: 38px;
        align-items: center;
        min-height: 250px;
        padding: 28px 36px;
        border-radius: 6px;
        background: #fff;
        color: var(--sg-ink);
        text-decoration: none;
        box-shadow: 0 0 0 1px rgba(16,24,40,.02);
        overflow: hidden;
    }

    .selling-card:hover { color: var(--sg-ink); box-shadow: 0 8px 30px rgba(16,24,40,.07); }

    .selling-image {
        width: 240px;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: #fff;
    }

    .selling-image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
    }

    .selling-info {
        min-width: 0;
        padding-right: 4px;
    }

    .selling-info h3 {
        margin-bottom: 8px;
        font-size: clamp(1.05rem, 1.25vw, 1.32rem);
        line-height: 1.25;
        font-weight: 600;
        word-break: normal;
        overflow-wrap: break-word;
    }

    .price-line {
        display: flex;
        align-items: baseline;
        gap: 16px;
        margin-bottom: 4px;
    }

    .home-price {
        color: #ff7200;
        font-size: 1.22rem;
        font-weight: 600;
    }

    .home-old-price {
        color: #8a8f98;
        font-size: 1.1rem;
        text-decoration: line-through;
    }

    .save-badge {
        display: inline-flex;
        margin-bottom: 28px;
        padding: 3px 10px;
        border-radius: 999px;
        background: #a7e126;
        color: #111;
        font-size: .82rem;
        font-weight: 600;
    }

    .selling-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .selling-actions form { margin: 0; }
    .sg-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        height: 37px;
        padding: 0 15px;
        border-radius: 5px;
        border: 1px solid var(--sg-orange);
        background: #fff;
        color: #ff7200;
        font-size: .86rem;
        font-weight: 500;
        text-decoration: none;
        white-space: nowrap;
    }

    .sg-btn.primary {
        background: var(--sg-orange);
        color: #fff;
    }

    .brands-section { padding: 28px 0 8px; }
    .brand-slider-wrap {
        position: relative;
        padding: 0 28px;
    }

    .brand-slider {
        overflow: hidden;
    }

    .brand-head {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
        border-bottom: 1px solid #ddd6ce;
    }

    .brand-title {
        position: relative;
        margin: 0;
        padding-bottom: 12px;
        font-size: 1.25rem;
        font-weight: 700;
    }

    .brand-title::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: -1px;
        width: 38px;
        height: 3px;
        background: var(--sg-orange);
    }

    .see-all {
        margin-bottom: 12px;
        color: #ff7200;
        font-size: .78rem;
        font-weight: 600;
        text-decoration: none;
        letter-spacing: .02em;
    }

    .brand-grid {
        display: flex;
        gap: 22px;
        overflow-x: auto;
        scroll-behavior: smooth;
        scroll-snap-type: x mandatory;
        scrollbar-width: none;
        padding: 0 2px 2px;
    }

    .brand-grid::-webkit-scrollbar {
        display: none;
    }

    .brand-card {
        flex: 0 0 calc((100% - 66px) / 4);
        min-height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e4ded7;
        border-radius: 5px;
        background: #fff;
        color: #1f2937;
        text-decoration: none;
        font-size: 1.45rem;
        font-weight: 800;
        scroll-snap-align: start;
    }

    .brand-card img {
        max-width: 150px;
        max-height: 48px;
        object-fit: contain;
    }

    .brand-arrow {
        position: absolute;
        top: 50%;
        width: 32px;
        height: 32px;
        border: 0;
        border-radius: 50%;
        background: var(--sg-orange);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transform: translateY(-50%);
        cursor: pointer;
        z-index: 2;
        transition: background .2s;
    }

    .brand-arrow:hover { background: var(--sg-orange-dark); }
    .brand-arrow.left { left: -4px; }
    .brand-arrow.right { right: -4px; background: #f8c99d; }
    .brand-arrow.right:hover { background: var(--sg-orange); }

    .legacy-sections {
        padding: 30px 0 0;
    }

    .legacy-head {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
    }

    .legacy-title {
        position: relative;
        margin: 0;
        padding-bottom: 10px;
        font-size: 1.35rem;
        font-weight: 700;
    }

    .legacy-title::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: 0;
        width: 44px;
        height: 3px;
        background: var(--sg-orange);
    }

    @media (max-width: 1199.98px) {
        .hero-grid { grid-template-columns: 1fr; }
        .promo-card { display: none; }
        .category-item { flex-basis: calc((100% - 88px) / 5); }
        .category-strip { gap: 22px; }
        .products-section .home-wrap { width: min(960px, calc(100% - 32px)); }
        .selling-card {
            grid-template-columns: 200px minmax(0, 1fr);
            column-gap: 24px;
            padding: 24px;
        }
        .selling-image { width: 200px; height: 180px; }
    }

    @media (max-width: 991.98px) {
        .home-wrap { width: min(100% - 28px, 760px); }
        .products-grid { grid-template-columns: 1fr; }
        .brand-grid { gap: 12px; }
        .brand-card { flex-basis: calc((100% - 12px) / 2); }
    }

    @media (max-width: 575.98px) {
        .home-shell { padding-top: 12px; }
        .hero-card, .hero-card img, .hero-fallback, .promo-fallback { min-height: 255px; }
        .hero-overlay { padding: 24px; justify-content: flex-end; }
        .hero-copy h1 { font-size: 2.2rem; }
        .hero-copy .eyebrow { font-size: 1.05rem; }
        .hero-copy .hero-cta { min-width: 160px; font-size: 1.1rem; }
        .category-section { padding-left: 20px; padding-right: 20px; }
        .category-strip { gap: 14px; }
        .category-item { flex-basis: calc((100% - 14px) / 2); min-width: 132px; }
        .side-arrow { width: 32px; height: 32px; }
        .selling-card {
            grid-template-columns: 112px minmax(0, 1fr);
            min-height: 190px;
            gap: 10px;
            padding: 18px 14px;
        }
        .selling-image { width: 112px; height: 150px; }
        .sg-btn { height: 34px; padding: 0 10px; font-size: .78rem; }
        .brand-slider-wrap { padding: 0 20px; }
        .brand-card { flex-basis: 100%; }
    }
</style>
@endpush

@section('content')
@php
    $heroSlides = $banners->count() ? $banners : collect([null, null, null]);
    $promoBanner = $promoBanners->first();
    $categoryIcons = ['bi-droplet-fill', 'bi-phone', 'bi-laptop', 'bi-bag', 'bi-house-heart', 'bi-dribbble', 'bi-book', 'bi-stars'];
@endphp

<div class="home-shell">
    <div class="home-wrap">
        <section class="hero-grid" aria-label="Promotions">
            <div id="homeHeroSlider" class="carousel slide hero-card" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    @foreach($heroSlides as $index => $banner)
                        <button type="button" data-bs-target="#homeHeroSlider" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}" aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>

                <div class="carousel-inner h-100">
                    @foreach($heroSlides as $index => $banner)
                        <div class="carousel-item h-100 {{ $index === 0 ? 'active' : '' }}">
                            @if($banner && $banner->image)
                                <img src="{{ asset('storage/'.$banner->image) }}" alt="{{ $banner->title ?? 'ShopGram offer' }}">
                            @else
                                <div class="hero-fallback"></div>
                            @endif

                            <div class="hero-overlay">
                                <div class="hero-copy">
                                    <div class="eyebrow">{{ $banner->subtitle ?? 'সরাসরি প্রকৃতি থেকে' }}</div>
                                    <h1>{{ $banner->title ?? 'আপনার ঘরে' }}</h1>
                                    <span class="hero-cta">{{ $banner->button_text ?? 'অর্ডার চলছে' }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button class="carousel-control-prev hero-control" type="button" data-bs-target="#homeHeroSlider" data-bs-slide="prev" aria-label="Previous slide">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <button class="carousel-control-next hero-control" type="button" data-bs-target="#homeHeroSlider" data-bs-slide="next" aria-label="Next slide">
                    <i class="bi bi-arrow-right"></i>
                </button>
            </div>

            <a class="hero-card promo-card text-decoration-none" href="{{ $promoBanner?->button_url ?: route('products.index') }}">
                @if($promoBanner && $promoBanner->image)
                    <img src="{{ asset('storage/'.$promoBanner->image) }}" alt="{{ $promoBanner->title ?? 'Special offer' }}">
                @else
                    <div class="promo-fallback"></div>
                @endif
                <div class="hero-overlay">
                    <div class="promo-copy">
                        <div class="eyebrow">{{ $promoBanner->subtitle ?? 'মিষ্টি-রসালো স্বাদে অনন্য' }}</div>
                        <h2>{{ $promoBanner->title ?? 'বাগানের সেরা আম্রপালি' }}</h2>
                        <span class="hero-cta">{{ $promoBanner->button_text ?? 'প্রি-অর্ডার চলছে' }}</span>
                    </div>
                </div>
            </a>
        </section>

        @if($categories->count())
            <section class="category-section">
                <h2 class="section-heading">Featured Categories</h2>
                <button class="side-arrow left" type="button" data-category-slide="prev" aria-label="Previous categories">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <div class="category-slider">
                    <div class="category-strip" id="featuredCategorySlider">
                        @foreach($categories as $cat)
                            @php
                                $categoryProduct = $cat->products->firstWhere('thumbnail') ?: $cat->children->flatMap->products->firstWhere('thumbnail');
                                $categoryImage = $cat->image ?: $categoryProduct?->thumbnail;
                            @endphp
                            <a class="category-item" href="{{ route('category.show', $cat->slug) }}">
                                <div class="category-image">
                                    @if($categoryImage)
                                        <img src="{{ asset('storage/'.$categoryImage) }}" alt="{{ $cat->name }}">
                                    @else
                                        <div class="category-icon">
                                            <i class="bi {{ $cat->icon ?: $categoryIcons[$loop->index % count($categoryIcons)] }}"></i>
                                        </div>
                                    @endif
                                </div>
                                <span>{{ $cat->name }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
                <button class="side-arrow right" type="button" data-category-slide="next" aria-label="Next categories">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </section>
        @endif
    </div>

    @if($bestSelling->count())
        <section class="products-section">
            <div class="home-wrap">
                <h2 class="section-heading">Top Selling Products</h2>
                <div class="products-grid">
                    @foreach($bestSelling as $product)
                        <div class="selling-card">
                            <a class="selling-image" href="{{ route('products.show', $product->slug) }}">
                                <img src="{{ $product->thumbnail ? asset('storage/'.$product->thumbnail) : asset('images/no-image.png') }}" alt="{{ $product->name }}">
                            </a>

                            <div class="selling-info">
                                <h3>{{ $product->name }}</h3>
                                <div class="price-line">
                                    @if($product->sale_price)
                                        <span class="home-price">&#2547;{{ number_format($product->sale_price, 0) }}</span>
                                        <span class="home-old-price">&#2547;{{ number_format($product->regular_price, 0) }}</span>
                                    @else
                                        <span class="home-price">&#2547;{{ number_format($product->regular_price, 0) }}</span>
                                    @endif
                                </div>

                                @if($product->sale_price && $product->regular_price > $product->sale_price)
                                    <span class="save-badge">Save &#2547;{{ number_format($product->regular_price - $product->sale_price, 0) }}</span>
                                @else
                                    <span class="d-block mb-4"></span>
                                @endif

                                <div class="selling-actions">
                                    <form action="{{ route('cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="sg-btn" {{ !$product->isInStock() ? 'disabled' : '' }}>
                                            <i class="bi bi-cart3"></i> Add To Cart
                                        </button>
                                    </form>

                                    <form action="{{ route('cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <input type="hidden" name="buy_now" value="1">
                                        <button type="submit" class="sg-btn primary" {{ !$product->isInStock() ? 'disabled' : '' }}>
                                            <i class="bi bi-cart3"></i> Buy now
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if($brands->count())
        <section class="brands-section">
            <div class="home-wrap">
                <div class="brand-head">
                    <h2 class="brand-title">Our Brands</h2>
                    <a class="see-all" href="{{ route('products.index') }}">SEE ALL <i class="bi bi-arrow-right"></i></a>
                </div>
                <div class="brand-slider-wrap">
                    <button class="brand-arrow left" type="button" data-brand-slide="prev" aria-label="Previous brands">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <div class="brand-slider">
                        <div class="brand-grid" id="brandSlider">
                            @foreach($brands as $brand)
                                <a class="brand-card" href="{{ route('brand.show', $brand->slug) }}">
                                    @if($brand->logo)
                                        <img src="{{ asset('storage/'.$brand->logo) }}" alt="{{ $brand->name }}">
                                    @else
                                        {{ $brand->name }}
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <button class="brand-arrow right" type="button" data-brand-slide="next" aria-label="Next brands">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
        </section>
    @endif

    <div class="legacy-sections">
        <div class="home-wrap">
            @if($featured->count())
                <section class="mb-5">
                    <div class="legacy-head">
                        <h2 class="legacy-title">Featured Products</h2>
                        <a class="see-all" href="{{ route('products.index') }}">SEE ALL <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="row g-3">
                        @foreach($featured as $product)
                            <div class="col-6 col-md-4 col-lg-3">
                                <x-product-card :product="$product" />
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($newArrivals->count())
                <section class="mb-5">
                    <div class="legacy-head">
                        <h2 class="legacy-title">New Arrivals</h2>
                        <a class="see-all" href="{{ route('products.index') }}">SEE ALL <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="row g-3">
                        @foreach($newArrivals as $product)
                            <div class="col-6 col-md-4 col-lg-3">
                                <x-product-card :product="$product" />
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($bestSelling->count())
                <section class="mb-5">
                    <div class="legacy-head">
                        <h2 class="legacy-title">Best Sellers</h2>
                        <a class="see-all" href="{{ route('products.index') }}">SEE ALL <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="row g-3">
                        @foreach($bestSelling as $product)
                            <div class="col-6 col-md-4 col-lg-3">
                                <x-product-card :product="$product" />
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($discounts->count())
                <section class="mb-5">
                    <div class="legacy-head">
                        <h2 class="legacy-title">Special Offers</h2>
                        <a class="see-all" href="{{ route('products.index') }}">SEE ALL <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="row g-3">
                        @foreach($discounts as $product)
                            <div class="col-6 col-md-4 col-lg-3">
                                <x-product-card :product="$product" />
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($allProducts->count())
                <section class="mb-5">
                    <div class="legacy-head">
                        <h2 class="legacy-title">Just For You</h2>
                        <a class="see-all" href="{{ route('products.index') }}">VIEW ALL PRODUCTS <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="row g-3">
                        @foreach($allProducts as $product)
                            <div class="col-6 col-md-4 col-lg-3">
                                <x-product-card :product="$product" />
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const setupAutoSlider = function (sliderId, itemSelector, buttonSelector, intervalMs) {
            const slider = document.getElementById(sliderId);
            if (!slider) {
                return;
            }

            const slide = function (direction) {
                const firstItem = slider.querySelector(itemSelector);
                const itemWidth = firstItem ? firstItem.getBoundingClientRect().width : 140;
                const gap = parseFloat(getComputedStyle(slider).columnGap || getComputedStyle(slider).gap || 24);
                const visibleItems = Math.max(1, Math.floor(slider.clientWidth / Math.max(1, itemWidth + gap)));
                const distance = (itemWidth + gap) * Math.max(1, visibleItems - 1);

                if (direction > 0 && slider.scrollLeft + slider.clientWidth >= slider.scrollWidth - 8) {
                    slider.scrollTo({ left: 0, behavior: 'smooth' });
                    return;
                }

                if (direction < 0 && slider.scrollLeft <= 8) {
                    slider.scrollTo({ left: slider.scrollWidth, behavior: 'smooth' });
                    return;
                }

                slider.scrollBy({
                    left: direction * distance,
                    behavior: 'smooth'
                });
            };

            let autoSlide = window.setInterval(function () {
                slide(1);
            }, intervalMs);

            const pauseAutoSlide = function () {
                window.clearInterval(autoSlide);
            };

            const resumeAutoSlide = function () {
                window.clearInterval(autoSlide);
                autoSlide = window.setInterval(function () {
                    slide(1);
                }, intervalMs);
            };

            slider.addEventListener('mouseenter', pauseAutoSlide);
            slider.addEventListener('mouseleave', resumeAutoSlide);
            slider.addEventListener('focusin', pauseAutoSlide);
            slider.addEventListener('focusout', resumeAutoSlide);

            document.querySelectorAll(buttonSelector).forEach(function (button) {
                button.addEventListener('click', function () {
                    const direction = (button.dataset.categorySlide || button.dataset.brandSlide) === 'next' ? 1 : -1;
                    slide(direction);
                    resumeAutoSlide();
                });
            });
        };

        setupAutoSlider('featuredCategorySlider', '.category-item', '[data-category-slide]', 3000);
        setupAutoSlider('brandSlider', '.brand-card', '[data-brand-slide]', 2800);
    });
</script>
@endpush
