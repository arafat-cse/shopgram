@extends('layouts.customer')
@section('title', 'My Addresses')
@section('customer_content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">My Addresses</h4>
    <a href="{{ route('customer.addresses.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> Add Address</a>
</div>

<div class="row g-3">
    @forelse($addresses as $addr)
    <div class="col-md-6">
        <div class="card border-0 shadow-sm {{ $addr->is_default ? 'border-primary border-2' : '' }}">
            <div class="card-body">
                @if($addr->is_default)<span class="badge bg-primary mb-2">Default</span>@endif
                <span class="badge bg-secondary mb-2">{{ ucfirst($addr->label) }}</span>
                <p class="mb-1 fw-semibold">{{ $addr->name }}</p>
                <p class="mb-1 text-muted small">{{ $addr->phone }}</p>
                <p class="mb-0 text-muted small">{{ $addr->address_line }}, {{ $addr->city }}, {{ $addr->district }}</p>
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('customer.addresses.edit', $addr) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                    @if(!$addr->is_default)
                    <form action="{{ route('customer.addresses.default', $addr) }}" method="POST">
                        @csrf<button class="btn btn-sm btn-outline-primary">Set Default</button>
                    </form>
                    <form action="{{ route('customer.addresses.destroy', $addr) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-4 text-muted">No addresses saved. <a href="{{ route('customer.addresses.create') }}">Add one</a>.</div>
    @endforelse
</div>
@endsection
