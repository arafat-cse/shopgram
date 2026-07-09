@extends('layouts.app')
@section('title', 'Our Brands')

@section('content')
<div class="container py-5">
    <x-breadcrumb :items="['Brands' => '#']" />

    <div class="text-center mb-5">
        <h1 class="fw-bold display-5 text-dark">Our Brands</h1>
        <p class="text-muted max-w-600 mx-auto">Explore products from your favorite world-renowned brands, premium quality guaranteed.</p>
    </div>

    <div class="row g-4">
        @forelse($brands as $brand)
        <div class="col-6 col-md-4 col-lg-3 col-xl-2">
            <a href="{{ route('brand.show', $brand->slug) }}" class="card h-100 border-0 shadow-sm text-decoration-none text-dark brand-hover-card">
                <div class="card-body d-flex flex-column align-items-center justify-content-center p-3 text-center">
                    <div class="brand-logo-container mb-3 d-flex align-items-center justify-content-center overflow-hidden bg-light rounded" style="width: 100%; height: 96px; padding: 8px;">
                        <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" class="img-fluid brand-logo-img" style="max-height: 74px; object-fit: contain; transition: transform 0.3s ease;">
                    </div>
                    <h5 class="fw-bold mb-2 brand-name-title">{{ $brand->name }}</h5>
                    <span class="badge rounded-pill bg-light text-secondary border px-3 py-1.5 small">
                        {{ $brand->products_count }} {{ Str::plural('product', $brand->products_count) }}
                    </span>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-tag text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-3">No brands found at the moment.</p>
        </div>
        @endforelse
    </div>
</div>

<style>
    .brand-hover-card {
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        border: 1px solid transparent !important;
    }
    .brand-hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
        border-color: var(--sg-orange) !important;
    }
    .brand-hover-card:hover .brand-logo-img {
        transform: scale(1.08);
    }
    .brand-logo-container {
        border: 1px solid #f1f3f5;
    }
    .brand-name-title {
        font-size: 1.15rem;
        color: #1a202c;
    }
</style>
@endsection
