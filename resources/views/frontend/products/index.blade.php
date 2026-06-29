@extends('layouts.app')
@section('title', isset($category) ? $category->name : (isset($brand) ? $brand->name : 'All Products'))
@section('content')
<div class="container py-4">
    <x-breadcrumb :items="[isset($category) ? $category->name : (isset($brand) ? $brand->name : 'Products') => '#']" />

    <div class="row g-4">
        {{-- Mobile Filter Button --}}
        <div class="col-12 d-lg-none">
            <button class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-2 py-2 fw-semibold" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileFiltersOffcanvas" aria-controls="mobileFiltersOffcanvas">
                <i class="bi bi-funnel"></i> Filter Products
            </button>
        </div>

        {{-- Sidebar Filters (Desktop) --}}
        <div class="col-lg-3 d-none d-lg-block">
            <div class="card border-0 shadow-sm p-3">
                <h6 class="fw-bold mb-3">Filters</h6>
                <form method="GET" action="{{ route('products.index') }}">
                    @if(isset($categories))
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Category</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="category" id="cat_all"
                                   value="" {{ request('category') ? '' : 'checked' }}>
                            <label class="form-check-label small" for="cat_all">All Categories</label>
                        </div>
                        @foreach($categories as $cat)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="category" id="cat{{ $cat->id }}"
                                   value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'checked' : '' }}>
                            <label class="form-check-label small" for="cat{{ $cat->id }}">{{ $cat->name }}</label>
                        </div>
                            @foreach($cat->children as $child)
                            <div class="form-check ms-3">
                                <input class="form-check-input" type="radio" name="category" id="cat{{ $child->id }}"
                                       value="{{ $child->slug }}" {{ request('category') == $child->slug ? 'checked' : '' }}>
                                <label class="form-check-label small text-muted" for="cat{{ $child->id }}">{{ $child->name }}</label>
                            </div>
                            @endforeach
                        @endforeach
                    </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Price Range</label>
                        <div class="d-flex gap-2">
                            <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Min" value="{{ request('min_price') }}">
                            <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Max" value="{{ request('max_price') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Sort By</label>
                        <select name="sort" class="form-select form-select-sm">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">Apply Filters</button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm w-100 mt-2">Clear</a>
                </form>
            </div>
        </div>

        {{-- Products Grid --}}
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="mb-0 text-muted small">Showing {{ $products->total() }} products</p>
            </div>

            @if($products->count())
            <div class="row g-3">
                @foreach($products as $product)
                <div class="col-6 col-md-4 col-xl-3">
                    <x-product-card :product="$product" />
                </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $products->links() }}</div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-box-seam text-muted" style="font-size:3rem"></i>
                <p class="text-muted mt-2">No products found.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">Clear Filters</a>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Mobile Filters Offcanvas --}}
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileFiltersOffcanvas" aria-labelledby="mobileFiltersOffcanvasLabel" style="z-index: 1070;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold" id="mobileFiltersOffcanvasLabel">Filters</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form method="GET" action="{{ route('products.index') }}">
            @if(isset($categories))
            <div class="mb-3">
                <label class="form-label fw-semibold small">Category</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category" id="m_cat_all"
                           value="" {{ request('category') ? '' : 'checked' }}>
                    <label class="form-check-label small" for="m_cat_all">All Categories</label>
                </div>
                @foreach($categories as $cat)
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category" id="m_cat{{ $cat->id }}"
                           value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'checked' : '' }}>
                    <label class="form-check-label small" for="m_cat{{ $cat->id }}">{{ $cat->name }}</label>
                </div>
                    @foreach($cat->children as $child)
                    <div class="form-check ms-3">
                        <input class="form-check-input" type="radio" name="category" id="m_cat{{ $child->id }}"
                               value="{{ $child->slug }}" {{ request('category') == $child->slug ? 'checked' : '' }}>
                        <label class="form-check-label small text-muted" for="m_cat{{ $child->id }}">{{ $child->name }}</label>
                    </div>
                    @endforeach
                @endforeach
            </div>
            @endif
            <div class="mb-3">
                <label class="form-label fw-semibold small">Price Range</label>
                <div class="d-flex gap-2">
                    <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Min" value="{{ request('min_price') }}">
                    <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Max" value="{{ request('max_price') }}">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold small">Sort By</label>
                <select name="sort" class="form-select form-select-sm">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm w-100 py-2">Apply Filters</button>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm w-100 mt-2 py-2">Clear All</a>
        </form>
    </div>
</div>
@endsection
