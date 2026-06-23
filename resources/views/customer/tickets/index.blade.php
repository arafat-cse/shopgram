@extends('layouts.customer')
@section('title', 'Support Tickets')
@section('customer_content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Support Tickets</h4>
    <a href="{{ route('customer.tickets.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> New Ticket</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Subject</th><th>Priority</th><th>Status</th><th>Date</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                <tr>
                    <td>{{ $ticket->id }}</td>
                    <td>{{ Str::limit($ticket->subject, 40) }}</td>
                    <td><span class="badge bg-{{ $ticket->priority === 'high' ? 'danger' : ($ticket->priority === 'medium' ? 'warning text-dark' : 'secondary') }}">{{ ucfirst($ticket->priority) }}</span></td>
                    <td><span class="badge bg-{{ $ticket->status === 'open' ? 'success' : ($ticket->status === 'pending' ? 'warning text-dark' : 'secondary') }}">{{ ucfirst($ticket->status) }}</span></td>
                    <td class="text-muted small">{{ $ticket->created_at->format('d M Y') }}</td>
                    <td><a href="{{ route('customer.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No tickets yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $tickets->links() }}</div>
</div>
@endsection
