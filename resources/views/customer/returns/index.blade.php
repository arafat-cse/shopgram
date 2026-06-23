@extends('layouts.customer')
@section('title', 'Return Requests')
@section('customer_content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Return Requests</h4>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Order #</th><th>Reason</th><th>Status</th><th>Date</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($returns as $return)
                <tr>
                    <td class="fw-semibold small">{{ $return->order->order_number }}</td>
                    <td class="small">{{ Str::limit($return->reason, 40) }}</td>
                    <td><span class="badge bg-{{ $return->status === 'approved' ? 'success' : ($return->status === 'rejected' ? 'danger' : ($return->status === 'pending' ? 'warning text-dark' : 'secondary')) }}">{{ ucfirst($return->status) }}</span></td>
                    <td class="text-muted small">{{ $return->created_at->format('d M Y') }}</td>
                    <td><a href="{{ route('customer.orders.show', $return->order) }}" class="btn btn-sm btn-outline-secondary">Order</a></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">No return requests.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $returns->links() }}</div>
</div>
@endsection
