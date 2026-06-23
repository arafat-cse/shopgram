@extends('layouts.admin')
@section('title', 'Banners')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Banners</h4>
    <a href="{{ route('admin.banners.create') }}" class="btn btn-primary"><i class="bi bi-plus"></i> Add Banner</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Image</th><th>Title</th><th>Position</th><th>Sort</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($banners as $banner)
                <tr>
                    <td><img src="{{ asset('storage/'.$banner->image) }}" height="50" style="object-fit:cover;border-radius:4px"></td>
                    <td class="small">{{ $banner->title ?? '-' }}</td>
                    <td class="small text-muted">{{ $banner->position }}</td>
                    <td class="small">{{ $banner->sort_order }}</td>
                    <td><span class="badge bg-{{ $banner->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($banner->status) }}</span></td>
                    <td>
                        <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No banners found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
