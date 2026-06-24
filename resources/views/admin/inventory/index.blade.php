@extends('layouts.admin')
@section('title', 'Inventory')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Inventory Management</h4>
</div>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search products..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="filter" class="form-select form-select-sm">
                    <option value="">All Stock</option>
                    <option value="in_stock" {{ request('filter', request('stock_status')) === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="low_stock" {{ request('filter', request('stock_status')) === 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out_of_stock" {{ request('filter', request('stock_status')) === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Product</th><th>SKU</th><th>Stock</th><th>Low Stock Threshold</th><th>Update Stock</th><th>History</th></tr></thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $product->thumbnail ? asset('storage/'.$product->thumbnail) : asset('images/no-image.png') }}" width="35" height="35" style="object-fit:cover;border-radius:4px">
                            <span class="small fw-semibold">{{ Str::limit($product->name, 40) }}</span>
                        </div>
                    </td>
                    <td class="small text-muted font-monospace">{{ $product->sku }}</td>
                    <td>
                        <span class="badge bg-{{ $product->isLowStock() ? 'warning text-dark' : ($product->isInStock() ? 'success' : 'danger') }} fs-6">
                            {{ $product->stock_quantity }}
                        </span>
                    </td>
                    <td class="small">{{ $product->low_stock_threshold ?? 5 }}</td>
                    <td>
                        <form action="{{ route('admin.inventory.adjust') }}" method="POST" class="d-flex gap-2">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="note" value="Manual inventory update">
                            <input type="number" name="quantity" class="form-control form-control-sm" style="width:90px" value="{{ $product->stock_quantity }}" min="0">
                            <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                        </form>
                    </td>
                    <td><a href="{{ route('admin.inventory.history', $product) }}" class="btn btn-sm btn-outline-secondary">History</a></td>
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
