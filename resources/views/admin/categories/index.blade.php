@extends('layouts.admin')
@section('title', 'Categories')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Categories</h4>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="bi bi-plus"></i> Add Category</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Name</th><th>Parent</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($categories as $cat)
                <tr>
                    <td>{{ $cat->parent_id ? '— ' : '' }}{{ $cat->name }}</td>
                    <td class="text-muted small">{{ $cat->parent->name ?? '-' }}</td>
                    <td><span class="badge bg-{{ $cat->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($cat->status) }}</span></td>
                    <td>
                        <a href="{{ route('admin.categories.edit', $cat) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center py-4 text-muted">No categories.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
