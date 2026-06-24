@extends('layouts.admin')
@section('title', 'Coupons')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Coupons</h4>
    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary"><i class="bi bi-plus"></i> Add Coupon</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Min Order</th>
                    <th>Usage</th>
                    <th>Ends</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coupons as $coupon)
                    <tr>
                        <td class="fw-semibold small font-monospace">{{ $coupon->code }}</td>
                        <td class="small">{{ $coupon->type === 'percent' ? 'Percentage' : 'Fixed Amount' }}</td>
                        <td class="small">{{ $coupon->type === 'percent' ? rtrim(rtrim($coupon->value, '0'), '.') . '%' : (($currencySymbol ?? 'Tk') . number_format($coupon->value, 0)) }}</td>
                        <td class="small">{{ $coupon->min_order_amount ? (($currencySymbol ?? 'Tk') . number_format($coupon->min_order_amount, 0)) : '-' }}</td>
                        <td class="small">{{ $coupon->used_count }}/{{ $coupon->usage_limit ?? '∞' }}</td>
                        <td class="small text-muted">{{ $coupon->ends_at ? $coupon->ends_at->format('d M Y') : 'No expiry' }}</td>
                        <td><span class="badge bg-{{ $coupon->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($coupon->status) }}</span></td>
                        <td>
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <x-delete-button :action="route('admin.coupons.destroy', $coupon)" message="Delete this coupon?" />
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-4 text-muted">No coupons found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $coupons->links() }}</div>
</div>
@endsection
