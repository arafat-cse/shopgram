@extends('layouts.admin')
@section('title', 'Ticket #' . $ticket->ticket_number)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Ticket #{{ $ticket->ticket_number }}</h4>
    <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<div class="row g-4">
    {{-- Ticket Info --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">Ticket Details</div>
            <div class="card-body small">
                <div class="mb-2"><span class="text-muted">Subject:</span><br><strong>{{ $ticket->subject }}</strong></div>
                <div class="mb-2"><span class="text-muted">Customer:</span><br>{{ $ticket->user->name ?? '-' }} <br><span class="text-muted">{{ $ticket->user->email ?? '' }}</span></div>
                <div class="mb-2"><span class="text-muted">Priority:</span><br>
                    <span class="badge bg-{{ $ticket->priority === 'high' ? 'danger' : ($ticket->priority === 'medium' ? 'warning text-dark' : 'secondary') }}">
                        {{ ucfirst($ticket->priority) }}
                    </span>
                </div>
                <div class="mb-2"><span class="text-muted">Status:</span><br>
                    <span class="badge bg-{{ $ticket->status === 'open' ? 'primary' : ($ticket->status === 'resolved' ? 'success' : ($ticket->status === 'closed' ? 'dark' : 'secondary')) }}">
                        {{ ucwords(str_replace('_', ' ', $ticket->status)) }}
                    </span>
                </div>
                <div class="mb-0"><span class="text-muted">Opened:</span><br>{{ $ticket->created_at->format('d M Y, h:i A') }}</div>
            </div>
        </div>

        {{-- Update Status --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Update Status</div>
            <div class="card-body">
                <form action="{{ route('admin.tickets.status', $ticket) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <select name="status" class="form-select form-select-sm">
                            <option value="open"        {{ $ticket->status === 'open'        ? 'selected' : '' }}>Open</option>
                            <option value="pending"     {{ $ticket->status === 'pending'     ? 'selected' : '' }}>Pending</option>
                            <option value="closed"      {{ $ticket->status === 'closed'      ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary w-100">Update</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Reply Thread --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">Conversation</div>
            <div class="card-body p-3" style="max-height:500px;overflow-y:auto;">
                @forelse($ticket->replies as $reply)
                <div class="d-flex mb-3 {{ $reply->is_admin_reply ? 'flex-row-reverse' : '' }}">
                    <div class="rounded-circle bg-{{ $reply->is_admin_reply ? 'primary' : 'secondary' }} text-white d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width:36px;height:36px;font-size:14px;">
                        {{ strtoupper(substr($reply->user->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="mx-2 {{ $reply->is_admin_reply ? 'text-end' : '' }}" style="max-width:75%">
                        <div class="bg-{{ $reply->is_admin_reply ? 'primary text-white' : 'light' }} rounded p-2 small">
                            {{ $reply->message }}
                        </div>
                        <div class="text-muted" style="font-size:11px;margin-top:3px;">
                            {{ $reply->user->name ?? 'Unknown' }} · {{ $reply->created_at->format('d M, h:i A') }}
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center py-3 small">No replies yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Reply Form --}}
        @if($ticket->status !== 'closed')
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Send Reply</div>
            <div class="card-body">
                <form action="{{ route('admin.tickets.reply', $ticket) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <textarea name="message" rows="4" class="form-control @error('message') is-invalid @enderror"
                            placeholder="Type your reply..." required>{{ old('message') }}</textarea>
                        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Send Reply</button>
                </form>
            </div>
        </div>
        @else
        <div class="alert alert-secondary small">Ticket is closed. Reopen to reply.</div>
        @endif
    </div>
</div>
@endsection
