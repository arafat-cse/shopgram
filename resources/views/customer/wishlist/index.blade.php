@extends('layouts.customer')
@section('title', 'My Wishlist')
@section('customer_content')
<h4 class="fw-bold mb-4">My Wishlist</h4>
@if($wishlist->count())
<div class="row g-3">
    @foreach($wishlist as $item)
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100 position-relative">
            <form action="{{ route('customer.wishlist.destroy', $item) }}" method="POST" class="position-absolute top-0 end-0 m-2" style="z-index:1">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger rounded-circle p-1 lh-1" style="width:28px;height:28px"><i class="bi bi-x"></i></button>
            </form>
            <a href="{{ route('products.show', $item->product->slug) }}">
                <img src="{{ $item->product->thumbnail ? asset('storage/'.$item->product->thumbnail) : asset('images/no-image.png') }}"
                     class="card-img-top" style="height:180px;object-fit:cover" alt="{{ $item->product->name }}">
            </a>
            <div class="card-body p-3">
                <h6 class="mb-1"><a href="{{ route('products.show', $item->product->slug) }}" class="text-dark text-decoration-none">{{ Str::limit($item->product->name, 40) }}</a></h6>
                <div class="text-danger fw-bold">৳{{ number_format($item->product->current_price, 0) }}</div>
                <form action="{{ route('cart.add') }}" method="POST" class="mt-2">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                    <button class="btn btn-primary btn-sm w-100">Add to Cart</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="mt-4">{{ $wishlist->links() }}</div>
@else
<div class="text-center py-5">
    <i class="bi bi-heart text-muted" style="font-size:3rem"></i>
    <p class="text-muted mt-2">Your wishlist is empty.</p>
    <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">Browse Products</a>
</div>
@endif
@endsection
