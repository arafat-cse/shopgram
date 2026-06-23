@extends('layouts.admin')
@section('title', 'Add Coupon')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Add Coupon</h4>
    <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>
<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-body">
        <form action="{{ route('admin.coupons.store') }}" method="POST">
            @csrf
            <div class="mb-3"><label class="form-label">Code *</label>
                <input type="text" name="code" class="form-control text-uppercase" value="{{ old('code') }}" required style="text-transform:uppercase"></div>
            <div class="mb-3"><label class="form-label">Type *</label>
                <select name="type" class="form-select" required>
                    <option value="fixed">Fixed Amount</option>
                    <option value="percent">Percentage</option>
                </select>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6"><label class="form-label">Value *</label>
                    <input type="number" name="value" class="form-control" value="{{ old('value') }}" min="0" step="0.01" required></div>
                <div class="col-md-6"><label class="form-label">Min Order (৳)</label>
                    <input type="number" name="min_order_amount" class="form-control" value="{{ old('min_order_amount', 0) }}" min="0"></div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6"><label class="form-label">Max Discount (৳)</label>
                    <input type="number" name="max_discount" class="form-control" value="{{ old('max_discount') }}" min="0"></div>
                <div class="col-md-6"><label class="form-label">Usage Limit</label>
                    <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit') }}" min="0"></div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6"><label class="form-label">Start Date</label>
                    <input type="date" name="starts_at" class="form-control" value="{{ old('starts_at') }}"></div>
                <div class="col-md-6"><label class="form-label">End Date</label>
                    <input type="date" name="expires_at" class="form-control" value="{{ old('expires_at') }}"></div>
            </div>
            <div class="mb-3"><label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save Coupon</button>
        </form>
    </div>
</div>
@endsection
