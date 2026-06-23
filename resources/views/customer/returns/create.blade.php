@extends('layouts.customer')
@section('title', 'Request Return')
@section('customer_content')
<h4 class="fw-bold mb-4">Request Return for Order #{{ $order->order_number }}</h4>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('customer.returns.store') }}" method="POST">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}">
            <x-alert />
            <div class="mb-3">
                <label class="form-label">Reason *</label>
                <textarea name="reason" class="form-control @error('reason') is-invalid @enderror" rows="4" required>{{ old('reason') }}</textarea>
                @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-warning">Submit Return Request</button>
            <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-outline-secondary ms-2">Cancel</a>
        </form>
    </div>
</div>
@endsection
