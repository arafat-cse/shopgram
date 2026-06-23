@extends('layouts.app')
@section('title', 'Shopping Cart')
@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4">Shopping Cart</h2>
    <x-alert />

    @if($items->isEmpty())
    <div class="text-center py-5">
        <i class="bi bi-cart3 text-muted" style="font-size:4rem"></i>
        <h4 class="mt-3 text-muted">Your cart is empty</h4>
        <a href="{{ route('products.index') }}" class="btn btn-primary mt-2">Continue Shopping</a>
    </div>
    @else
    <div class="row g-4">
        <div class="col-lg-8">
            @foreach($items as $item)
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center g-3">
                        <div class="col-3 col-md-2">
                            <img src="{{ $item->product->thumbnail ? asset('storage/'.$item->product->thumbnail) : asset('images/no-image.png') }}"
                                 class="img-fluid rounded" alt="{{ $item->product->name }}" style="max-height:80px;object-fit:cover">
                        </div>
                        <div class="col-9 col-md-5">
                            <a href="{{ route('products.show', $item->product->slug) }}" class="text-dark text-decoration-none fw-semibold">{{ $item->product->name }}</a>
                            @if($item->variant)<br><small class="text-muted">{{ $item->variant->display_name }}</small>@endif
                            <div class="text-danger fw-bold mt-1">৳{{ number_format($item->price, 0) }}</div>
                        </div>
                        <div class="col-7 col-md-3">
                            <form action="{{ route('cart.update') }}" method="POST" class="d-flex align-items-center gap-2">
                                @csrf
                                <input type="hidden" name="cart_item_id" value="{{ $item->id }}">
                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="form-control form-control-sm text-center" style="width:70px" onchange="this.form.submit()">
                            </form>
                        </div>
                        <div class="col-5 col-md-2 text-end">
                            <div class="fw-bold">৳{{ number_format($item->subtotal, 0) }}</div>
                            <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="mt-1">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-3">
                <h5 class="fw-bold mb-3">Order Summary</h5>

                {{-- Coupon --}}
                @if($coupon)
                <div class="alert alert-success py-2 small d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-check-circle"></i> Coupon: {{ $coupon->code }}</span>
                    <form action="{{ route('cart.coupon.remove') }}" method="POST" class="d-inline">
                        @csrf<button class="btn btn-sm btn-link text-danger p-0">Remove</button>
                    </form>
                </div>
                @else
                <form action="{{ route('cart.coupon.apply') }}" method="POST" class="mb-3">
                    @csrf
                    <div class="input-group input-group-sm">
                        <input type="text" name="coupon_code" class="form-control" placeholder="Coupon code">
                        <button class="btn btn-outline-primary">Apply</button>
                    </div>
                    @error('coupon_code')<small class="text-danger">{{ $message }}</small>@enderror
                </form>
                @endif

                <table class="table table-sm">
                    <tr><td>Subtotal</td><td class="text-end">৳{{ number_format($subtotal, 0) }}</td></tr>
                    @if($discount > 0)
                    <tr class="text-success"><td>Discount</td><td class="text-end">-৳{{ number_format($discount, 0) }}</td></tr>
                    @endif
                    <tr class="fw-bold"><td>Total</td><td class="text-end text-danger">৳{{ number_format($subtotal - $discount, 0) }}</td></tr>
                </table>

                <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100">Proceed to Checkout</a>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 mt-2">Continue Shopping</a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
