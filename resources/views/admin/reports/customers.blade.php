@extends('layouts.admin')
@section('title', 'Customers Report')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Customers Report</h4>
    <a href="{{ route('admin.reports.export', 'customers') }}" class="btn btn-outline-success btn-sm">Export CSV</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">Customers by Orders</div>
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Orders</th><th>Total Spent</th><th>Joined</th></tr>
            </thead>
            <tbody>
                @forelse($customers as $i => $customer)
                <tr>
                    <td class="small text-muted">{{ $customers->firstItem() + $i }}</td>
                    <td class="small fw-semibold">{{ $customer->name }}</td>
                    <td class="small text-muted">{{ $customer->email }}</td>
                    <td class="small text-muted">{{ $customer->phone ?? '-' }}</td>
                    <td class="small">{{ $customer->orders_count }}</td>
                    <td class="small fw-semibold">৳{{ number_format($customer->orders_sum_total ?? 0, 0) }}</td>
                    <td class="small text-muted">{{ $customer->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">No customers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $customers->links() }}</div>
</div>
@endsection
