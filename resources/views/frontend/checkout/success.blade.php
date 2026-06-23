@extends('layouts.app')
@section('title', 'Order Placed!')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 text-center">
            <div class="card border-0 shadow-sm p-5">
                <div class="text-success mb-3" style="font-size:4rem"><i class="bi bi-check-circle-fill"></i></div>
                <h2 class="fw-bold">Order Placed Successfully!</h2>
                <p class="text-muted">Thank you for your order. We'll process it shortly.</p>
                <div class="alert alert-light border">
                    <strong>Order #{{ $order->order_number }}</strong><br>
                    <small class="text-muted">Total: ৳{{ number_format($order->total, 0) }}</small>
                </div>
                <div class="d-flex gap-3 justify-content-center mt-3">
                    <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-primary">View Order</a>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
