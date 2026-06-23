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
            <thead class="table-light"><tr><th>Code</th><th>Type</th><th>Value</th><th>Min Order</th><th>Usage</th><th>Expiry</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($coupons as $coupon)
                <tr>
                    <td class="fw-semibold small font-monospace">{{ $coupon->code }}</td>
                    <td class="small">{{ ucfirst($coupon->discount_type) }}</td>
                    <td class="small">{{ $coupon->discount_type === 'percentage' ? $coupon->discount_value.'%' : '৳'.number_format($coupon->discount_value,0) }}</td>
                    <td class="small">{{ $coupon->minimum_order_amount ? '৳'.number_format($coupon->minimum_order_amount,0) : '-' }}</td>
                    <td class="small">{{ $coupon->used_count }}/{{ $coupon->usage_limit ?? '∞' }}</td>
                    <td class="small text-muted">{{ $coupon->expires_at ? $coupon->expires_at->format('d M Y') : 'No expiry' }}</td>
                    <td><span class="badge bg-{{ $coupon->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($coupon->status) }}</span></td>
                    <td>
                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete coupon?')">
                            @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
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
