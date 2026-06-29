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
    .navbar { background: #fff !important; }

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

    /* ── Hero fallback slide themes ── */
    .hero-fallback {
        min-height: 394px;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    /* Slide 1 — Flash Sale: deep crimson → orange */
    .hero-fb-1 {
        background: linear-gradient(135deg, #1a0a00 0%, #6b1a00 40%, #c0390b 70%, #e8621a 100%);
    }
    .hero-fb-1::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 60% 55% at 80% 50%, rgba(255,160,50,.22) 0%, transparent 60%),
            radial-gradient(circle at 90% 10%, rgba(255,220,80,.18) 0%, transparent 40%);
        pointer-events: none;
    }
    /* Slide 2 — New Arrivals: dark navy → indigo */
    .hero-fb-2 {
        background: linear-gradient(135deg, #050a1a 0%, #0d1b4b 45%, #1a2d7a 70%, #2a4db5 100%);
    }
    .hero-fb-2::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 55% 50% at 75% 45%, rgba(100,160,255,.18) 0%, transparent 60%),
            radial-gradient(circle at 85% 15%, rgba(180,220,255,.12) 0%, transparent 35%);
        pointer-events: none;
    }
    /* Slide 3 — Exclusive Deals: forest → teal */
    .hero-fb-3 {
        background: linear-gradient(135deg, #021209 0%, #064a26 45%, #0b7c43 70%, #10a861 100%);
    }
    .hero-fb-3::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 55% 50% at 78% 48%, rgba(80,255,160,.15) 0%, transparent 60%),
            radial-gradient(circle at 88% 12%, rgba(160,255,200,.12) 0%, transparent 35%);
        pointer-events: none;
    }

    /* Promo card — "Today's Deal" deep purple → pink */
    .promo-fallback {
        min-height: 394px;
        height: 100%;
        position: relative;
        overflow: hidden;
        background: linear-gradient(150deg, #160820 0%, #3b0764 45%, #6d1e99 72%, #c026d3 100%);
    }
    .promo-fallback::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 65% 55% at 50% 80%, rgba(255,80,200,.18) 0%, transparent 60%),
            radial-gradient(circle at 15% 20%, rgba(200,120,255,.15) 0%, transparent 40%);
        pointer-events: none;
    }

    /* Decorative circle ring on right side of slides */
    .hero-deco-ring {
        position: absolute;
        right: -60px;
        top: 50%;
        transform: translateY(-50%);
        width: 380px;
        height: 380px;
        border-radius: 50%;
        border: 48px solid rgba(255,255,255,.06);
        pointer-events: none;
    }
    .hero-deco-ring::after {
        content: '';
        position: absolute;
        inset: 32px;
        border-radius: 50%;
        border: 24px solid rgba(255,255,255,.04);
    }

    /* Urgency badge */
    .urgency-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        border-radius: 999px;
        font-size: clamp(.72rem, 1.1vw, .92rem);
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        margin-bottom: 12px;
    }
    .urgency-badge.fire  { background: rgba(255,80,0,.85);  color: #fff; }
    .urgency-badge.new   { background: rgba(60,130,255,.85); color: #fff; }
    .urgency-badge.deal  { background: rgba(16,185,100,.85); color: #fff; }
    .urgency-badge.promo { background: rgba(220,0,220,.75);  color: #fff; }

    /* Countdown block */
    .hero-countdown {
        display: flex;
        gap: 8px;
        align-items: center;
        margin-top: 14px;
    }
    .cd-label {
        font-size: clamp(.7rem, 1vw, .9rem);
        font-weight: 500;
        opacity: .85;
        white-space: nowrap;
    }
    .cd-blocks {
        display: flex;
        gap: 5px;
    }
    .cd-block {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-width: 46px;
        padding: 4px 6px 2px;
        border-radius: 7px;
        background: rgba(0,0,0,.45);
        border: 1px solid rgba(255,255,255,.15);
        backdrop-filter: blur(4px);
    }
    .cd-num {
        font-size: clamp(1.1rem, 1.8vw, 1.7rem);
        font-weight: 800;
        line-height: 1;
    }
    .cd-unit {
        font-size: .58rem;
        font-weight: 600;
        opacity: .7;
        letter-spacing: .05em;
        text-transform: uppercase;
        margin-top: 2px;
    }
    .cd-sep {
        font-size: 1.3rem;
        font-weight: 700;
        opacity: .7;
        margin-top: -6px;
    }

    /* Deal date badge */
    .deal-date-badge {
        display: inline-block;
        margin-top: 12px;
        padding: 5px 14px;
        border-radius: 7px;
        background: rgba(0,0,0,.4);
        border: 1px solid rgba(255,255,255,.18);
        font-size: clamp(.72rem, 1vw, .88rem);
        font-weight: 600;
        opacity: .9;
        backdrop-filter: blur(3px);
    }

    /* Make hero CTA a real link-looking button */
    .hero-cta-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-top: 18px;
        padding: 10px 30px 12px;
        border: 2px solid rgba(255,255,255,.55);
        border-radius: 10px;
        background: rgba(0,0,0,.55);
        color: #fff;
        font-size: clamp(1rem, 1.6vw, 1.5rem);
        font-weight: 700;
        text-decoration: none;
        backdrop-filter: blur(4px);
        transition: background .2s, border-color .2s;
        pointer-events: all;
    }
    .hero-cta-link:hover { background: rgba(0,0,0,.75); color: #fff; border-color: rgba(255,255,255,.85); }

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

    .stock-urgency-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        width: fit-content;
        margin-bottom: 18px;
        padding: 4px 10px;
        border-radius: 999px;
        background: #fff3cd;
        color: #8a4b00;
        font-size: .82rem;
        font-weight: 700;
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

                @php
                    $fbSlides = [
                        [
                            'badge_class' => 'fire',
                            'badge_icon'  => '⚡',
                            'badge_text'  => 'Flash Sale — Today Only',
                            'eyebrow'     => 'Limited Time Deal',
                            'headline'    => 'Up to 50% OFF',
                            'sub'         => 'On top electronics, gadgets & more',
                            'cta_text'    => 'Shop Now',
                            'cta_url'     => route('products.index'),
                            'countdown'   => true,
                            'theme'       => 'hero-fb-1',
                        ],
                        [
                            'badge_class' => 'new',
                            'badge_icon'  => '🆕',
                            'badge_text'  => 'Just Dropped',
                            'eyebrow'     => 'Fresh Picks',
                            'headline'    => 'New Arrivals',
                            'sub'         => 'Discover what\'s just landed in store',
                            'cta_text'    => 'Explore Now',
                            'cta_url'     => route('products.index'),
                            'countdown'   => false,
                            'date_msg'    => 'Added ' . now()->format('d M Y'),
                            'theme'       => 'hero-fb-2',
                        ],
                        [
                            'badge_class' => 'deal',
                            'badge_icon'  => '🎯',
                            'badge_text'  => 'Exclusive Deal',
                            'eyebrow'     => 'Best Value',
                            'headline'    => 'Grab Big Deals',
                            'sub'         => 'Handpicked products at unbeatable prices',
                            'cta_text'    => 'See Offers',
                            'cta_url'     => route('products.index'),
                            'countdown'   => false,
                            'date_msg'    => 'Valid till ' . now()->addDays(3)->format('d M Y'),
                            'theme'       => 'hero-fb-3',
                        ],
                    ];
                @endphp

                <div class="carousel-inner h-100">
                    @foreach($heroSlides as $index => $banner)
                        @php $fb = $fbSlides[$index % 3]; @endphp
                        <div class="carousel-item h-100 {{ $index === 0 ? 'active' : '' }}">
                            @if($banner && $banner->image)
                                <img src="{{ asset('storage/'.$banner->image) }}" alt="{{ $banner->title ?? 'ShopGram offer' }}">
                                <div class="hero-overlay" style="pointer-events:none;">
                                    <div class="hero-copy">
                                        <div class="eyebrow">{{ $banner->subtitle }}</div>
                                        <h1>{{ $banner->title }}</h1>
                                        <a href="{{ $banner->button_url ?: route('products.index') }}" class="hero-cta-link" style="pointer-events:all;">
                                            {{ $banner->button_text ?: 'Shop Now' }} &rarr;
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="hero-fallback {{ $fb['theme'] }}">
                                    <div class="hero-deco-ring"></div>
                                </div>
                                <div class="hero-overlay" style="pointer-events:none;">
                                    <div class="hero-copy">
                                        <span class="urgency-badge {{ $fb['badge_class'] }}">
                                            {{ $fb['badge_icon'] }} {{ $fb['badge_text'] }}
                                        </span>
                                        <div class="eyebrow" style="opacity:.85; font-size:clamp(.95rem,1.6vw,1.5rem);">{{ $fb['eyebrow'] }}</div>
                                        <h1>{{ $fb['headline'] }}</h1>
                                        <div style="font-size:clamp(.85rem,1.2vw,1.1rem); opacity:.8; margin-top:6px;">{{ $fb['sub'] }}</div>

                                        @if($fb['countdown'])
                                            <div class="hero-countdown">
                                                <span class="cd-label">⏰ Ends in:</span>
                                                <div class="cd-blocks">
                                                    <div class="cd-block"><span class="cd-num" id="cd-h-{{ $index }}">00</span><span class="cd-unit">hrs</span></div>
                                                    <span class="cd-sep">:</span>
                                                    <div class="cd-block"><span class="cd-num" id="cd-m-{{ $index }}">00</span><span class="cd-unit">min</span></div>
                                                    <span class="cd-sep">:</span>
                                                    <div class="cd-block"><span class="cd-num" id="cd-s-{{ $index }}">00</span><span class="cd-unit">sec</span></div>
                                                </div>
                                            </div>
                                        @elseif(!empty($fb['date_msg']))
                                            <span class="deal-date-badge">📅 {{ $fb['date_msg'] }}</span>
                                        @endif

                                        <div>
                                            <a href="{{ $fb['cta_url'] }}" class="hero-cta-link" style="pointer-events:all;">
                                                {{ $fb['cta_text'] }} &rarr;
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
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
                    <div class="hero-overlay">
                        <div class="promo-copy">
                            <div class="eyebrow">{{ $promoBanner->subtitle }}</div>
                            <h2>{{ $promoBanner->title }}</h2>
                            <span class="hero-cta">{{ $promoBanner->button_text ?: 'Learn More' }}</span>
                        </div>
                    </div>
                @else
                    <div class="promo-fallback">
                        <div class="hero-deco-ring" style="right:-40px; width:280px; height:280px;"></div>
                    </div>
                    <div class="hero-overlay">
                        <div class="promo-copy">
                            <span class="urgency-badge promo" style="margin-bottom:10px;">🔥 Today's Deal</span>
                            <div class="eyebrow" style="opacity:.8; font-size:clamp(.9rem,1.3vw,1.3rem);">Don't Miss Out</div>
                            <h2>Exclusive<br>Offers</h2>
                            <span class="deal-date-badge" style="display:block; margin-top:10px; text-align:right;">
                                📅 Valid: {{ now()->format('d M') }} – {{ now()->addDays(2)->format('d M Y') }}
                            </span>
                            <span class="hero-cta" style="margin-top:14px; font-size:clamp(.9rem,1.3vw,1.3rem); min-width:140px; padding:8px 16px 10px;">
                                View All Deals &rarr;
                            </span>
                        </div>
                    </div>
                @endif
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
        <section class="products-section" id="best-sellers">
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
                                @endif
                                @php $reviewCount = $product->reviews()->count(); $avgRating = $product->average_rating; @endphp
                                @if($reviewCount > 0)
                                    <div class="d-flex align-items-center gap-1 mb-1" style="font-size:.8rem">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi {{ $i <= round($avgRating) ? 'bi-star-fill' : 'bi-star' }}" style="color:#f5a623"></i>
                                        @endfor
                                        <span class="text-muted">({{ $reviewCount }})</span>
                                    </div>
                                @endif

                                @if($product->isLowStock())
                                    <span class="stock-urgency-badge">
                                        <i class="bi bi-lightning-charge-fill"></i>
                                        Only {{ $product->stock_quantity }} left!
                                    </span>
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
        <section class="brands-section" id="brands">
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

            @if($recentProducts->count())
                <section class="mb-5">
                    <div class="legacy-head">
                        <h2 class="legacy-title">Recently Viewed Products</h2>
                        <a class="see-all" href="{{ route('products.index') }}">SEE ALL <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="row g-3">
                        @foreach($recentProducts as $product)
                            <div class="col-6 col-md-4 col-lg-3">
                                <x-product-card :product="$product" />
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($newArrivals->count())
                <section class="mb-5" id="new-arrivals">
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
                <section class="mb-5" id="offers">
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

        // Midnight countdown for flash sale slides
        function pad(n) { return String(n).padStart(2, '0'); }
        function tickCountdown() {
            const now = new Date();
            const midnight = new Date(now);
            midnight.setHours(24, 0, 0, 0);
            let diff = Math.max(0, Math.floor((midnight - now) / 1000));
            const h = Math.floor(diff / 3600); diff -= h * 3600;
            const m = Math.floor(diff / 60);   diff -= m * 60;
            const s = diff;
            document.querySelectorAll('[id^="cd-h-"]').forEach(el => el.textContent = pad(h));
            document.querySelectorAll('[id^="cd-m-"]').forEach(el => el.textContent = pad(m));
            document.querySelectorAll('[id^="cd-s-"]').forEach(el => el.textContent = pad(s));
        }
        tickCountdown();
        setInterval(tickCountdown, 1000);
    });
</script>
@endpush
