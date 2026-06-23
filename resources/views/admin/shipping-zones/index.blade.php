@extends('layouts.admin')
@section('title', 'Shipping Zones')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Shipping Zones</h4>
    <a href="{{ route('admin.shipping-zones.create') }}" class="btn btn-primary"><i class="bi bi-plus"></i> Add Zone</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Zone Name</th><th>Areas</th><th>Base Charge</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($zones as $zone)
                <tr>
                    <td class="fw-semibold small">{{ $zone->name }}</td>
                    <td class="small text-muted">{{ Str::limit($zone->areas ?? '-', 50) }}</td>
                    <td class="small">৳{{ number_format($zone->base_charge, 0) }}</td>
                    <td><span class="badge bg-{{ $zone->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($zone->status) }}</span></td>
                    <td>
                        <a href="{{ route('admin.shipping-zones.edit', $zone) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.shipping-zones.destroy', $zone) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete zone?')">
                            @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">No shipping zones found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
