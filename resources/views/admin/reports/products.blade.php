@extends('layouts.admin')
@section('title', 'Products Report')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Products Report</h4>
    <a href="{{ route('admin.reports.export', 'products') }}" class="btn btn-outline-success btn-sm">Export CSV</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">Top Selling Products</div>
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Product</th><th>Category</th><th>Qty Sold</th><th>Revenue</th><th>Stock</th></tr>
            </thead>
            <tbody>
                @forelse($products as $i => $product)
                <tr>
                    <td class="small text-muted">{{ $products->firstItem() + $i }}</td>
                    <td class="small fw-semibold">{{ Str::limit($product->name, 40) }}</td>
                    <td class="small text-muted">{{ $product->category->name ?? '-' }}</td>
                    <td class="small">{{ number_format($product->total_qty ?? 0) }}</td>
                    <td class="small fw-semibold">৳{{ number_format($product->total_revenue ?? 0, 0) }}</td>
                    <td class="small">
                        @if($product->stock_quantity == 0)
                            <span class="badge bg-danger">Out</span>
                        @elseif($product->stock_quantity <= $product->low_stock_threshold)
                            <span class="badge bg-warning text-dark">{{ $product->stock_quantity }}</span>
                        @else
                            <span class="text-success">{{ $product->stock_quantity }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $products->links() }}</div>
</div>
@endsection
