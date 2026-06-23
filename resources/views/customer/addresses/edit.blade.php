@extends('layouts.customer')
@section('title', 'Edit Address')
@section('customer_content')
<h4 class="fw-bold mb-4">Edit Address</h4>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('customer.addresses.update', $address) }}" method="POST">
            @csrf @method('PUT')
            <x-alert />
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Label</label>
                    <select name="label" class="form-select">
                        <option value="home" {{ $address->label === 'home' ? 'selected' : '' }}>Home</option>
                        <option value="office" {{ $address->label === 'office' ? 'selected' : '' }}>Office</option>
                        <option value="other" {{ $address->label === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $address->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone *</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $address->phone) }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Address *</label>
                    <input type="text" name="address_line" class="form-control" value="{{ old('address_line', $address->address_line) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">City *</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city', $address->city) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">District *</label>
                    <input type="text" name="district" class="form-control" value="{{ old('district', $address->district) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">ZIP</label>
                    <input type="text" name="zip" class="form-control" value="{{ old('zip', $address->zip) }}">
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_default" value="1" {{ $address->is_default ? 'checked' : '' }}>
                        <label class="form-check-label">Set as default address</label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Update Address</button>
            <a href="{{ route('customer.addresses.index') }}" class="btn btn-outline-secondary mt-3 ms-2">Cancel</a>
        </form>
    </div>
</div>
@endsection
