@extends('layouts.customer')
@section('title', 'Change Password')
@section('customer_content')
<h4 class="fw-bold mb-4">Change Password</h4>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('customer.password.update') }}" method="POST">
            @csrf @method('PUT')
            <x-alert />
            <div class="mb-3">
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Password</button>
        </form>
    </div>
</div>
@endsection
