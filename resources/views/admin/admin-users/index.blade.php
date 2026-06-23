@extends('layouts.admin')
@section('title', 'Admin Users')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Admin Users</h4>
    <a href="{{ route('admin.admin-users.create') }}" class="btn btn-primary"><i class="bi bi-plus"></i> Add Admin</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last Login</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($adminUsers as $user)
                <tr>
                    <td class="fw-semibold small">{{ $user->name }}</td>
                    <td class="small">{{ $user->email }}</td>
                    <td><span class="badge bg-primary">{{ $user->roles->pluck('name')->implode(', ') }}</span></td>
                    <td><span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($user->status) }}</span></td>
                    <td class="text-muted small">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</td>
                    <td>
                        <a href="{{ route('admin.admin-users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('admin.admin-users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete admin user?')">
                            @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No admin users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
