@props(['product'])
<div class="product-card card h-100 border-0 shadow-sm position-relative">
    @if($product->discount_percent > 0)
        <span class="badge badge-discount badge-pill text-white" style="background:var(--primary);font-size:.75rem;padding:4px 8px">-{{ $product->discount_percent }}%</span>
    @endif
    <a href="{{ route('products.show', $product->slug) }}">
        <img src="{{ $product->thumbnail ? asset('storage/'.$product->thumbnail) : asset('images/no-image.png') }}"
             class="card-img-top" alt="{{ $product->name }}" style="height:200px;object-fit:cover">
    </a>
    <div class="card-body d-flex flex-column p-3">
        <small class="text-muted">{{ $product->category->name ?? '' }}</small>
        <h6 class="card-title mb-1 flex-grow-1">
            <a href="{{ route('products.show', $product->slug) }}" class="text-dark text-decoration-none">{{ Str::limit($product->name, 50) }}</a>
        </h6>
        <div class="d-flex align-items-center gap-2 mb-2">
            @if($product->sale_price)
                <span class="price-sale">৳{{ number_format($product->sale_price, 0) }}</span>
                <span class="price-original">৳{{ number_format($product->regular_price, 0) }}</span>
            @else
                <span class="price-sale">৳{{ number_format($product->regular_price, 0) }}</span>
            @endif
        </div>
        @if(!$product->isInStock())
            <span class="badge bg-secondary mb-2">Out of Stock</span>
        @elseif($product->isLowStock())
            <span class="badge bg-warning text-dark mb-2">Low Stock</span>
        @endif
        <div class="d-flex gap-2">
            <form action="{{ route('cart.add') }}" method="POST" class="flex-grow-1">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit" class="btn btn-primary btn-sm w-100" {{ !$product->isInStock() ? 'disabled' : '' }}>
                    <i class="bi bi-cart-plus"></i> Add
                </button>
            </form>
            <form action="{{ route('customer.wishlist.store') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-heart"></i></button>
            </form>
        </div>
    </div>
</div>
