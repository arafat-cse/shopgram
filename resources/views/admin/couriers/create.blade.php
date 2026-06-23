@extends('layouts.admin')
@section('title', 'Add Courier')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Add Courier</h4>
    <a href="{{ route('admin.couriers.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>
<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-body">
        <form action="{{ route('admin.couriers.store') }}" method="POST">
            @csrf
            <div class="mb-3"><label class="form-label">Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
            <div class="mb-3"><label class="form-label">Tracking URL (use {tracking_number})</label>
                <input type="text" name="tracking_url" class="form-control" value="{{ old('tracking_url') }}" placeholder="https://courier.com/track?id={tracking_number}"></div>
            <div class="mb-3"><label class="form-label">Contact Info</label>
                <input type="text" name="contact_info" class="form-control" value="{{ old('contact_info') }}"></div>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" name="is_active" value="1" checked>
                <label class="form-check-label">Active</label>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
@endsection
