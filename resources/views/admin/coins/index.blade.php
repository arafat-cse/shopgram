@extends('layouts.admin')
@section('title', 'Loyalty Coins')
@section('content')

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold mb-0">Loyalty Coins</h4>
        <p class="text-muted small mb-0 mt-1">
            Coins earned/spent by customers. Total outstanding balance:
            <span class="badge bg-warning text-dark ms-1">{{ number_format($totalCoinsOutstanding) }} coins</span>
        </p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        {{-- Filters --}}
        <form method="GET" class="row g-2 mb-3">
            <div class="col-auto">
                <input type="text" name="user_id" class="form-control form-control-sm" placeholder="Filter by User ID" value="{{ request('user_id') }}">
            </div>
            <div class="col-auto">
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    @foreach(['earn','redeem','reversal_earn','reversal_redeem','admin_adjust'] as $t)
                        <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ', $t)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-outline-secondary" type="submit">Filter</button>
                @if(request('user_id') || request('type'))
                    <a href="{{ route('admin.coins.index') }}" class="btn btn-sm btn-outline-danger">Clear</a>
                @endif
            </div>
        </form>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>Type</th>
                            <th>Order</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                        <tr>
                            <td class="text-muted small">{{ $tx->created_at->format('d M Y, h:i A') }}</td>
                            <td class="small">
                                {{ $tx->user->name ?? '—' }}
                                <div class="text-muted" style="font-size:.72rem">#{{ $tx->user_id }}</div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $tx->type_label }}</span></td>
                            <td class="small text-muted">{{ $tx->order?->order_number ?? '—' }}</td>
                            <td class="text-end fw-semibold {{ $tx->amount >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $tx->amount >= 0 ? '+' : '' }}{{ number_format($tx->amount) }}
                            </td>
                            <td class="text-end">{{ number_format($tx->balance_after) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No coin transactions found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">{{ $transactions->links() }}</div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Manual Adjustment</div>
            <div class="card-body">
                <form action="{{ route('admin.coins.adjust') }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small">User (ID, email, or phone)</label>
                        <input type="text" name="identifier" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Amount (+ credit / - debit)</label>
                        <input type="number" name="amount" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Reason</label>
                        <input type="text" name="reason" class="form-control form-control-sm" required maxlength="255">
                    </div>
                    <button class="btn btn-sm btn-warning w-100 fw-semibold">Apply Adjustment</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
