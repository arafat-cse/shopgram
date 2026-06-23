@extends('layouts.admin')
@section('title', 'Reviews')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Product Reviews</h4>
</div>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Product</th><th>Customer</th><th>Rating</th><th>Review</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($reviews as $review)
                <tr>
                    <td class="small">{{ Str::limit($review->product->name ?? '-', 30) }}</td>
                    <td class="small">{{ $review->user->name ?? '-' }}</td>
                    <td>
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= $review->rating ? '-fill text-warning' : ' text-muted' }}"></i>
                        @endfor
                    </td>
                    <td class="small text-muted">{{ Str::limit($review->comment, 60) }}</td>
                    <td><span class="badge bg-{{ $review->status === 'approved' ? 'success' : ($review->status === 'rejected' ? 'danger' : 'warning text-dark') }}">{{ ucfirst($review->status) }}</span></td>
                    <td>
                        @if($review->status === 'pending')
                        <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-outline-success">Approve</button></form>
                        <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-outline-danger">Reject</button></form>
                        @endif
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-secondary">Delete</button></form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No reviews found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $reviews->links() }}</div>
</div>
@endsection
