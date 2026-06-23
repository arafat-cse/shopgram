@extends('layouts.admin')
@section('title', 'Couriers')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Couriers</h4>
    <a href="{{ route('admin.couriers.create') }}" class="btn btn-primary"><i class="bi bi-plus"></i> Add Courier</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Name</th><th>Tracking URL</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($couriers as $courier)
                <tr>
                    <td class="fw-semibold small">{{ $courier->name }}</td>
                    <td class="small text-muted">{{ Str::limit($courier->tracking_url ?? '-', 50) }}</td>
                    <td><span class="badge bg-{{ $courier->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($courier->status) }}</span></td>
                    <td>
                        <a href="{{ route('admin.couriers.edit', $courier) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.couriers.destroy', $courier) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete courier?')">
                            @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center py-4 text-muted">No couriers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
