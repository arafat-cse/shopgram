@extends('layouts.admin')
@section('title', 'Coupons Report')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Coupons Report</h4>
    <a href="{{ route('admin.reports.export', 'coupons') }}" class="btn btn-outline-success btn-sm">Export CSV</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">Coupon Usage</div>
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead class="table-light">
                <tr><th>Code</th><th>Type</th><th>Value</th><th>Used</th><th>Limit</th><th>Status</th><th>Expires</th></tr>
            </thead>
            <tbody>
                @forelse($coupons as $coupon)
                <tr>
                    <td class="small fw-semibold"><code>{{ $coupon->code }}</code></td>
                    <td class="small">{{ ucfirst($coupon->type) }}</td>
                    <td class="small">
                        @if($coupon->type === 'percent')
                            {{ $coupon->value }}%
                        @else
                            ৳{{ number_format($coupon->value, 0) }}
                        @endif
                    </td>
                    <td class="small">{{ $coupon->usages_count }} / {{ $coupon->usage_limit ?? '∞' }}</td>
                    <td class="small text-muted">{{ $coupon->per_user_limit ?? '∞' }} per user</td>
                    <td><span class="badge bg-{{ $coupon->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($coupon->status) }}</span></td>
                    <td class="small text-muted">{{ $coupon->ends_at ? $coupon->ends_at->format('d M Y') : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">No coupons found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $coupons->links() }}</div>
</div>
@endsection
