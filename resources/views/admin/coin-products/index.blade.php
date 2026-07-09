@extends('layouts.admin')
@section('title', 'Coin Products')
@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold mb-0">Coin Products</h4>
        <p class="text-muted small mb-0 mt-1">
            Products customers can redeem using only coins (no cash). Set a coin price to list a product here.
            <span class="badge bg-warning text-dark ms-1">{{ $redeemableCount }} active</span>
        </p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
        {{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Search --}}
<form method="GET" class="mb-3">
    <div class="input-group" style="max-width:380px">
        <input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
        @if(request('search'))
            <a href="{{ route('admin.coin-products.index') }}" class="btn btn-outline-danger"><i class="bi bi-x"></i></a>
        @endif
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:60px"></th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Regular Price</th>
                    <th>Stock</th>
                    <th style="width:280px">Coin Price</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr class="{{ $product->is_coin_redeemable ? 'table-warning' : '' }}">
                    <td>
                        @if($product->thumbnail)
                            <img src="{{ $product->thumbnail_url }}"
                                 width="48" height="48"
                                 style="object-fit:cover;border-radius:6px;border:1px solid #eee">
                        @else
                            <div style="width:48px;height:48px;background:#f1f3f5;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-image text-muted"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="fw-semibold small">{{ $product->name }}</div>
                        @if($product->sku)<div class="text-muted" style="font-size:.75rem">SKU: {{ $product->sku }}</div>@endif
                    </td>
                    <td class="small text-muted">{{ $product->category->name ?? '—' }}</td>
                    <td class="small">৳{{ number_format($product->regular_price, 0) }}</td>
                    <td class="small">
                        @if($product->stock_quantity <= 0)
                            <span class="badge bg-danger">Out</span>
                        @else
                            <span class="text-success">{{ $product->stock_quantity }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-2 align-items-center">
                            <form action="{{ route('admin.coin-products.set', $product) }}" method="POST" class="d-flex gap-2">
                                @csrf
                                <input type="number" name="coin_price" class="form-control form-control-sm" style="width:100px"
                                       min="1" placeholder="Coins" value="{{ $product->coin_price }}" required>
                                <button type="submit" class="btn btn-sm {{ $product->is_coin_redeemable ? 'btn-warning' : 'btn-outline-secondary' }} px-3">
                                    <i class="bi bi-coin me-1"></i>{{ $product->is_coin_redeemable ? 'Update' : 'Add' }}
                                </button>
                            </form>
                            @if($product->is_coin_redeemable)
                                <form action="{{ route('admin.coin-products.remove', $product) }}" method="POST"
                                      onsubmit="return confirm('Remove {{ $product->name }} from coin redemption?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No products found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $products->links() }}</div>
</div>

@endsection
