@extends('layouts.admin')
@section('title', 'Stock Report')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Stock Report</h4>
    <a href="{{ route('admin.reports.export', 'stock') }}" class="btn btn-outline-success btn-sm">Export CSV</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-danger">{{ $out_of_stock }}</div>
            <div class="text-muted small">Out of Stock Products</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-warning">{{ $lowStock->total() }}</div>
            <div class="text-muted small">Low Stock Products</div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">Low Stock Products</div>
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead class="table-light">
                <tr><th>Product</th><th>Category</th><th>SKU</th><th>Stock</th><th>Threshold</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($lowStock as $product)
                <tr>
                    <td class="small fw-semibold">{{ Str::limit($product->name, 40) }}</td>
                    <td class="small text-muted">{{ $product->category->name ?? '-' }}</td>
                    <td class="small text-muted">{{ $product->sku ?? '-' }}</td>
                    <td>
                        @if($product->stock_quantity == 0)
                            <span class="badge bg-danger">Out of Stock</span>
                        @else
                            <span class="badge bg-warning text-dark">{{ $product->stock_quantity }}</span>
                        @endif
                    </td>
                    <td class="small text-muted">{{ $product->low_stock_threshold }}</td>
                    <td><a href="{{ route('admin.inventory.index') }}" class="btn btn-xs btn-outline-primary" style="font-size:11px;padding:2px 8px;">Restock</a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-success">All products have sufficient stock.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $lowStock->links() }}</div>
</div>
@endsection
