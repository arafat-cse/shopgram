@extends('layouts.admin')

@section('title', 'Analytics')

@section('breadcrumb')
<li class="breadcrumb-item active">Analytics</li>
@endsection

@push('styles')
<style>
    .analytics-card { border: 0; border-radius: 12px; box-shadow: 0 10px 26px rgba(15, 23, 42, .07); height: 100%; }
    .analytics-icon { width: 44px; height: 44px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.3rem; }
    .analytics-value { font-size: 1.35rem; font-weight: 800; color: #0f172a; }
    .chart-box { min-height: 310px; }
</style>
@endpush

@section('content')
@php
    $money = fn($amount) => ($currencySymbol ?? '৳') . number_format((float) $amount, 0);
    $cardItems = [
        ['Total Products', $cards['total_products'], 'bi-box-seam', 'primary', 'analytics.stock.view', false],
        ['Total Product Purchase Price', $money($cards['total_product_purchase_price']), 'bi-bag-check', 'warning', 'analytics.profit_loss.view', true],
        ['Total Product Selling Price', $money($cards['total_product_selling_price']), 'bi-tags', 'success', 'analytics.sales.view', true],
        ['Total Orders', $cards['total_orders'], 'bi-cart3', 'primary', 'analytics.sales.view', false],
        ['Total Sales', $money($cards['total_sales']), 'bi-cash-stack', 'success', 'analytics.sales.view', true],
        ['Purchase Cost of Sold Products', $money($cards['total_purchase_cost_sold']), 'bi-receipt', 'warning', 'analytics.profit_loss.view', true],
        ['Total Profit', $money($cards['total_profit']), 'bi-graph-up-arrow', 'success', 'analytics.profit_loss.view', true],
        ['Total Loss', $money($cards['total_loss']), 'bi-graph-down-arrow', 'danger', 'analytics.profit_loss.view', true],
        ['Pending Orders', $cards['pending_orders'], 'bi-clock', 'warning', 'analytics.sales.view', false],
        ['Processing Orders', $cards['processing_orders'], 'bi-arrow-repeat', 'info', 'analytics.sales.view', false],
        ['Completed Orders', $cards['completed_orders'], 'bi-check2-circle', 'success', 'analytics.sales.view', false],
        ['Cancelled Orders', $cards['cancelled_orders'], 'bi-x-circle', 'danger', 'analytics.sales.view', false],
        ['Total Customers', $cards['total_customers'], 'bi-people', 'info', 'analytics.customer.view', false],
        ['Low Stock Products', $cards['low_stock_products'], 'bi-exclamation-triangle', 'warning', 'analytics.stock.view', false],
        ['Out of Stock Products', $cards['out_of_stock_products'], 'bi-slash-circle', 'danger', 'analytics.stock.view', false],
        ["Today's Sales", $money($cards['today_sales']), 'bi-calendar-day', 'success', 'analytics.sales.view', true],
        ['This Month Sales', $money($cards['this_month_sales']), 'bi-calendar-month', 'success', 'analytics.sales.view', true],
        ['This Year Sales', $money($cards['this_year_sales']), 'bi-calendar3', 'success', 'analytics.sales.view', true],
    ];
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Business Analytics</h4>
        <div class="text-muted small">{{ $range['from']->format('d M Y') }} - {{ $range['to']->format('d M Y') }}</div>
    </div>
    @can('analytics.report.export')
        <div class="d-flex gap-2">
            <a href="{{ route('admin.analytics.export.pdf', request()->query()) }}" class="btn btn-outline-danger btn-sm"><i class="bi bi-file-earmark-pdf"></i> Export PDF</a>
            <a href="{{ route('admin.analytics.export.excel', request()->query()) }}" class="btn btn-outline-success btn-sm"><i class="bi bi-file-earmark-excel"></i> Export Excel</a>
        </div>
    @endcan
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Report Filter</label>
                <select name="filter" class="form-select form-select-sm" onchange="toggleCustomDates(this.value)">
                    @foreach([
                        'today' => 'Today',
                        'yesterday' => 'Yesterday',
                        'last_7_days' => 'Last 7 Days',
                        'this_month' => 'This Month',
                        'last_month' => 'Last Month',
                        'this_year' => 'This Year',
                        'custom' => 'Custom Date Range',
                    ] as $value => $label)
                        <option value="{{ $value }}" {{ request('filter', 'this_month') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 custom-date-field">
                <label class="form-label small fw-semibold">From</label>
                <input type="date" name="from_date" value="{{ request('from_date', $range['from']->toDateString()) }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3 custom-date-field">
                <label class="form-label small fw-semibold">To</label>
                <input type="date" name="to_date" value="{{ request('to_date', $range['to']->toDateString()) }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary btn-sm">Apply Filter</button>
                <a href="{{ route('admin.analytics.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach($cardItems as [$label, $value, $icon, $color, $permission, $sensitive])
        @can($permission)
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card analytics-card">
                    <div class="card-body d-flex gap-3 align-items-center">
                        <div class="analytics-icon bg-{{ $color }} bg-opacity-10 text-{{ $color }}"><i class="bi {{ $icon }}"></i></div>
                        <div>
                            <div class="analytics-value">{{ $value }}</div>
                            <div class="text-muted small">{{ $label }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    @endforeach
</div>

<div class="row g-4 mb-4">
    @can('analytics.sales.view')
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm chart-box">
            <div class="card-header bg-white fw-bold">Monthly Sales</div>
            <div class="card-body"><canvas id="salesChart"></canvas></div>
        </div>
    </div>
    @endcan
    @can('analytics.profit_loss.view')
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm chart-box">
            <div class="card-header bg-white fw-bold">Monthly Profit</div>
            <div class="card-body"><canvas id="profitChart"></canvas></div>
        </div>
    </div>
    @endcan
    @can('analytics.sales.view')
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm chart-box">
            <div class="card-header bg-white fw-bold">Order Status</div>
            <div class="card-body"><canvas id="statusChart"></canvas></div>
        </div>
    </div>
    @endcan
</div>

<div class="row g-4">
    @can('analytics.sales.view')
    <div class="col-xl-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-bold">Top Selling Products</div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light"><tr><th>Product</th><th>Sold</th><th>Sales</th></tr></thead>
                    <tbody>
                    @forelse($topSellingProducts as $product)
                        <tr><td class="small fw-semibold">{{ $product->name }}</td><td>{{ $product->sold_quantity }}</td><td>{{ $money($product->sales_amount) }}</td></tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">No sales found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endcan

    @can('analytics.sales.view')
    <div class="col-xl-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-bold">Recent Orders</div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light"><tr><th>Order</th><th>Customer</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td class="small fw-semibold">{{ $order->order_number }}</td>
                            <td class="small">{{ $order->user->name ?? '-' }}</td>
                            <td>{{ $money($order->total) }}</td>
                            <td><span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'secondary' }}">{{ ucfirst($order->payment_status) }}</span></td>
                            <td><x-order-status-badge :status="$order->status" /></td>
                            <td class="small text-muted">{{ $order->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No orders found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endcan

    @can('analytics.stock.view')
    <div class="col-xl-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-bold text-warning"><i class="bi bi-exclamation-triangle"></i> Low Stock Alert</div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light"><tr><th>Product</th><th>SKU</th><th>Stock</th><th>Alert</th></tr></thead>
                    <tbody>
                    @forelse($lowStockProducts as $product)
                        <tr><td class="small fw-semibold">{{ $product->name }}</td><td class="small">{{ $product->sku }}</td><td><span class="badge bg-warning text-dark">{{ $product->stock_quantity }}</span></td><td>{{ $product->low_stock_threshold }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">All stock levels OK.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endcan
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function toggleCustomDates(value) {
    document.querySelectorAll('.custom-date-field').forEach((el) => {
        el.style.display = value === 'custom' ? '' : 'none';
    });
}
toggleCustomDates(@json(request('filter', 'this_month')));

const chartDefaults = { responsive: true, maintainAspectRatio: false };

@can('analytics.sales.view')
new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: { labels: @json($monthlySales['labels']), datasets: [{ label: 'Sales', data: @json($monthlySales['data']), borderColor: '#16a34a', backgroundColor: 'rgba(22,163,74,.12)', tension: .35, fill: true }] },
    options: chartDefaults
});
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: { labels: @json(array_keys($orderStatus)), datasets: [{ data: @json(array_values($orderStatus)), backgroundColor: ['#f59e0b', '#0ea5e9', '#22c55e', '#ef4444'] }] },
    options: chartDefaults
});
@endcan

@can('analytics.profit_loss.view')
new Chart(document.getElementById('profitChart'), {
    type: 'bar',
    data: { labels: @json($monthlyProfit['labels']), datasets: [{ label: 'Profit', data: @json($monthlyProfit['data']), backgroundColor: '#2563eb' }] },
    options: chartDefaults
});
@endcan
</script>
@endpush
