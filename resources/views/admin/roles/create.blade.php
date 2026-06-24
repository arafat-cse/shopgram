@extends('layouts.admin')
@section('title', 'Create Role')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Create Role</h4>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<form action="{{ route('admin.roles.store') }}" method="POST">
    @csrf
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Role Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>Permissions</strong>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleAllPermissions()">Select All</button>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @forelse($permissions as $group => $items)
                    <div class="col-md-6 col-xl-4">
                        <div class="border rounded p-3 h-100">
                            <h6 class="fw-bold text-capitalize mb-3">{{ str_replace('_', ' ', $group) }}</h6>
                            @foreach($items as $permission)
                                <div class="form-check mb-2">
                                    <input class="form-check-input permission-check" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="permission_{{ $permission->id }}" {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-muted">No permissions found.</div>
                @endforelse
            </div>
        </div>
        <div class="card-footer bg-white text-end">
            <button type="submit" class="btn btn-primary">Create Role</button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function toggleAllPermissions() {
    const checks = document.querySelectorAll('.permission-check');
    const shouldCheck = Array.from(checks).some(check => !check.checked);
    checks.forEach(check => check.checked = shouldCheck);
}
</script>
@endpush
