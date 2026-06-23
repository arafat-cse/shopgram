@extends('layouts.admin')
@section('title', 'Support Tickets')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Support Tickets</h4>
</div>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search tickets..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>#</th><th>Subject</th><th>Customer</th><th>Priority</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($tickets as $ticket)
                <tr>
                    <td class="small fw-semibold">{{ $ticket->ticket_number }}</td>
                    <td class="small">{{ Str::limit($ticket->subject, 40) }}</td>
                    <td class="small">{{ $ticket->user->name ?? '-' }}</td>
                    <td><span class="badge bg-{{ $ticket->priority === 'high' ? 'danger' : ($ticket->priority === 'medium' ? 'warning text-dark' : 'secondary') }}">{{ ucfirst($ticket->priority) }}</span></td>
                    <td><span class="badge bg-{{ $ticket->status === 'open' ? 'primary' : ($ticket->status === 'resolved' ? 'success' : 'secondary') }}">{{ ucwords(str_replace('_',' ',$ticket->status)) }}</span></td>
                    <td class="text-muted small">{{ $ticket->created_at->format('d M Y') }}</td>
                    <td><a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">No tickets found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $tickets->links() }}</div>
</div>
@endsection
