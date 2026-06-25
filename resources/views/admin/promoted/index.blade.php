@extends('layouts.admin')
@section('title', 'Promoted Products')
@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold mb-0">Promoted Products</h4>
        <p class="text-muted small mb-0 mt-1">
            Promoted products appear in the <strong>first-visit popup</strong> on the storefront.
            <span class="badge bg-warning text-dark ms-1">{{ $promotedCount }} active</span>
        </p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Search --}}
<form method="GET" class="mb-3">
    <div class="input-group" style="max-width:380px">
        <input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
        @if(request('search'))
            <a href="{{ route('admin.promoted.index') }}" class="btn btn-outline-danger"><i class="bi bi-x"></i></a>
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
                    <th>Price</th>
                    <th>Stock</th>
                    <th class="text-center">Promoted</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr class="{{ $product->is_promoted ? 'table-warning' : '' }}">
                    <td>
                        @if($product->thumbnail)
                            <img src="{{ asset('storage/'.$product->thumbnail) }}"
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
                    <td class="small">
                        @if($product->sale_price)
                            <span class="text-danger fw-semibold">৳{{ number_format($product->sale_price, 0) }}</span>
                            <span class="text-muted text-decoration-line-through ms-1" style="font-size:.75rem">৳{{ number_format($product->regular_price, 0) }}</span>
                        @else
                            ৳{{ number_format($product->regular_price, 0) }}
                        @endif
                    </td>
                    <td class="small">
                        @if($product->stock_quantity <= 0)
                            <span class="badge bg-danger">Out</span>
                        @elseif($product->isLowStock())
                            <span class="badge bg-warning text-dark">{{ $product->stock_quantity }} left</span>
                        @else
                            <span class="text-success">{{ $product->stock_quantity }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <form action="{{ route('admin.promoted.toggle', $product) }}" method="POST">
                            @csrf
                            @if($product->is_promoted)
                                <button type="submit" class="btn btn-sm btn-warning fw-semibold px-3"
                                        title="Click to remove from promoted">
                                    <i class="bi bi-megaphone-fill me-1"></i>Promoted
                                </button>
                            @else
                                <button type="submit" class="btn btn-sm btn-outline-secondary px-3"
                                        title="Click to add to promoted">
                                    <i class="bi bi-megaphone me-1"></i>Add
                                </button>
                            @endif
                        </form>
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
