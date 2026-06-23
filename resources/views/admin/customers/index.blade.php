@extends('layouts.admin')
@section('title', 'Customers')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Customers</h4>
</div>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name, email or phone..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm">Search</button>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Name</th><th>Email</th><th>Phone</th><th>Orders</th><th>Joined</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td class="fw-semibold small">{{ $customer->name }}</td>
                    <td class="small">{{ $customer->email }}</td>
                    <td class="small">{{ $customer->phone ?? '-' }}</td>
                    <td><span class="badge bg-secondary">{{ $customer->orders_count ?? 0 }}</span></td>
                    <td class="text-muted small">{{ $customer->created_at->format('d M Y') }}</td>
                    <td><a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No customers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $customers->links() }}</div>
</div>
@endsection
