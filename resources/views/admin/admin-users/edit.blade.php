@extends('layouts.admin')
@section('title', 'Edit Admin User')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit Admin User</h4>
    <a href="{{ route('admin.admin-users.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.admin-users.update', $adminUser) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $adminUser->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $adminUser->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $adminUser->phone) }}">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Current Role</label>
                    <div class="form-control bg-light">{{ $adminUser->roles->pluck('name')->implode(', ') ?: 'No role assigned' }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                    <div class="form-text">Leave blank to keep current password.</div>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-primary">Update Admin User</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white fw-bold">Assign Role</div>
    <div class="card-body">
        <form action="{{ route('admin.admin-users.role.assign', $adminUser) }}" method="POST" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-8">
                <label class="form-label">Role</label>
                <select name="role" class="form-select" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ $adminUser->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary w-100">Assign Role</button>
            </div>
        </form>
    </div>
</div>
@endsection
