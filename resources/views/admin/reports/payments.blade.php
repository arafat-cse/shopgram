@extends('layouts.admin')
@section('title', 'Payments Report')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Payments Report</h4>
    <a href="{{ route('admin.reports.export', 'payments') }}" class="btn btn-outline-success btn-sm">Export CSV</a>
</div>

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

{{-- By Method Summary --}}
<div class="row g-3 mb-4">
    @foreach($by_method as $row)
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-4 fw-bold text-primary">৳{{ number_format($row->total, 0) }}</div>
            <div class="text-muted small">{{ strtoupper($row->method) }} ({{ $row->count }})</div>
        </div>
    </div>
    @endforeach
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">Payment Transactions</div>
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead class="table-light">
                <tr><th>Order #</th><th>Customer</th><th>Method</th><th>Amount</th><th>Status</th><th>Txn ID</th><th>Date</th></tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td class="small fw-semibold">{{ $payment->order->order_number ?? '-' }}</td>
                    <td class="small">{{ $payment->order->user->name ?? '-' }}</td>
                    <td class="small">{{ strtoupper($payment->method) }}</td>
                    <td class="small fw-semibold">৳{{ number_format($payment->amount, 0) }}</td>
                    <td><span class="badge bg-{{ $payment->status === 'paid' ? 'success' : ($payment->status === 'failed' ? 'danger' : 'warning text-dark') }}">{{ ucfirst($payment->status) }}</span></td>
                    <td class="small text-muted">{{ $payment->transaction_id ?? '-' }}</td>
                    <td class="small text-muted">{{ $payment->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">No payments found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $payments->withQueryString()->links() }}</div>
</div>
@endsection
