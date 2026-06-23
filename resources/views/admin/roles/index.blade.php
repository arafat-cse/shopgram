@extends('layouts.admin')
@section('title', 'Roles & Permissions')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Roles & Permissions</h4>
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary"><i class="bi bi-plus"></i> Add Role</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Role</th><th>Guard</th><th>Permissions</th><th>Users</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($roles as $role)
                <tr>
                    <td class="fw-semibold small">{{ $role->name }}</td>
                    <td class="text-muted small">{{ $role->guard_name }}</td>
                    <td><span class="badge bg-secondary">{{ $role->permissions_count ?? $role->permissions->count() }}</span></td>
                    <td><span class="badge bg-info text-dark">{{ $role->users_count ?? 0 }}</span></td>
                    <td>
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        @if(!in_array($role->name, ['super-admin','admin']))
                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete role?')">
                            @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">No roles found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
