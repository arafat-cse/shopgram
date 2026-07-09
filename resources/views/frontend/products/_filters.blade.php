@php $idPrefix = $idPrefix ?? 'f'; @endphp
<form method="GET" action="{{ $filterActionUrl ?? route('products.index') }}" class="product-filter-form">

    {{-- Price Range --}}
    <div class="filter-section mb-4">
        <div class="fw-semibold small mb-3">Price Range</div>
        <div class="filter-price-slider" data-min="{{ $priceBounds['min'] }}" data-max="{{ $priceBounds['max'] }}">
            <div class="range-track">
                <div class="range-fill"></div>
            </div>
            <input type="range" class="range-input range-min"
                   min="{{ $priceBounds['min'] }}" max="{{ $priceBounds['max'] }}"
                   value="{{ request('min_price', $priceBounds['min']) }}">
            <input type="range" class="range-input range-max"
                   min="{{ $priceBounds['min'] }}" max="{{ $priceBounds['max'] }}"
                   value="{{ request('max_price', $priceBounds['max']) }}">
        </div>
        <div class="d-flex gap-2 mt-3">
            <input type="number" name="min_price" class="form-control form-control-sm price-num-min"
                   value="{{ request('min_price', $priceBounds['min']) }}">
            <input type="number" name="max_price" class="form-control form-control-sm price-num-max"
                   value="{{ request('max_price', $priceBounds['max']) }}">
        </div>
    </div>

    {{-- Availability --}}
    <div class="filter-section mb-4 border-top pt-3">
        <button type="button" class="filter-section-toggle" data-bs-toggle="collapse" data-bs-target="#{{ $idPrefix }}_availability">
            <span class="fw-semibold small">Availability</span>
            <i class="bi bi-chevron-up"></i>
        </button>
        <div class="collapse show mt-3" id="{{ $idPrefix }}_availability">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="in_stock" value="1" id="{{ $idPrefix }}_inStock"
                       {{ request('in_stock') ? 'checked' : '' }}>
                <label class="form-check-label small" for="{{ $idPrefix }}_inStock">In Stock Only</label>
            </div>
        </div>
    </div>

    {{-- Brand --}}
    @if(isset($brands) && $brands->count())
    <div class="filter-section mb-4 border-top pt-3">
        <button type="button" class="filter-section-toggle" data-bs-toggle="collapse" data-bs-target="#{{ $idPrefix }}_brand">
            <span class="fw-semibold small">Brand</span>
            <i class="bi bi-chevron-up"></i>
        </button>
        <div class="collapse show mt-3" id="{{ $idPrefix }}_brand">
            <div class="filter-brand-list">
                @foreach($brands as $b)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="brand[]" value="{{ $b->slug }}" id="{{ $idPrefix }}_brand{{ $b->id }}"
                           {{ in_array($b->slug, (array) request('brand', [])) ? 'checked' : '' }}>
                    <label class="form-check-label small" for="{{ $idPrefix }}_brand{{ $b->id }}">{{ $b->name }}</label>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Category --}}
    @if(isset($categories) && $categories->count())
        @php
            $selectedCategoryId = request('category_id');
            $selectedParent = null;
            $selectedChildId = null;
            foreach ($categories as $cat) {
                if ((string) $cat->id === (string) $selectedCategoryId) { $selectedParent = $cat; break; }
                $match = $cat->children->first(fn($c) => (string) $c->id === (string) $selectedCategoryId);
                if ($match) { $selectedParent = $cat; $selectedChildId = $match->id; break; }
            }
        @endphp
        <div class="filter-section mb-4 border-top pt-3">
            <button type="button" class="filter-section-toggle" data-bs-toggle="collapse" data-bs-target="#{{ $idPrefix }}_category">
                <span class="fw-semibold small">Category</span>
                <i class="bi bi-chevron-up"></i>
            </button>
            <div class="collapse show mt-3" id="{{ $idPrefix }}_category">
                <select class="form-select form-select-sm mb-2 category-parent-select" name="category_id">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                                data-children='@json($cat->children->map(fn($c) => ["id" => $c->id, "name" => $c->name])->values())'
                                {{ $selectedParent && $selectedParent->id === $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                <select class="form-select form-select-sm category-child-select" name="category_id"
                        style="{{ $selectedParent && $selectedParent->children->count() ? '' : 'display:none;' }}">
                    <option value="">All Subcategories</option>
                    @if($selectedParent)
                        @foreach($selectedParent->children as $child)
                            <option value="{{ $child->id }}" {{ $selectedChildId === $child->id ? 'selected' : '' }}>{{ $child->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    @endif

    {{-- Sort --}}
    <div class="filter-section border-top pt-3">
        <label class="form-label fw-semibold small">Sort By</label>
        <select name="sort" class="form-select form-select-sm">
            <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Latest</option>
            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
        </select>
    </div>

    @if(request()->anyFilled(['category_id', 'brand', 'min_price', 'max_price', 'in_stock', 'sort']))
        <a href="{{ $filterActionUrl ?? route('products.index') }}" class="btn btn-outline-secondary btn-sm w-100 mt-4">Clear All</a>
    @endif
</form>
