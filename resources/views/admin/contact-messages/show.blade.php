@extends('layouts.admin')

@section('title', 'Contact Message')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.contact-messages.index') }}">Contact Messages</a></li>
<li class="breadcrumb-item active">View</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Contact Message</h4>
    <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
            <div>
                <h5 class="fw-bold mb-1">{{ $contactMessage->subject }}</h5>
                <div class="text-muted small">{{ $contactMessage->created_at->format('d M Y, h:i A') }}</div>
            </div>
            <span class="badge align-self-start bg-{{ $contactMessage->status === 'unread' ? 'danger' : 'secondary' }}">{{ ucfirst($contactMessage->status) }}</span>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="border rounded p-3">
                    <div class="text-muted small">Name</div>
                    <div class="fw-semibold">{{ $contactMessage->name }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="border rounded p-3">
                    <div class="text-muted small">Email</div>
                    <a href="mailto:{{ $contactMessage->email }}" class="fw-semibold">{{ $contactMessage->email }}</a>
                </div>
            </div>
        </div>

        <div class="border rounded p-3 bg-light">
            <div class="text-muted small mb-2">Message</div>
            <div style="white-space: pre-wrap;">{{ $contactMessage->message }}</div>
        </div>
    </div>
</div>
@endsection
