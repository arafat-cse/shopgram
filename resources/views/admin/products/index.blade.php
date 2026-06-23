@extends('layouts.admin')
@section('title', 'Products')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Products</h4>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><i class="bi bi-plus"></i> Add Product</a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search products..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $product->thumbnail ? asset('storage/'.$product->thumbnail) : asset('images/no-image.png') }}" width="40" height="40" style="object-fit:cover;border-radius:4px">
                            <div>
                                <div class="fw-semibold small">{{ Str::limit($product->name, 35) }}</div>
                                <small class="text-muted">{{ $product->sku }}</small>
                            </div>
                        </div>
                    </td>
                    <td class="small">{{ $product->category->name ?? '-' }}</td>
                    <td class="small">
                        @if($product->sale_price)
                            <span class="text-danger">৳{{ number_format($product->sale_price,0) }}</span>
                            <del class="text-muted">৳{{ number_format($product->regular_price,0) }}</del>
                        @else
                            ৳{{ number_format($product->regular_price,0) }}
                        @endif
                    </td>
                    <td><span class="badge bg-{{ $product->isLowStock() ? 'warning text-dark' : ($product->isInStock() ? 'success' : 'danger') }}">{{ $product->stock_quantity }}</span></td>
                    <td><span class="badge bg-{{ $product->status === 'active' ? 'success' : ($product->status === 'draft' ? 'warning text-dark' : 'secondary') }}">{{ ucfirst($product->status) }}</span></td>
                    <td>
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
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
