@extends('layouts.app')
@section('title', 'Search: '.$q)
@section('content')
<div class="container py-4">
    <h4 class="fw-bold mb-1">Search Results for: <em>{{ $q }}</em></h4>
    <p class="text-muted small mb-4">{{ $products->total() }} products found</p>

    @if($products->count())
    <div class="row g-3">
        @foreach($products as $product)
        <div class="col-6 col-md-4 col-lg-3">
            <x-product-card :product="$product" />
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $products->links() }}</div>
    @else
    <div class="text-center py-5">
        <i class="bi bi-search text-muted" style="font-size:3rem"></i>
        <p class="text-muted mt-2">No results found for "{{ $q }}".</p>
        <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">Browse Products</a>
    </div>
    @endif
</div>
@endsection
