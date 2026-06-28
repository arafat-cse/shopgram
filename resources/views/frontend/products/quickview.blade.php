<div class="modal-header border-0 pb-0">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body pt-0">
    <div class="row g-4">
        {{-- Gallery Column --}}
        <div class="col-md-6">
            <div class="qv-gallery-wrap">
                <div class="qv-main-img-box rounded bg-light d-flex align-items-center justify-content-center overflow-hidden position-relative" style="aspect-ratio: 1/1;">
                    @php
                        $galleryImages = collect();
                        if ($product->thumbnail) {
                            $galleryImages->push(asset('storage/'.$product->thumbnail));
                        }
                        foreach ($product->images as $img) {
                            $galleryImages->push(asset('storage/'.$img->image_path));
                        }
                        if ($galleryImages->isEmpty()) {
                            $galleryImages->push(asset('images/no-image.png'));
                        }
                    @endphp
                    <img id="qvMainImage" src="{{ $galleryImages->first() }}" alt="{{ $product->name }}" class="w-100 h-100" style="object-fit: contain; padding: 10px;">
                </div>
                
                @if($galleryImages->count() > 1)
                    <div class="d-flex flex-wrap gap-2 mt-2 justify-content-center">
                        @foreach($galleryImages as $imgUrl)
                            <button type="button" class="btn p-0 border qv-thumb-btn {{ $loop->first ? 'active border-primary' : '' }}" data-image="{{ $imgUrl }}" style="width: 50px; height: 50px; overflow: hidden; border-radius: 6px;">
                                <img src="{{ $imgUrl }}" alt="Thumb" class="w-100 h-100" style="object-fit: cover;">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Info Column --}}
        <div class="col-md-6 d-flex flex-column justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-semibold">{{ $product->category->name ?? '' }}</small>
                <h4 class="fw-bold mt-1 mb-2">{{ $product->name }}</h4>
                
                {{-- Rating --}}
                @php $reviewCount = $product->reviews()->count(); $avgRating = $product->average_rating; @endphp
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="text-warning small">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi {{ $i <= round($avgRating) ? 'bi-star-fill' : 'bi-star' }}"></i>
                        @endfor
                    </div>
                    <span class="text-muted small">({{ $reviewCount }} reviews)</span>
                </div>

                {{-- Price --}}
                <div class="mb-3" id="qvPriceSection">
                    @if($product->sale_price)
                        <span class="fs-4 fw-bold text-danger" id="qvMainPrice">৳{{ number_format($product->sale_price, 0) }}</span>
                        <span class="text-muted text-decoration-line-through ms-2 small" id="qvOriginalPrice">৳{{ number_format($product->regular_price, 0) }}</span>
                        <span class="badge bg-danger ms-2" id="qvDiscountBadge">{{ $product->discount_percent }}% OFF</span>
                    @else
                        <span class="fs-4 fw-bold text-danger" id="qvMainPrice">৳{{ number_format($product->regular_price, 0) }}</span>
                        <span class="text-muted text-decoration-line-through ms-2 d-none" id="qvOriginalPrice"></span>
                        <span class="badge bg-danger ms-2 d-none" id="qvDiscountBadge"></span>
                    @endif
                </div>

                {{-- Stock --}}
                <div class="mb-3">
                    <span id="qvStockBadge" class="badge {{ !$product->isInStock() ? 'bg-danger' : ($product->isLowStock() ? 'bg-warning text-dark' : 'bg-success') }}">
                        {{ !$product->isInStock() ? 'Out of Stock' : ($product->isLowStock() ? 'Only '.$product->stock_quantity.' left!' : 'In Stock') }}
                    </span>
                </div>

                @if($product->short_description)
                    <p class="text-muted small mb-3">{{ Str::limit($product->short_description, 180) }}</p>
                @endif

                {{-- Swatches --}}
                @php
                    $uniqueColors = $product->variants->pluck('color')->filter()->unique();
                    $uniqueSizes = $product->variants->pluck('size')->filter()->unique();
                    $uniqueOptions = $product->variants->pluck('custom_option')->filter()->unique();
                @endphp

                @if($uniqueColors->count())
                    <div class="mb-2">
                        <label class="form-label fw-bold small mb-1">Color:</label>
                        <div class="d-flex flex-wrap gap-2" id="qvColorContainer">
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
                                <button type="button" class="btn btn-sm btn-outline-secondary qv-swatch-btn qv-color-swatch" data-color="{{ $color }}">
                                    @if($hex)
                                        <span class="color-dot" style="background-color: {{ $hex }}; border: 1px solid #ccc; display: inline-block; width: 12px; height: 12px; border-radius: 50%; margin-right: 4px; vertical-align: middle;"></span>
                                    @endif
                                    {{ $color }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($uniqueSizes->count())
                    <div class="mb-2">
                        <label class="form-label fw-bold small mb-1">Size:</label>
                        <div class="d-flex flex-wrap gap-2" id="qvSizeContainer">
                            @foreach($uniqueSizes as $size)
                                <button type="button" class="btn btn-sm btn-outline-secondary qv-swatch-btn qv-size-swatch" data-size="{{ $size }}">
                                    {{ $size }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($uniqueOptions->count())
                    <div class="mb-2">
                        <label class="form-label fw-bold small mb-1">Option:</label>
                        <div class="d-flex flex-wrap gap-2" id="qvOptionContainer">
                            @foreach($uniqueOptions as $opt)
                                <button type="button" class="btn btn-sm btn-outline-secondary qv-swatch-btn qv-option-swatch" data-option="{{ $opt }}">
                                    {{ $opt }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="mt-4 border-top pt-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="input-group" style="max-width: 110px;">
                        <button class="btn btn-sm btn-outline-secondary" type="button" id="qvQtyMinus">-</button>
                        <input type="number" id="qvQtyInput" class="form-control form-control-sm text-center" value="1" min="1" max="{{ $product->stock_quantity }}">
                        <button class="btn btn-sm btn-outline-secondary" type="button" id="qvQtyPlus">+</button>
                    </div>
                    
                    <div class="d-flex gap-2 flex-grow-1">
                        <form action="{{ route('cart.add') }}" method="POST" class="flex-grow-1" id="qvCartForm">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="variant_id" id="qvHiddenVariant">
                            <input type="hidden" name="quantity" id="qvHiddenQty" value="1">
                            <button type="submit" id="qvAtcBtn" class="btn btn-primary w-100" {{ !$product->isInStock() ? 'disabled' : '' }}>
                                <i class="bi bi-cart-plus"></i> Add
                            </button>
                        </form>
                        
                        <form action="{{ route('cart.add') }}" method="POST" class="flex-grow-1" id="qvBuyForm">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="variant_id" id="qvBuyVariant">
                            <input type="hidden" name="quantity" id="qvBuyQty" value="1">
                            <input type="hidden" name="buy_now" value="1">
                            <button type="submit" id="qvBuyBtn" class="btn btn-warning w-100" {{ !$product->isInStock() ? 'disabled' : '' }}>
                                <i class="bi bi-zap"></i> Buy Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .qv-swatch-btn {
        font-size: 0.8rem;
        padding: 4px 10px;
        border-radius: 6px;
        transition: all 0.2s ease;
    }
    .qv-swatch-btn.active {
        border-color: var(--primary) !important;
        background-color: rgba(233, 30, 99, 0.08) !important;
        color: var(--primary) !important;
        box-shadow: 0 0 0 1px var(--primary);
    }
    .qv-thumb-btn.active {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 1px var(--primary);
    }
</style>

<script>
(function() {
    // Gallery Switcher
    document.querySelectorAll('.qv-thumb-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.qv-thumb-btn').forEach(b => {
                b.classList.remove('active');
                b.classList.remove('border-primary');
            });
            this.classList.add('active');
            this.classList.add('border-primary');
            document.getElementById('qvMainImage').src = this.dataset.image;
        });
    });

    // Quantity Counter
    const qtyInput = document.getElementById('qvQtyInput');
    const syncQty = () => {
        const val = qtyInput.value;
        document.getElementById('qvHiddenQty').value = val;
        document.getElementById('qvBuyQty').value = val;
    };

    document.getElementById('qvQtyMinus').addEventListener('click', () => {
        if (qtyInput.value > 1) {
            qtyInput.value--;
            syncQty();
        }
    });

    document.getElementById('qvQtyPlus').addEventListener('click', () => {
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

    // Swatches / Variants Handler
    const variants = @json($product->variants);
    const originalPrice = {{ $product->sale_price ?? $product->regular_price }};
    const regularPrice = {{ $product->regular_price }};
    const isDiscounted = {{ $product->sale_price ? 'true' : 'false' }};
    const discountPercent = {{ $product->discount_percent ?? 0 }};
    const originalStock = {{ $product->stock_quantity }};

    const hasColors = document.querySelectorAll('.qv-color-swatch').length > 0;
    const hasSizes = document.querySelectorAll('.qv-size-swatch').length > 0;
    const hasOptions = document.querySelectorAll('.qv-option-swatch').length > 0;

    let selectedColor = null;
    let selectedSize = null;
    let selectedOption = null;

    const colorSwatches = document.querySelectorAll('.qv-color-swatch');
    const sizeSwatches = document.querySelectorAll('.qv-size-swatch');
    const optionSwatches = document.querySelectorAll('.qv-option-swatch');

    const mainPriceEl = document.getElementById('qvMainPrice');
    const originalPriceEl = document.getElementById('qvOriginalPrice');
    const discountBadgeEl = document.getElementById('qvDiscountBadge');
    const stockBadgeEl = document.getElementById('qvStockBadge');
    const hiddenVariantInput = document.getElementById('qvHiddenVariant');
    const buyVariantInput = document.getElementById('qvBuyVariant');
    const atcBtn = document.getElementById('qvAtcBtn');
    const buyBtn = document.getElementById('qvBuyBtn');

    const updateVariant = () => {
        // Only run check if all attributes present are selected
        if ((hasColors && !selectedColor) || (hasSizes && !selectedSize) || (hasOptions && !selectedOption)) {
            // reset values
            hiddenVariantInput.value = '';
            buyVariantInput.value = '';
            mainPriceEl.textContent = '৳' + originalPrice.toLocaleString();
            if (isDiscounted) {
                originalPriceEl.classList.remove('d-none');
                discountBadgeEl.classList.remove('d-none');
            } else {
                originalPriceEl.classList.add('d-none');
                discountBadgeEl.classList.add('d-none');
            }
            stockBadgeEl.textContent = originalStock > 0 ? (originalStock <= 5 ? `Only ${originalStock} left!` : 'In Stock') : 'Out of Stock';
            stockBadgeEl.className = 'badge ' + (originalStock > 0 ? (originalStock <= 5 ? 'bg-warning text-dark' : 'bg-success') : 'bg-danger');
            qtyInput.setAttribute('max', originalStock);
            if (originalStock > 0) {
                atcBtn.removeAttribute('disabled');
                buyBtn.removeAttribute('disabled');
            } else {
                atcBtn.setAttribute('disabled', 'true');
                buyBtn.setAttribute('disabled', 'true');
            }
            return;
        }

        // Find match
        const match = variants.find(v => {
            return (!hasColors || v.color === selectedColor) &&
                   (!hasSizes || v.size === selectedSize) &&
                   (!hasOptions || v.custom_option === selectedOption);
        });

        if (match) {
            hiddenVariantInput.value = match.id;
            buyVariantInput.value = match.id;

            // Update price
            const priceToUse = match.price ? parseFloat(match.price) : originalPrice;
            mainPriceEl.textContent = '৳' + priceToUse.toLocaleString();
            
            if (match.price && isDiscounted) {
                // calculate original price accordingly
                const calcOrig = Math.round(priceToUse / (1 - (discountPercent / 100)));
                originalPriceEl.textContent = '৳' + calcOrig.toLocaleString();
                originalPriceEl.classList.remove('d-none');
            } else {
                originalPriceEl.classList.add('d-none');
                discountBadgeEl.classList.add('d-none');
            }

            // Update Stock
            const stock = match.stock_quantity;
            qtyInput.setAttribute('max', stock);
            if (parseInt(qtyInput.value) > stock) {
                qtyInput.value = stock > 0 ? stock : 1;
                syncQty();
            }

            if (stock > 0) {
                stockBadgeEl.textContent = stock <= 5 ? `Only ${stock} left!` : 'In Stock';
                stockBadgeEl.className = 'badge bg-success';
                atcBtn.removeAttribute('disabled');
                buyBtn.removeAttribute('disabled');
            } else {
                stockBadgeEl.textContent = 'Out of Stock';
                stockBadgeEl.className = 'badge bg-danger';
                atcBtn.setAttribute('disabled', 'true');
                buyBtn.setAttribute('disabled', 'true');
            }
        } else {
            // no match combination
            hiddenVariantInput.value = '';
            buyVariantInput.value = '';
            stockBadgeEl.textContent = 'Unavailable';
            stockBadgeEl.className = 'badge bg-secondary';
            atcBtn.setAttribute('disabled', 'true');
            buyBtn.setAttribute('disabled', 'true');
        }
    };

    // Color Swatches
    colorSwatches.forEach(swatch => {
        swatch.addEventListener('click', function() {
            colorSwatches.forEach(s => s.classList.remove('active'));
            this.classList.add('active');
            selectedColor = this.dataset.color;
            updateVariant();
        });
    });

    // Size Swatches
    sizeSwatches.forEach(swatch => {
        swatch.addEventListener('click', function() {
            sizeSwatches.forEach(s => s.classList.remove('active'));
            this.classList.add('active');
            selectedSize = this.dataset.size;
            updateVariant();
        });
    });

    // Option Swatches
    optionSwatches.forEach(swatch => {
        swatch.addEventListener('click', function() {
            optionSwatches.forEach(s => s.classList.remove('active'));
            this.classList.add('active');
            selectedOption = this.dataset.option;
            updateVariant();
        });
    });
})();
</script>
