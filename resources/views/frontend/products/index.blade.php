@extends('layouts.app')
@section('title', $pageTitle ?? (isset($category) ? $category->name : (isset($brand) ? $brand->name : 'All Products')))
@section('content')
<div class="container py-4">
    <x-breadcrumb :items="[($breadcrumbTitle ?? (isset($category) ? $category->name : (isset($brand) ? $brand->name : 'Products'))) => '#']" />

    <div class="row g-4">
        {{-- Mobile Filter Button --}}
        <div class="col-12 d-lg-none">
            <button class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-2 py-2 fw-semibold" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileFiltersOffcanvas" aria-controls="mobileFiltersOffcanvas">
                <i class="bi bi-funnel"></i> Filter Products
            </button>
        </div>

        {{-- Sidebar Filters (Desktop) --}}
        <div class="col-lg-3 d-none d-lg-block">
            <div class="card border-0 shadow-sm p-3">
                @include('frontend.products._filters', ['idPrefix' => 'd'])
            </div>
        </div>

        {{-- Products Grid --}}
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="mb-0 text-muted small">Showing {{ $products->total() }} products</p>
            </div>

            @if($products->count())
            <div class="row g-3">
                @foreach($products as $product)
                <div class="col-6 col-md-4 col-xl-3">
                    <x-product-card :product="$product" />
                </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $products->links() }}</div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-box-seam text-muted" style="font-size:3rem"></i>
                <p class="text-muted mt-2">No products found.</p>
                <a href="{{ $filterActionUrl ?? route('products.index') }}" class="btn btn-primary btn-sm">Clear Filters</a>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Mobile Filters Offcanvas --}}
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileFiltersOffcanvas" aria-labelledby="mobileFiltersOffcanvasLabel" style="z-index: 1070;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold" id="mobileFiltersOffcanvasLabel">Filters</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        @include('frontend.products._filters', ['idPrefix' => 'm'])
    </div>
</div>

<style>
.filter-section-toggle {
    display: flex; align-items: center; justify-content: space-between;
    width: 100%; border: 0; background: none; padding: 0; cursor: pointer;
}
.filter-section-toggle .bi-chevron-up { transition: transform .2s ease; color: #6b7280; }
.filter-section-toggle.collapsed .bi-chevron-up { transform: rotate(180deg); }

.filter-brand-list { max-height: 220px; overflow-y: auto; padding-right: 4px; }

.filter-price-slider { position: relative; height: 32px; }
.filter-price-slider .range-track {
    position: absolute; top: 50%; left: 0; right: 0; height: 4px;
    background: #e5e7eb; border-radius: 4px; transform: translateY(-50%);
}
.filter-price-slider .range-fill {
    position: absolute; height: 4px; background: var(--primary); border-radius: 4px;
}
.filter-price-slider .range-input {
    position: absolute; top: 50%; left: 0; width: 100%; margin: 0;
    -webkit-appearance: none; appearance: none; background: none;
    pointer-events: none; transform: translateY(-50%);
}
.filter-price-slider .range-input::-webkit-slider-thumb {
    -webkit-appearance: none; pointer-events: auto;
    width: 18px; height: 18px; border-radius: 50%;
    background: #fff; border: 3px solid var(--primary); cursor: pointer;
    box-shadow: 0 1px 4px rgba(0,0,0,.25);
}
.filter-price-slider .range-input::-moz-range-thumb {
    pointer-events: auto; width: 18px; height: 18px; border-radius: 50%;
    background: #fff; border: 3px solid var(--primary); cursor: pointer;
    box-shadow: 0 1px 4px rgba(0,0,0,.25);
}
.filter-price-slider .range-input::-webkit-slider-runnable-track { background: none; }
.filter-price-slider .range-input::-moz-range-track { background: none; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.product-filter-form').forEach(function (form) {
        // Category & subcategory share one query param (category_id) — right
        // before submit, disable whichever of the two shouldn't be sent so
        // only the most specific selection wins. Uses requestSubmit() (not
        // submit()) everywhere below so this listener actually fires.
        var parentSel = form.querySelector('.category-parent-select');
        var childSel  = form.querySelector('.category-child-select');
        form.addEventListener('submit', function () {
            if (!parentSel || !childSel) return;
            if (childSel.value) {
                parentSel.disabled = true;
            } else {
                childSel.disabled = true;
            }
        });

        // Auto-submit on checkbox / select change (category selects handled separately below)
        form.querySelectorAll('input[type="checkbox"], select:not(.category-parent-select):not(.category-child-select)').forEach(function (el) {
            el.addEventListener('change', function () { form.requestSubmit(); });
        });

        // Cascading category -> subcategory dropdown
        if (parentSel && childSel) {
            parentSel.addEventListener('change', function () {
                var opt = parentSel.selectedOptions[0];
                var children = (opt && opt.dataset.children) ? JSON.parse(opt.dataset.children) : [];

                childSel.innerHTML = '<option value="">All Subcategories</option>';
                children.forEach(function (c) {
                    var o = document.createElement('option');
                    o.value = c.id;
                    o.textContent = c.name;
                    childSel.appendChild(o);
                });

                childSel.style.display = children.length ? '' : 'none';
                form.requestSubmit();
            });

            childSel.addEventListener('change', function () { form.requestSubmit(); });
        }

        // Dual price range slider
        var slider = form.querySelector('.filter-price-slider');
        if (!slider) return;

        var boundMin = parseFloat(slider.dataset.min);
        var boundMax = parseFloat(slider.dataset.max);
        var rangeMin = slider.querySelector('.range-min');
        var rangeMax = slider.querySelector('.range-max');
        var fill     = slider.querySelector('.range-fill');
        var numMin   = form.querySelector('.price-num-min');
        var numMax   = form.querySelector('.price-num-max');

        function updateFill() {
            var span = (boundMax - boundMin) || 1;
            var left  = ((parseFloat(rangeMin.value) - boundMin) / span) * 100;
            var right = ((parseFloat(rangeMax.value) - boundMin) / span) * 100;
            fill.style.left  = left + '%';
            fill.style.width = Math.max(right - left, 0) + '%';
        }

        function syncFromSliders() {
            if (parseFloat(rangeMin.value) > parseFloat(rangeMax.value)) {
                rangeMin.value = rangeMax.value;
            }
            numMin.value = rangeMin.value;
            numMax.value = rangeMax.value;
            updateFill();
        }

        function syncFromInputs() {
            var minV = Math.max(boundMin, Math.min(parseFloat(numMin.value) || boundMin, boundMax));
            var maxV = Math.max(boundMin, Math.min(parseFloat(numMax.value) || boundMax, boundMax));
            if (minV > maxV) minV = maxV;
            rangeMin.value = minV;
            rangeMax.value = maxV;
            numMin.value = minV;
            numMax.value = maxV;
            updateFill();
        }

        rangeMin.addEventListener('input', syncFromSliders);
        rangeMax.addEventListener('input', syncFromSliders);
        rangeMin.addEventListener('change', function () { syncFromSliders(); form.requestSubmit(); });
        rangeMax.addEventListener('change', function () { syncFromSliders(); form.requestSubmit(); });

        numMin.addEventListener('change', function () { syncFromInputs(); form.requestSubmit(); });
        numMax.addEventListener('change', function () { syncFromInputs(); form.requestSubmit(); });

        updateFill();
    });
});
</script>
@endsection
