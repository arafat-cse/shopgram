@extends('layouts.admin')
@section('title', 'Brands')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Brands</h4>
    <a href="{{ route('admin.brands.create') }}" class="btn btn-primary"><i class="bi bi-plus"></i> Add Brand</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Logo</th><th>Name</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($brands as $brand)
                <tr>
                    <td><img src="{{ $brand->logo ? asset('storage/'.$brand->logo) : asset('images/no-image.png') }}" width="40" height="40" style="object-fit:contain"></td>
                    <td>{{ $brand->name }}</td>
                    <td><span class="badge bg-{{ $brand->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($brand->status) }}</span></td>
                    <td>
                        <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center py-4 text-muted">No brands.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $brands->links() }}</div>
</div>
@endsection
