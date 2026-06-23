@extends('layouts.admin')
@section('title', 'Newsletter Subscribers')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Newsletter Subscribers</h4>
    <a href="{{ route('admin.newsletter.export') }}" class="btn btn-outline-success btn-sm"><i class="bi bi-download"></i> Export CSV</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Email</th><th>Status</th><th>Subscribed At</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($subscribers as $sub)
                <tr>
                    <td class="small">{{ $sub->email }}</td>
                    <td><span class="badge bg-{{ $sub->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($sub->status) }}</span></td>
                    <td class="text-muted small">{{ $sub->created_at->format('d M Y') }}</td>
                    <td>
                        <form action="{{ route('admin.newsletter.destroy', $sub) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove subscriber?')">
                            @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Remove</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center py-4 text-muted">No subscribers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $subscribers->links() }}</div>
</div>
@endsection
