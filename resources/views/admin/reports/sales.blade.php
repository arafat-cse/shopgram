@extends('layouts.admin')
@section('title', 'Sales Report')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Sales Report</h4>
    <a href="{{ route('admin.reports.export', 'sales') }}" class="btn btn-outline-success btn-sm">Export CSV</a>
</div>

{{-- Date Filter --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">From</label>
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $from }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">To</label>
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $to }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            </div>
        </form>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-primary">৳{{ number_format($data['total_sales'], 0) }}</div>
            <div class="text-muted small">Total Sales</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-success">{{ $data['order_count'] }}</div>
            <div class="text-muted small">Delivered Orders</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-info">৳{{ number_format($data['avg_order_value'], 0) }}</div>
            <div class="text-muted small">Avg Order Value</div>
        </div>
    </div>
</div>

{{-- Daily Breakdown --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">Daily Breakdown</div>
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead class="table-light">
                <tr><th>Date</th><th>Orders</th><th>Revenue</th></tr>
            </thead>
            <tbody>
                @forelse($data['by_day'] as $row)
                <tr>
                    <td class="small">{{ $row->date }}</td>
                    <td class="small">{{ $row->orders }}</td>
                    <td class="small fw-semibold">৳{{ number_format($row->revenue, 0) }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center py-4 text-muted">No data for selected period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
