@extends('layouts.admin')
@section('title', 'Pages')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Pages</h4>
    <a href="{{ route('admin.pages.create') }}" class="btn btn-primary"><i class="bi bi-plus"></i> Add Page</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Title</th><th>Slug</th><th>Status</th><th>Updated</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($pages as $page)
                <tr>
                    <td class="fw-semibold small">{{ $page->title }}</td>
                    <td class="text-muted small">{{ $page->slug }}</td>
                    <td><span class="badge bg-{{ $page->status === 'published' ? 'success' : 'secondary' }}">{{ ucfirst($page->status) }}</span></td>
                    <td class="text-muted small">{{ $page->updated_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">No pages found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
