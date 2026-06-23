@extends('layouts.app')
@section('title', $product->name)
@section('content')
<div class="container py-4">
    <x-breadcrumb :items="[$product->category->name => route('category.show', $product->category->slug), $product->name => '#']" />

    <div class="row g-4">
        {{-- Product Images --}}
        <div class="col-md-5">
            <div class="card border-0">
                <img id="mainImage"
                     src="{{ $product->thumbnail ? asset('storage/'.$product->thumbnail) : asset('images/no-image.png') }}"
                     class="img-fluid rounded" alt="{{ $product->name }}" style="max-height:400px;object-fit:contain;width:100%">
            </div>
            @if($product->images->count())
            <div class="d-flex gap-2 mt-2 flex-wrap">
                @foreach($product->images as $img)
                <img src="{{ asset('storage/'.$img->image_path) }}"
                     class="img-thumbnail cursor-pointer" style="width:70px;height:70px;object-fit:cover;cursor:pointer"
                     onclick="document.getElementById('mainImage').src=this.src">
                @endforeach
            </div>
            @endif
        </div>

        {{-- Product Info --}}
        <div class="col-md-7">
            <h1 class="h3 fw-bold">{{ $product->name }}</h1>
            <div class="d-flex align-items-center gap-3 mb-2">
                <x-star-rating :rating="(int) $product->average_rating" />
                <span class="text-muted small">({{ $product->reviews->count() }} reviews)</span>
                @if($product->sku)<span class="text-muted small">SKU: {{ $product->sku }}</span>@endif
            </div>

            <div class="mb-3">
                @if($product->sale_price)
                    <span class="fs-3 fw-bold text-danger">৳{{ number_format($product->sale_price, 0) }}</span>
                    <span class="text-muted text-decoration-line-through ms-2">৳{{ number_format($product->regular_price, 0) }}</span>
                    <span class="badge bg-danger ms-2">{{ $product->discount_percent }}% OFF</span>
                @else
                    <span class="fs-3 fw-bold text-danger">৳{{ number_format($product->regular_price, 0) }}</span>
                @endif
            </div>

            @if(!$product->isInStock())
                <span class="badge bg-danger fs-6 mb-3">Out of Stock</span>
            @elseif($product->isLowStock())
                <span class="badge bg-warning text-dark fs-6 mb-3">Only {{ $product->stock_quantity }} left!</span>
            @else
                <span class="badge bg-success mb-3">In Stock</span>
            @endif

            @if($product->short_description)
                <p class="text-muted">{{ $product->short_description }}</p>
            @endif

            {{-- Variant Selector --}}
            @if($product->variants->count())
            <div class="mb-3">
                <label class="form-label fw-semibold">Select Variant</label>
                <select class="form-select" id="variantSelect">
                    <option value="">-- Select --</option>
                    @foreach($product->variants as $v)
                    <option value="{{ $v->id }}" data-price="{{ $v->price ?? '' }}" data-stock="{{ $v->stock_quantity }}">
                        {{ $v->display_name }} {{ $v->price ? '- ৳'.number_format($v->price,0) : '' }}
                    </option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Quantity --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Quantity</label>
                <div class="input-group" style="max-width:130px">
                    <button class="btn btn-outline-secondary" type="button" id="qtyMinus">-</button>
                    <input type="number" class="form-control text-center" value="1" min="1" max="{{ $product->stock_quantity }}" id="qtyInput">
                    <button class="btn btn-outline-secondary" type="button" id="qtyPlus">+</button>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex gap-3 mb-3 flex-wrap">
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="variant_id" id="hiddenVariant">
                    <input type="hidden" name="quantity" id="hiddenQty" value="1">
                    <button type="submit" class="btn btn-primary btn-lg" {{ !$product->isInStock() ? 'disabled' : '' }}>
                        <i class="bi bi-cart-plus"></i> Add to Cart
                    </button>
                </form>
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="variant_id" id="buyVariant">
                    <input type="hidden" name="quantity" id="buyQty" value="1">
                    <input type="hidden" name="buy_now" value="1">
                    <button type="submit" class="btn btn-warning btn-lg" {{ !$product->isInStock() ? 'disabled' : '' }}>
                        <i class="bi bi-zap"></i> Buy Now
                    </button>
                </form>
                <form action="{{ route('customer.wishlist.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button type="submit" class="btn btn-outline-danger btn-lg">
                        <i class="bi bi-heart"></i>
                    </button>
                </form>
            </div>

            {{-- Share --}}
            <div class="d-flex gap-2 align-items-center">
                <span class="small text-muted">Share:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-facebook"></i></a>
                <a href="https://wa.me/?text={{ urlencode($product->name . ' ' . url()->current()) }}" target="_blank" class="btn btn-sm btn-outline-success"><i class="bi bi-whatsapp"></i></a>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="mt-5">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#desc">Description</button></li>
            @if($product->specification)<li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#spec">Specifications</button></li>@endif
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviews">Reviews ({{ $product->reviews->count() }})</button></li>
        </ul>
        <div class="tab-content border border-top-0 p-4 rounded-bottom">
            <div class="tab-pane fade show active" id="desc">{!! nl2br(e($product->description)) !!}</div>
            @if($product->specification)<div class="tab-pane fade" id="spec">{!! nl2br(e($product->specification)) !!}</div>@endif
            <div class="tab-pane fade" id="reviews">
                @forelse($product->reviews as $review)
                <div class="border-bottom pb-3 mb-3">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $review->user->name }}</strong>
                        <x-star-rating :rating="$review->rating" />
                    </div>
                    <p class="mb-0 text-muted small">{{ $review->comment }}</p>
                </div>
                @empty
                <p class="text-muted">No reviews yet.</p>
                @endforelse

                @auth
                <hr>
                <h6>Write a Review</h6>
                <form action="{{ route('customer.reviews.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="mb-2">
                        <label class="form-label">Rating</label>
                        <select name="rating" class="form-select form-select-sm" style="max-width:120px">
                            @for($i=5;$i>=1;$i--)<option value="{{ $i }}">{{ $i }} Star</option>@endfor
                        </select>
                    </div>
                    <div class="mb-2">
                        <textarea name="comment" class="form-control" rows="3" placeholder="Your review..."></textarea>
                    </div>
                    <button class="btn btn-primary btn-sm">Submit Review</button>
                </form>
                @endauth
            </div>
        </div>
    </div>

    {{-- Related Products --}}
    @if($related->count())
    <section class="mt-5">
        <h3 class="section-title">Related Products</h3>
        <div class="row g-3">
            @foreach($related as $product)
            <div class="col-6 col-md-4 col-lg-2">
                <x-product-card :product="$product" />
            </div>
            @endforeach
        </div>
    </section>
    @endif
</div>
@push('scripts')
<script>
const qtyInput = document.getElementById('qtyInput');
document.getElementById('qtyMinus').addEventListener('click', () => { if (qtyInput.value > 1) { qtyInput.value--; syncQty(); } });
document.getElementById('qtyPlus').addEventListener('click', () => { qtyInput.value++; syncQty(); });
qtyInput.addEventListener('change', syncQty);

const variantSelect = document.getElementById('variantSelect');
function syncQty() {
    const q = qtyInput.value;
    document.getElementById('hiddenQty').value = q;
    document.getElementById('buyQty').value = q;
}
if (variantSelect) {
    variantSelect.addEventListener('change', function() {
        const v = this.value;
        document.getElementById('hiddenVariant').value = v;
        document.getElementById('buyVariant').value = v;
    });
}
</script>
@endpush
@endsection
