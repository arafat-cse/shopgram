@extends('layouts.admin')

@section('title', 'Contact Messages')

@section('breadcrumb')
<li class="breadcrumb-item active">Contact Messages</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Contact Messages</h4>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name, email, subject..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread</option>
                    <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($messages as $message)
                    <tr>
                        <td class="small fw-semibold">{{ $message->name }}</td>
                        <td class="small">{{ $message->email }}</td>
                        <td class="small">{{ Str::limit($message->subject, 45) }}</td>
                        <td><span class="badge bg-{{ $message->status === 'unread' ? 'danger' : 'secondary' }}">{{ ucfirst($message->status) }}</span></td>
                        <td class="small text-muted">{{ $message->created_at->format('d M Y, h:i A') }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.contact-messages.show', $message) }}" class="btn btn-sm btn-outline-primary">View</a>
                                <x-delete-button :action="route('admin.contact-messages.destroy', $message)" message="Delete this message?" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No contact messages found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $messages->links() }}</div>
</div>
@endsection
