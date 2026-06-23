@extends('layouts.admin')
@section('title', 'Reports')
@section('content')
<h4 class="fw-bold mb-4">Reports</h4>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-2 fw-bold text-primary">৳{{ number_format($summary['total_revenue'] ?? 0, 0) }}</div>
            <div class="text-muted small">Total Revenue</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-2 fw-bold text-success">{{ $summary['total_orders'] ?? 0 }}</div>
            <div class="text-muted small">Total Orders</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-2 fw-bold text-info">{{ $summary['total_customers'] ?? 0 }}</div>
            <div class="text-muted small">Total Customers</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-2 fw-bold text-warning">৳{{ number_format($summary['avg_order_value'] ?? 0, 0) }}</div>
            <div class="text-muted small">Avg Order Value</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold d-flex justify-content-between">
                Sales by Period
                <form method="GET" class="d-flex gap-2">
                    <select name="period" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="7" {{ request('period','7') == '7' ? 'selected' : '' }}>Last 7 days</option>
                        <option value="30" {{ request('period') == '30' ? 'selected' : '' }}>Last 30 days</option>
                        <option value="90" {{ request('period') == '90' ? 'selected' : '' }}>Last 90 days</option>
                    </select>
                </form>
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <thead class="table-light"><tr><th>Date</th><th>Orders</th><th>Revenue</th></tr></thead>
                    <tbody>
                        @forelse($salesByDay ?? [] as $row)
                        <tr>
                            <td class="small">{{ $row->date }}</td>
                            <td class="small">{{ $row->orders }}</td>
                            <td class="small">৳{{ number_format($row->revenue, 0) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">No data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Top Selling Products</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <thead class="table-light"><tr><th>Product</th><th>Qty Sold</th><th>Revenue</th></tr></thead>
                    <tbody>
                        @forelse($topProducts ?? [] as $p)
                        <tr>
                            <td class="small">{{ Str::limit($p->name, 35) }}</td>
                            <td class="small">{{ $p->total_qty }}</td>
                            <td class="small">৳{{ number_format($p->total_revenue, 0) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">No data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
