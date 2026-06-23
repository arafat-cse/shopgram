@extends('layouts.admin')
@section('title', 'Payments')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Payment Transactions</h4>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Transaction ID or order #" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="paid"     {{ request('status') === 'paid'     ? 'selected' : '' }}>Paid</option>
                    <option value="failed"   {{ request('status') === 'failed'   ? 'selected' : '' }}>Failed</option>
                    <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="method" class="form-select form-select-sm">
                    <option value="">All Methods</option>
                    <option value="cod"   {{ request('method') === 'cod'   ? 'selected' : '' }}>COD</option>
                    <option value="bkash" {{ request('method') === 'bkash' ? 'selected' : '' }}>bKash</option>
                    <option value="nagad" {{ request('method') === 'nagad' ? 'selected' : '' }}>Nagad</option>
                    <option value="card"  {{ request('method') === 'card'  ? 'selected' : '' }}>Card</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Transaction ID</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                {{-- Main row --}}
                <tr>
                    <td class="fw-semibold small">
                        <a href="{{ route('admin.orders.show', $payment->order) }}" class="text-decoration-none">
                            {{ $payment->order->order_number ?? '-' }}
                        </a>
                    </td>
                    <td class="small">{{ $payment->order->user->name ?? '-' }}</td>
                    <td>
                        <span class="badge bg-{{ $payment->method === 'cod' ? 'warning text-dark' : ($payment->method === 'bkash' ? 'danger' : ($payment->method === 'nagad' ? 'success' : 'info')) }}">
                            {{ strtoupper($payment->method) }}
                        </span>
                    </td>
                    <td class="fw-semibold small">৳{{ number_format($payment->amount, 0) }}</td>
                    <td class="small font-monospace text-muted">{{ $payment->transaction_id ?: '—' }}</td>
                    <td>
                        <span class="badge bg-{{ $payment->status === 'paid' ? 'success' : ($payment->status === 'failed' ? 'danger' : ($payment->status === 'refunded' ? 'warning text-dark' : 'secondary')) }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                    <td class="text-muted small">{{ $payment->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary py-0 px-2" type="button"
                                data-bs-toggle="collapse" data-bs-target="#pay-{{ $payment->id }}"
                                aria-expanded="false">
                            <i class="bi bi-pencil-square"></i> Update
                        </button>
                    </td>
                </tr>
                {{-- Inline update form (collapsed) --}}
                <tr class="collapse bg-light" id="pay-{{ $payment->id }}">
                    <td colspan="8" class="px-4 py-3">
                        <form action="{{ route('admin.payments.status.update', $payment) }}" method="POST"
                              class="row g-2 align-items-end">
                            @csrf
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold mb-1">Payment Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="pending"  {{ $payment->status === 'pending'  ? 'selected' : '' }}>Pending</option>
                                    <option value="paid"     {{ $payment->status === 'paid'     ? 'selected' : '' }}>Paid ✓</option>
                                    <option value="failed"   {{ $payment->status === 'failed'   ? 'selected' : '' }}>Failed</option>
                                    <option value="refunded" {{ $payment->status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold mb-1">
                                    Transaction ID
                                    @if($payment->method === 'cod')
                                        <span class="text-muted">(COD receipt / note)</span>
                                    @else
                                        <span class="text-muted">(bKash/Nagad TrxID)</span>
                                    @endif
                                </label>
                                <input type="text" name="transaction_id" class="form-control form-control-sm"
                                       value="{{ $payment->transaction_id }}"
                                       placeholder="{{ $payment->method === 'cod' ? 'e.g. COD-COLLECTED' : 'e.g. 8N7A2B3C4D' }}">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-sm btn-success px-3">
                                    <i class="bi bi-check-lg"></i> Save
                                </button>
                            </div>
                            @if($payment->method === 'cod')
                            <div class="col-12 mt-1">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle"></i>
                                    COD: delivery agent cash collect করার পর <strong>Paid</strong> করুন।
                                    Order status আলাদাভাবে <a href="{{ route('admin.orders.show', $payment->order) }}">Order page</a> থেকে update করুন।
                                </small>
                            </div>
                            @endif
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">No payment records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $payments->links() }}</div>
</div>
@endsection
