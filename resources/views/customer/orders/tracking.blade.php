@extends('layouts.app')
@section('title', 'Track Order')
@section('content')
<div class="container py-4">
<h4 class="fw-bold mb-4">Track Your Order</h4>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('order.track') }}" method="POST">
            @csrf
            <div class="row g-2">
                <div class="col-md-8">
                    <input type="text" name="order_number" class="form-control @error('order_number') is-invalid @enderror"
                           placeholder="Enter Order Number (e.g. SG-ABC123)" value="{{ old('order_number') }}" required>
                    @error('order_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">Track</button>
                </div>
            </div>
        </form>
    </div>
</div>

@isset($order)
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong>Order #{{ $order->order_number }}</strong>
        <x-order-status-badge :status="$order->status" />
    </div>
    <div class="card-body">
        @foreach($order->statusHistories as $history)
        <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
            <div class="text-muted small" style="min-width:130px">{{ $history->created_at->format('d M Y H:i') }}</div>
            <div>
                <x-order-status-badge :status="$history->status" />
                @if($history->note)<p class="text-muted small mb-0 mt-1">{{ $history->note }}</p>@endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endisset
</div>
@endsection
