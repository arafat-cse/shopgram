@extends('layouts.admin')
@section('title', 'Add Shipping Zone')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Add Shipping Zone</h4>
    <a href="{{ route('admin.shipping-zones.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>
<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-body">
        <form action="{{ route('admin.shipping-zones.store') }}" method="POST">
            @csrf
            <div class="mb-3"><label class="form-label">Zone Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
            <div class="mb-3"><label class="form-label">Districts (comma separated)</label>
                <textarea name="districts" class="form-control" rows="2" placeholder="Dhaka, Chittagong, Sylhet">{{ old('districts') }}</textarea></div>
            <div class="row g-3 mb-3">
                <div class="col-md-6"><label class="form-label">Charge (৳) *</label>
                    <input type="number" name="charge" class="form-control" value="{{ old('charge', 0) }}" min="0" step="0.01" required></div>
                <div class="col-md-6"><label class="form-label">Free Above (৳)</label>
                    <input type="number" name="free_above" class="form-control" value="{{ old('free_above') }}" min="0" placeholder="Leave blank to disable"></div>
            </div>
            <div class="mb-3"><label class="form-label">Estimated Delivery Days</label>
                <input type="text" name="estimated_days" class="form-control" value="{{ old('estimated_days', '3-5') }}"></div>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" name="is_active" value="1" checked>
                <label class="form-check-label">Active</label>
            </div>
            <button type="submit" class="btn btn-primary">Save Zone</button>
        </form>
    </div>
</div>
@endsection
