@extends('layouts.app')
@section('title', $product->name)
@push('styles')
<style>
    .product-gallery-shell {
        background: #fff;
        border: 1px solid #eef0f4;
        border-radius: 10px;
        padding: 14px;
        box-shadow: 0 12px 34px rgba(15, 23, 42, .06);
    }

    .product-main-image-wrap {
        position: relative;
        background: #fafafa;
        border-radius: 8px;
        aspect-ratio: 1 / 1;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        cursor: zoom-in;
    }

    .product-main-image-wrap.has-video-active {
        cursor: default;
    }

    .product-main-image-wrap img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 14px;
        transform-origin: center;
        transition: opacity .18s ease, transform .22s ease;
        will-change: transform;
        pointer-events: none;
    }

    @media (hover: hover) and (pointer: fine) {
        .product-main-image-wrap:hover img {
            transform: scale(1.85);
        }
    }

    .product-main-image-wrap.touch-zoomed {
        cursor: zoom-out;
    }
    .product-main-image-wrap.touch-zoomed img {
        transform: scale(2);
        transition: transform .2s ease;
    }

    .product-main-image-wrap img.is-changing {
        opacity: .45;
        transform: scale(.985);
    }

    .product-thumb-strip {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(72px, 1fr));
        gap: 10px;
        margin-top: 12px;
    }

    .product-thumb-btn {
        border: 1px solid #e5e7eb;
        background: #fff;
        border-radius: 8px;
        padding: 4px;
        height: 78px;
        cursor: pointer;
        transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
    }

    .product-thumb-btn:hover,
    .product-thumb-btn.active {
        border-color: #e91e63;
        box-shadow: 0 8px 20px rgba(233, 30, 99, .12);
        transform: translateY(-1px);
    }

    .product-thumb-btn img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 6px;
    }

    @media (max-width: 575px) {
        .product-gallery-shell { padding: 10px; }
        .product-thumb-strip { grid-template-columns: repeat(auto-fill, minmax(58px, 1fr)); gap: 8px; }
        .product-thumb-btn { height: 64px; }
    }

    .social-proof-badge {
        font-size: .8rem;
        font-weight: 500;
        color: #374151;
        background: #f9fafb !important;
        border-color: #e5e7eb !important;
    }
    .social-proof-badge .bi { font-size: .85rem; }

    #stickyAtcBar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 1040;
        background: #fff;
        border-top: 1px solid #e5e7eb;
        box-shadow: 0 -4px 20px rgba(0,0,0,.1);
        padding: 10px 0;
        transform: translateY(100%);
        transition: transform .28s cubic-bezier(.4,0,.2,1);
    }
    #stickyAtcBar.visible { transform: translateY(0); }
    #stickyAtcBar .sticky-thumb {
        width: 44px;
        height: 44px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #eee;
        flex-shrink: 0;
    }
    #stickyAtcBar .sticky-name {
        font-weight: 600;
        font-size: .9rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
    }
    @media (max-width: 575px) {
        #stickyAtcBar .sticky-name { max-width: 120px; }
    }
    .swatch-btn {
        font-weight: 500;
        font-size: 0.9rem;
        padding: 6px 14px;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    .swatch-btn.active {
        border-color: var(--primary) !important;
        background-color: rgba(233, 30, 99, 0.08) !important;
        color: var(--primary) !important;
        box-shadow: 0 0 0 1px var(--primary);
    }
</style>
@endpush
@section('content')
<div class="container py-4">
    <x-breadcrumb :items="[$product->category->name => route('category.show', $product->category->slug), $product->name => '#']" />

    <div class="row g-4">
        {{-- Product Images --}}
        <div class="col-md-5">
            @php
                $videoId = '';
                if ($product->video_url) {
                    if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $product->video_url, $match)) {
                        $videoId = $match[1];
                    }
                }

                $galleryImages = collect();
                if ($product->thumbnail) {
                    $galleryImages->push([
                        'url' => asset('storage/'.$product->thumbnail),
                        'alt' => $product->name,
                    ]);
                }

                foreach ($product->images as $image) {
                    $galleryImages->push([
                        'url' => asset('storage/'.$image->image_path),
                        'alt' => $product->name,
                    ]);
                }

                if ($galleryImages->isEmpty()) {
                    $galleryImages->push([
                        'url' => asset('images/no-image.png'),
                        'alt' => $product->name,
                    ]);
                }
            @endphp

            <div class="product-gallery-shell">
                <div class="product-main-image-wrap">
                    <img id="mainImage" src="{{ $galleryImages->first()['url'] }}" alt="{{ $product->name }}">
                    @if($videoId)
                        <div id="videoContainer" style="display: none; position: absolute; inset: 0; width: 100%; height: 100%; background: #000;">
                            <iframe id="videoIframe" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen style="width: 100%; height: 100%;"></iframe>
                        </div>
                    @endif
                </div>

                @if(($galleryImages->count() + ($videoId ? 1 : 0)) > 1)
                    <div class="product-thumb-strip" aria-label="Product image gallery">
                        @if($videoId)
                            <button type="button"
                                    class="product-thumb-btn"
                                    data-is-video="true"
                                    data-video-id="{{ $videoId }}"
                                    aria-label="View product video">
                                <div class="position-relative d-flex align-items-center justify-content-center" style="width: 100%; height: 100%;">
                                    <img src="https://img.youtube.com/vi/{{ $videoId }}/hqdefault.jpg" alt="Video Thumbnail" style="width: 100%; height: 100%; object-fit: cover; border-radius: 6px; filter: brightness(0.7)">
                                    <i class="bi bi-play-circle-fill text-white position-absolute" style="font-size: 1.6rem; z-index: 2;"></i>
                                </div>
                            </button>
                        @endif
                        @foreach($galleryImages as $galleryImage)
                            <button type="button"
                                    class="product-thumb-btn {{ ($loop->first && !$videoId) ? 'active' : '' }}"
                                    data-image="{{ $galleryImage['url'] }}"
                                    aria-label="View image {{ $loop->iteration }}">
                                <img src="{{ $galleryImage['url'] }}" alt="{{ $galleryImage['alt'] }}">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Product Info --}}
        <div class="col-md-7">
            <h1 class="h3 fw-bold">{{ $product->name }}</h1>
            <div class="d-flex align-items-center gap-3 mb-2">
                <x-star-rating :rating="(int) $product->average_rating" />
                <span class="text-muted small">({{ $product->reviews->count() }} reviews)</span>
                @if($product->sku)<span class="text-muted small">SKU: {{ $product->sku }}</span>@endif
            </div>

            <div class="mb-3" id="priceSection">
                @if($product->sale_price)
                    <span class="fs-3 fw-bold text-danger" id="mainPrice">৳{{ number_format($product->sale_price, 0) }}</span>
                    <span class="text-muted text-decoration-line-through ms-2" id="originalPrice">৳{{ number_format($product->regular_price, 0) }}</span>
                    <span class="badge bg-danger ms-2" id="discountBadge">{{ $product->discount_percent }}% OFF</span>
                @else
                    <span class="fs-3 fw-bold text-danger" id="mainPrice">৳{{ number_format($product->regular_price, 0) }}</span>
                    <span class="text-muted text-decoration-line-through ms-2 d-none" id="originalPrice"></span>
                    <span class="badge bg-danger ms-2 d-none" id="discountBadge"></span>
                @endif
            </div>

            <span id="stockBadge" class="badge {{ !$product->isInStock() ? 'bg-danger' : ($product->isLowStock() ? 'bg-warning text-dark' : 'bg-success') }} fs-6 mb-3">
                {{ !$product->isInStock() ? 'Out of Stock' : ($product->isLowStock() ? 'Only '.$product->stock_quantity.' left!' : 'In Stock') }}
            </span>

            {{-- Social Proof --}}
            <div class="d-flex flex-wrap gap-2 mb-3" id="socialProofBadges">
                <span class="badge rounded-pill text-bg-light border px-3 py-2 social-proof-badge">
                    <i class="bi bi-eye text-danger me-1"></i>
                    <span id="viewingCount">–</span> people viewing now
                </span>
                @if($soldLast24h > 0)
                <span class="badge rounded-pill text-bg-light border px-3 py-2 social-proof-badge">
                    <i class="bi bi-bag-check text-success me-1"></i>
                    {{ $soldLast24h }} sold in last 24h
                </span>
                @endif
            </div>

            @if($product->short_description)
                <p class="text-muted">{{ $product->short_description }}</p>
            @endif

            {{-- Variant Selector Swatches --}}
            @if($product->variants->count())
                @php
                    $uniqueColors = $product->variants->pluck('color')->filter()->unique();
                    $uniqueSizes = $product->variants->pluck('size')->filter()->unique();
                    $uniqueOptions = $product->variants->pluck('custom_option')->filter()->unique();
                @endphp

                @if($uniqueColors->count())
                    <div class="mb-3">
                        <label class="form-label fw-bold small mb-1">Color</label>
                        <div class="d-flex flex-wrap gap-2" id="colorContainer">
                            @foreach($uniqueColors as $color)
                                @php
                                    $colorMap = [
                                        'Black' => '#000000', 'White' => '#ffffff', 'Red' => '#ff0000', 'Blue' => '#0000ff',
                                        'Green' => '#4caf50', 'Purple' => '#800080', 'Yellow' => '#ffff00', 'Pink' => '#ffc0cb',
                                        'Navy Blue' => '#000080', 'Royal Blue' => '#4169e1', 'Magenta' => '#ff00ff',
                                        'Awesome Iceblue' => '#e0f7fa', 'Awesome Navy' => '#1a237e', 'Dark Blue' => '#00008b',
                                        'Off-White' => '#faf9f6'
                                    ];
                                    $hex = $colorMap[$color] ?? null;
                                @endphp
                                <button type="button" class="btn btn-outline-secondary swatch-btn color-swatch" data-color="{{ $color }}">
                                    @if($hex)
                                        <span class="color-dot" style="background-color: {{ $hex }}; border: 1px solid #ccc; display: inline-block; width: 14px; height: 14px; border-radius: 50%; margin-right: 6px; vertical-align: middle;"></span>
                                    @endif
                                    {{ $color }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($uniqueSizes->count())
                    <div class="mb-3">
                        <label class="form-label fw-bold small mb-1">Size</label>
                        <div class="d-flex flex-wrap gap-2" id="sizeContainer">
                            @foreach($uniqueSizes as $size)
                                <button type="button" class="btn btn-outline-secondary swatch-btn size-swatch" data-size="{{ $size }}">
                                    {{ $size }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($uniqueOptions->count())
                    <div class="mb-3">
                        <label class="form-label fw-bold small mb-1">Option</label>
                        <div class="d-flex flex-wrap gap-2" id="optionContainer">
                            @foreach($uniqueOptions as $opt)
                                <button type="button" class="btn btn-outline-secondary swatch-btn option-swatch" data-option="{{ $opt }}">
                                    {{ $opt }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
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
                    <button type="submit" id="mainAtcBtn" class="btn btn-primary btn-lg" {{ !$product->isInStock() ? 'disabled' : '' }}>
                        <i class="bi bi-cart-plus"></i> Add to Cart
                    </button>
                </form>
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="variant_id" id="buyVariant">
                    <input type="hidden" name="quantity" id="buyQty" value="1">
                    <input type="hidden" name="buy_now" value="1">
                    <button type="submit" id="mainBuyBtn" class="btn btn-warning btn-lg" {{ !$product->isInStock() ? 'disabled' : '' }}>
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
                    @if($canReview)
                    <hr>
                    <h6>Write a Review</h6>
                    <form action="{{ route('customer.reviews.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="order_id" value="{{ $unreviewedOrder->id }}">
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
                    @else
                    <hr>
                    <div class="alert alert-light border text-muted py-2 px-3 small">
                        <i class="bi bi-info-circle me-1"></i> You can only review this product if you have purchased it and the order has been delivered.
                    </div>
                    @endif
                @else
                <hr>
                <div class="alert alert-light border text-muted py-2 px-3 small">
                    <i class="bi bi-info-circle me-1"></i> Please <a href="{{ route('login') }}">login</a> to write a review.
                </div>
                @endauth
            </div>
        </div>
    </div>

    {{-- Recently Viewed Products --}}
    @if($recentProducts->count())
    <section class="mt-5">
        <h3 class="section-title">Recently Viewed Products</h3>
        <div class="row g-3">
            @foreach($recentProducts as $product)
            <div class="col-6 col-md-4 col-lg-2">
                <x-product-card :product="$product" />
            </div>
            @endforeach
        </div>
    </section>
    @endif

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
{{-- Sticky Add-to-Cart Bar --}}
@if($product->isInStock())
<div id="stickyAtcBar" aria-hidden="true">
    <div class="container">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ $product->thumbnail ? asset('storage/'.$product->thumbnail) : asset('images/no-image.png') }}"
                 alt="{{ $product->name }}" class="sticky-thumb">
            <div class="flex-grow-1 min-w-0">
                <div class="sticky-name">{{ $product->name }}</div>
                <div class="text-danger fw-bold small">
                    ৳{{ number_format($product->sale_price ?? $product->regular_price, 0) }}
                    @if($product->sale_price)<span class="text-muted text-decoration-line-through ms-1 fw-normal">৳{{ number_format($product->regular_price, 0) }}</span>@endif
                </div>
            </div>
            <form action="{{ route('cart.add') }}" method="POST" class="flex-shrink-0">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="variant_id" class="sticky-variant-input">
                <input type="hidden" name="quantity" class="sticky-qty-input" value="1">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-cart-plus"></i> Add to Cart
                </button>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Image Lightbox Modal --}}
<div class="modal fade" id="imageLightboxModal" tabindex="-1" aria-hidden="true" style="z-index: 2000;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 bg-transparent">
            <div class="modal-header border-0 p-0 justify-content-end">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="font-size: 1.5rem;"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="lightboxImage" src="" class="img-fluid rounded shadow" style="max-height: 85vh; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Seeded "viewing now" — deterministic from product_id so it's consistent per product
(function() {
    const seed = {{ $product->id }};
    const base = ((seed * 1103515245 + 12345) >>> 0) % 23 + 6; // 6–28
    let current = base;
    const el = document.getElementById('viewingCount');
    if (el) {
        el.textContent = current;
        setInterval(function() {
            const delta = Math.random() < 0.5 ? 1 : -1;
            current = Math.max(3, Math.min(38, current + delta));
            el.textContent = current;
        }, 8000);
    }
})();

const qtyInput = document.getElementById('qtyInput');
function syncQty() {
    const q = qtyInput.value;
    document.getElementById('hiddenQty').value = q;
    document.getElementById('buyQty').value = q;
    document.querySelectorAll('.sticky-qty-input').forEach(el => el.value = q);
}
document.getElementById('qtyMinus').addEventListener('click', () => { if (qtyInput.value > 1) { qtyInput.value--; syncQty(); } });
document.getElementById('qtyPlus').addEventListener('click', () => {
    const max = parseInt(qtyInput.getAttribute('max')) || 999;
    if (parseInt(qtyInput.value) < max) {
        qtyInput.value++;
        syncQty();
    }
});
qtyInput.addEventListener('change', () => {
    const max = parseInt(qtyInput.getAttribute('max')) || 999;
    if (qtyInput.value < 1) qtyInput.value = 1;
    if (qtyInput.value > max) qtyInput.value = max;
    syncQty();
});

const mainImage = document.getElementById('mainImage');
const mainImageWrap = document.querySelector('.product-main-image-wrap');
const videoContainer = document.getElementById('videoContainer');
const videoIframe = document.getElementById('videoIframe');

if (mainImage && mainImageWrap) {
    // Desktop: mouse-follow zoom
    if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        mainImageWrap.addEventListener('mousemove', (event) => {
            if (mainImage.style.display === 'none') return;
            const rect = mainImageWrap.getBoundingClientRect();
            const x = ((event.clientX - rect.left) / rect.width) * 100;
            const y = ((event.clientY - rect.top) / rect.height) * 100;
            mainImage.style.transformOrigin = `${x}% ${y}%`;
        });
        mainImageWrap.addEventListener('mouseleave', () => {
            mainImage.style.transformOrigin = 'center';
        });
    }

    // Mobile: tap-to-zoom at touch point
    let touchZoomed = false;
    mainImageWrap.addEventListener('touchstart', (e) => {
        const touch = e.touches[0];
        const rect = mainImageWrap.getBoundingClientRect();
        const x = ((touch.clientX - rect.left) / rect.width) * 100;
        const y = ((touch.clientY - rect.top) / rect.height) * 100;
        if (!touchZoomed) {
            mainImage.style.transformOrigin = `${x}% ${y}%`;
            mainImageWrap.classList.add('touch-zoomed');
            touchZoomed = true;
        } else {
            mainImage.style.transformOrigin = 'center';
            mainImageWrap.classList.remove('touch-zoomed');
            touchZoomed = false;
        }
        e.preventDefault();
    }, { passive: false });
}

// Lightbox Trigger
const lightboxImage = document.getElementById('lightboxImage');
const lightboxModal = new bootstrap.Modal(document.getElementById('imageLightboxModal'));

if (mainImageWrap) {
    mainImageWrap.addEventListener('click', () => {
        if (videoContainer && videoContainer.style.display === 'block') return;
        lightboxImage.src = mainImage.src;
        lightboxModal.show();
    });
}

document.querySelectorAll('.product-thumb-btn').forEach((button) => {
    button.addEventListener('click', () => {
        document.querySelectorAll('.product-thumb-btn').forEach((item) => item.classList.remove('active'));
        button.classList.add('active');

        if (button.dataset.isVideo === 'true') {
            const videoId = button.dataset.videoId;
            if (videoContainer && videoIframe) {
                videoIframe.src = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
                videoContainer.style.display = 'block';
                if (mainImage) mainImage.style.display = 'none';
                if (mainImageWrap) mainImageWrap.classList.add('has-video-active');
            }
        } else {
            if (videoContainer && videoIframe) {
                videoContainer.style.display = 'none';
                videoIframe.src = '';
            }
            if (mainImage) {
                mainImage.style.display = 'block';
                if (mainImageWrap) mainImageWrap.classList.remove('has-video-active');
                
                const nextImage = button.dataset.image;
                if (mainImage.src !== nextImage) {
                    mainImage.classList.add('is-changing');
                    window.setTimeout(() => {
                        mainImage.src = nextImage;
                        mainImage.style.transformOrigin = 'center';
                        mainImage.classList.remove('is-changing');
                    }, 120);
                }
            }
        }
    });
});

// Swatches & Variants
(function() {
    const variants = @json($product->variants);
    const originalPrice = {{ $product->sale_price ?? $product->regular_price }};
    const regularPrice = {{ $product->regular_price }};
    const isDiscounted = {{ $product->sale_price ? 'true' : 'false' }};
    const discountPercent = {{ $product->discount_percent ?? 0 }};
    const originalStock = {{ $product->stock_quantity }};

    const hasColors = document.querySelectorAll('.color-swatch').length > 0;
    const hasSizes = document.querySelectorAll('.size-swatch').length > 0;
    const hasOptions = document.querySelectorAll('.option-swatch').length > 0;

    let selectedColor = null;
    let selectedSize = null;
    let selectedOption = null;

    const colorSwatches = document.querySelectorAll('.color-swatch');
    const sizeSwatches = document.querySelectorAll('.size-swatch');
    const optionSwatches = document.querySelectorAll('.option-swatch');

    const mainPriceEl = document.getElementById('mainPrice');
    const originalPriceEl = document.getElementById('originalPrice');
    const discountBadgeEl = document.getElementById('discountBadge');
    const stockBadgeEl = document.getElementById('stockBadge');
    const hiddenVariantInput = document.getElementById('hiddenVariant');
    const buyVariantInput = document.getElementById('buyVariant');
    const mainAtcBtn = document.getElementById('mainAtcBtn');
    const mainBuyBtn = document.getElementById('mainBuyBtn');

    const updateVariant = () => {
        if ((hasColors && !selectedColor) || (hasSizes && !selectedSize) || (hasOptions && !selectedOption)) {
            hiddenVariantInput.value = '';
            buyVariantInput.value = '';
            document.querySelectorAll('.sticky-variant-input').forEach(el => el.value = '');
            mainPriceEl.textContent = '৳' + originalPrice.toLocaleString();
            if (isDiscounted) {
                originalPriceEl.classList.remove('d-none');
                discountBadgeEl.classList.remove('d-none');
            } else {
                originalPriceEl.classList.add('d-none');
                discountBadgeEl.classList.add('d-none');
            }
            stockBadgeEl.textContent = originalStock > 0 ? (originalStock <= 5 ? `Only ${originalStock} left!` : 'In Stock') : 'Out of Stock';
            stockBadgeEl.className = 'badge ' + (originalStock > 0 ? (originalStock <= 5 ? 'bg-warning text-dark' : 'bg-success') : 'bg-danger') + ' fs-6 mb-3';
            qtyInput.setAttribute('max', originalStock);
            if (originalStock > 0) {
                mainAtcBtn.removeAttribute('disabled');
                mainBuyBtn.removeAttribute('disabled');
            } else {
                mainAtcBtn.setAttribute('disabled', 'true');
                mainBuyBtn.setAttribute('disabled', 'true');
            }
            return;
        }

        const match = variants.find(v => {
            return (!hasColors || v.color === selectedColor) &&
                   (!hasSizes || v.size === selectedSize) &&
                   (!hasOptions || v.custom_option === selectedOption);
        });

        if (match) {
            hiddenVariantInput.value = match.id;
            buyVariantInput.value = match.id;
            document.querySelectorAll('.sticky-variant-input').forEach(el => el.value = match.id);

            const priceToUse = match.price ? parseFloat(match.price) : originalPrice;
            mainPriceEl.textContent = '৳' + priceToUse.toLocaleString();
            
            if (match.price && isDiscounted) {
                const calcOrig = Math.round(priceToUse / (1 - (discountPercent / 100)));
                originalPriceEl.textContent = '৳' + calcOrig.toLocaleString();
                originalPriceEl.classList.remove('d-none');
            } else {
                originalPriceEl.classList.add('d-none');
                discountBadgeEl.classList.add('d-none');
            }

            const stock = match.stock_quantity;
            qtyInput.setAttribute('max', stock);
            if (parseInt(qtyInput.value) > stock) {
                qtyInput.value = stock > 0 ? stock : 1;
                syncQty();
            }

            if (stock > 0) {
                stockBadgeEl.textContent = stock <= 5 ? `Only ${stock} left!` : 'In Stock';
                stockBadgeEl.className = 'badge bg-success fs-6 mb-3';
                mainAtcBtn.removeAttribute('disabled');
                mainBuyBtn.removeAttribute('disabled');
            } else {
                stockBadgeEl.textContent = 'Out of Stock';
                stockBadgeEl.className = 'badge bg-danger fs-6 mb-3';
                mainAtcBtn.setAttribute('disabled', 'true');
                mainBuyBtn.setAttribute('disabled', 'true');
            }
        } else {
            hiddenVariantInput.value = '';
            buyVariantInput.value = '';
            document.querySelectorAll('.sticky-variant-input').forEach(el => el.value = '');
            stockBadgeEl.textContent = 'Unavailable';
            stockBadgeEl.className = 'badge bg-secondary fs-6 mb-3';
            mainAtcBtn.setAttribute('disabled', 'true');
            mainBuyBtn.setAttribute('disabled', 'true');
        }
    };

    colorSwatches.forEach(swatch => {
        swatch.addEventListener('click', function() {
            colorSwatches.forEach(s => s.classList.remove('active'));
            this.classList.add('active');
            selectedColor = this.dataset.color;
            updateVariant();
        });
    });

    sizeSwatches.forEach(swatch => {
        swatch.addEventListener('click', function() {
            sizeSwatches.forEach(s => s.classList.remove('active'));
            this.classList.add('active');
            selectedSize = this.dataset.size;
            updateVariant();
        });
    });

    optionSwatches.forEach(swatch => {
        swatch.addEventListener('click', function() {
            optionSwatches.forEach(s => s.classList.remove('active'));
            this.classList.add('active');
            selectedOption = this.dataset.option;
            updateVariant();
        });
    });
})();

// Sticky ATC bar — show when main ATC button scrolls out of view
const stickyBar = document.getElementById('stickyAtcBar');
const mainAtcBtnElement = document.getElementById('mainAtcBtn');
if (stickyBar && mainAtcBtnElement) {
    qtyInput.addEventListener('input', syncQty);

    const obs = new IntersectionObserver(entries => {
        const hidden = !entries[0].isIntersecting;
        stickyBar.classList.toggle('visible', hidden);
        stickyBar.setAttribute('aria-hidden', String(!hidden));
    }, { threshold: 0.2 });
    obs.observe(mainAtcBtnElement);
}
</script>
@endpush
@endsection
