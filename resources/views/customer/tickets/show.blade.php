@extends('layouts.customer')
@section('title', 'Ticket #'.$ticket->id)
@section('customer_content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Ticket #{{ $ticket->id }}: {{ $ticket->subject }}</h4>
    <span class="badge bg-{{ $ticket->status === 'open' ? 'success' : ($ticket->status === 'pending' ? 'warning text-dark' : 'secondary') }} fs-6">{{ ucfirst($ticket->status) }}</span>
</div>

@foreach($ticket->replies as $reply)
<div class="card mb-3 border-0 shadow-sm {{ $reply->is_admin_reply ? 'border-start border-primary border-3' : '' }}">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-2">
            <strong>{{ $reply->is_admin_reply ? 'Support Team' : $reply->user->name }}</strong>
            <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
        </div>
        <p class="mb-0">{{ $reply->message }}</p>
    </div>
</div>
@endforeach

@if($ticket->status !== 'closed')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Send Reply</h6>
        <form action="{{ route('customer.tickets.reply', $ticket) }}" method="POST">
            @csrf
            <div class="mb-3">
                <textarea name="message" class="form-control" rows="3" placeholder="Your message..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Reply</button>
        </form>
    </div>
</div>
@endif
@endsection
