@extends('layouts.admin')
@section('title', 'Edit Product')
@section('content')
@php
    $isDuplicateDraft = request()->boolean('copy')
        || ($product->status === 'draft' && Str::startsWith($product->name, 'Copy of '));
@endphp
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit: {{ Str::limit($product->name, 40) }}</h4>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="product-upload-form">
    @csrf @method('PUT')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">Basic Info</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Product Name *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Category *</label>
                            <select name="category_id" class="form-select" required>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Brand</label>
                            <select name="brand_id" class="form-select">
                                <option value="">No Brand</option>
                                @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Short Description</label>
                        <textarea name="short_description" class="form-control" rows="2">{{ old('short_description', $product->short_description) }}</textarea>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Full Description</label>
                        <textarea name="description" class="form-control" rows="5">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">Pricing & Stock</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Regular Price (৳) *</label>
                            <input type="number" name="regular_price" class="form-control" value="{{ old('regular_price', $product->regular_price) }}" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sale Price (৳)</label>
                            <input type="number" name="sale_price" class="form-control" value="{{ old('sale_price', $product->sale_price) }}" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Purchase Price (৳)</label>
                            <input type="number" name="purchase_price" class="form-control" value="{{ old('purchase_price', $product->purchase_price) }}" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Low Stock At</label>
                            <input type="number" name="low_stock_threshold" class="form-control" value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" min="0">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">SEO</div>
                <div class="card-body">
                    <div class="mb-3"><label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $product->meta_title) }}"></div>
                    <div><label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="2">{{ old('meta_description', $product->meta_description) }}</textarea></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">Status</div>
                <div class="card-body">
                    <select name="status" class="form-select mb-3">
                        <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="draft" {{ old('status', $product->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                        <label class="form-check-label">Featured</label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="is_new_arrival" value="1" {{ old('is_new_arrival', $product->is_new_arrival) ? 'checked' : '' }}>
                        <label class="form-check-label">New Arrival</label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="is_best_selling" value="1" {{ old('is_best_selling', $product->is_best_selling) ? 'checked' : '' }}>
                        <label class="form-check-label">Best Seller</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_promoted" value="1" {{ old('is_promoted', $product->is_promoted) ? 'checked' : '' }}>
                        <label class="form-check-label">Promoted <small class="text-muted">(popup)</small></label>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold"><i class="bi bi-coin text-warning"></i> Loyalty Coins</div>
                <div class="card-body">
                    <label class="form-label">Coins earned per unit purchased</label>
                    <input type="number" name="coin_reward" class="form-control mb-3" min="0" step="1"
                           value="{{ old('coin_reward', $product->coin_reward) }}">

                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="isCoinRedeemable" name="is_coin_redeemable" value="1"
                               {{ old('is_coin_redeemable', $product->is_coin_redeemable) ? 'checked' : '' }}
                               onchange="document.getElementById('coinPriceWrap').classList.toggle('d-none', !this.checked)">
                        <label class="form-check-label" for="isCoinRedeemable">Redeemable with Coins</label>
                    </div>
                    <div id="coinPriceWrap" class="{{ old('is_coin_redeemable', $product->is_coin_redeemable) ? '' : 'd-none' }}">
                        <label class="form-label">Coin price (coins required to redeem)</label>
                        <input type="number" name="coin_price" class="form-control" min="1" step="1"
                               value="{{ old('coin_price', $product->coin_price) }}">
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">Thumbnail</div>
                <div class="card-body">
                    @if($product->thumbnail)
                    <img src="{{ $product->thumbnail_url }}" class="img-fluid rounded mb-2" alt="">
                    @endif
                    <input type="file" name="thumbnail" class="form-control product-image-input" accept="image/jpeg,image/png,image/webp">
                    <small class="text-muted">Leave blank to keep current</small>
                </div>
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white fw-bold">YouTube Video</div>
                <div class="card-body">
                    <input type="url" name="video_url" class="form-control" placeholder="https://www.youtube.com/watch?v=..." value="{{ old('video_url', $product->video_url) }}">
                    <small class="text-muted">Enter a valid YouTube video URL to show it in the product gallery.</small>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white fw-bold">Product Gallery</div>
                <div class="card-body">
                    <input type="file" name="gallery[]" class="form-control product-image-input @error('gallery.*') is-invalid @enderror" accept="image/jpeg,image/png,image/webp" multiple>
                    @error('gallery.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted d-block mt-2">Add more product images. Each image max 16MB, total form upload max 64MB.</small>

                    @if($product->images->count())
                        <div class="row g-2 mt-3">
                            @foreach($product->images as $image)
                                <div class="col-4">
                                    <div class="border rounded p-1 position-relative bg-white">
                                        <img src="{{ asset('storage/'.$image->image_path) }}" class="w-100 rounded" alt="" style="height:86px;object-fit:cover">
                                        <x-delete-button
                                            :action="route('admin.products.images.delete', $image)"
                                            message="Delete this image?"
                                            label=""
                                            icon="bi-x"
                                            variant="danger"
                                            form-class="d-inline"
                                            button-class="position-absolute top-0 end-0 m-1"
                                        />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4">
        <button type="submit" class="btn btn-primary px-4">{{ $isDuplicateDraft ? 'Save Product' : 'Update Product' }}</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
    </div>
</form>

@php
    $variantSizeOptions = \App\Enums\VariantSize::values();
    $variantColorOptions = \App\Enums\VariantColor::values();
@endphp
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white fw-bold">Variants</div>
    <div class="card-body">
        @if($product->variants->count())
        <div class="mb-4">
            <div class="row g-2 fw-bold small text-muted d-none d-md-flex mb-1">
                <div class="col-md-2">Size</div>
                <div class="col-md-2">Color</div>
                <div class="col-md-2">Custom Option</div>
                <div class="col-md-1">Weight</div>
                <div class="col-md-1">Material</div>
                <div class="col-md-2">SKU (auto)</div>
                <div class="col-md-1">Price (৳)</div>
                <div class="col-md-1">Stock</div>
            </div>
            @foreach($product->variants as $variant)
            <div class="row g-2 align-items-center mb-2">
                <form action="{{ route('admin.products.variants.update', [$product, $variant]) }}" method="POST" class="row g-2 align-items-center col">
                    @csrf @method('PUT')
                    <div class="col-md-2">
                        <select name="size" class="form-select form-select-sm">
                            <option value="">—</option>
                            @foreach($variantSizeOptions as $option)
                            <option value="{{ $option }}" {{ old('size', $variant->size) == $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                            @if($variant->size && !in_array($variant->size, $variantSizeOptions))
                            <option value="{{ $variant->size }}" selected>{{ $variant->size }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="color" class="form-select form-select-sm">
                            <option value="">—</option>
                            @foreach($variantColorOptions as $option)
                            <option value="{{ $option }}" {{ old('color', $variant->color) == $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                            @if($variant->color && !in_array($variant->color, $variantColorOptions))
                            <option value="{{ $variant->color }}" selected>{{ $variant->color }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-2"><input type="text" name="custom_option" class="form-control form-control-sm" value="{{ old('custom_option', $variant->custom_option) }}"></div>
                    <div class="col-md-1"><input type="text" name="weight" class="form-control form-control-sm" value="{{ old('weight', $variant->weight) }}"></div>
                    <div class="col-md-1"><input type="text" name="material" class="form-control form-control-sm" value="{{ old('material', $variant->material) }}"></div>
                    <div class="col-md-2"><input type="text" class="form-control form-control-sm" value="{{ $variant->sku }}" disabled></div>
                    <div class="col-md-1"><input type="number" name="price" class="form-control form-control-sm" min="0" step="0.01" value="{{ old('price', $variant->price) }}"></div>
                    <div class="col-md-1"><input type="number" name="stock_quantity" class="form-control form-control-sm" min="0" required value="{{ old('stock_quantity', $variant->stock_quantity) }}"></div>
                    <div class="col-md-1 d-flex">
                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Save"><i class="bi bi-check-lg"></i></button>
                    </div>
                </form>
                <div class="col-auto">
                    <x-delete-button
                        :action="route('admin.products.variants.destroy', [$product, $variant])"
                        message="Delete this variant?"
                        label=""
                        icon="bi-trash"
                        variant="outline-danger"
                        form-class="d-inline"
                    />
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <form action="{{ route('admin.products.variants.store', $product) }}" method="POST" id="variantAddForm">
            @csrf
            <div class="table-responsive">
                <table class="table table-sm align-middle" id="variantAddTable">
                    <thead>
                        <tr>
                            <th>Size</th><th>Color</th><th>Custom Option</th><th>Weight</th><th>Material</th><th>Price (৳)</th><th>Stock</th><th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="addVariantRowBtn"><i class="bi bi-plus-lg"></i> Add Row</button>
            <button type="submit" class="btn btn-sm btn-primary ms-2">Save New Variants</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const tbody = document.querySelector('#variantAddTable tbody');
    const addBtn = document.getElementById('addVariantRowBtn');
    if (!tbody || !addBtn) return;

    const sizeOptions = @json($variantSizeOptions);
    const colorOptions = @json($variantColorOptions);

    function buildSelect(name, options) {
        const opts = ['<option value="">—</option>']
            .concat(options.map((opt) => `<option value="${opt}">${opt}</option>`));
        return `<select name="${name}" class="form-select form-select-sm">${opts.join('')}</select>`;
    }

    let rowIndex = 0;

    function addVariantRow() {
        const idx = rowIndex++;
        const tr = document.createElement('tr');
        let html = '';
        html += `<td>${buildSelect(`variants[${idx}][size]`, sizeOptions)}</td>`;
        html += `<td>${buildSelect(`variants[${idx}][color]`, colorOptions)}</td>`;
        ['custom_option', 'weight', 'material'].forEach((field) => {
            html += `<td><input type="text" name="variants[${idx}][${field}]" class="form-control form-control-sm"></td>`;
        });
        html += `<td><input type="number" name="variants[${idx}][price]" class="form-control form-control-sm" min="0" step="0.01"></td>`;
        html += `<td><input type="number" name="variants[${idx}][stock_quantity]" class="form-control form-control-sm" min="0" value="0" required></td>`;
        html += `<td><button type="button" class="btn btn-sm btn-outline-danger remove-variant-row"><i class="bi bi-x"></i></button></td>`;
        tr.innerHTML = html;
        tbody.appendChild(tr);
    }

    addBtn.addEventListener('click', addVariantRow);
    tbody.addEventListener('click', (event) => {
        const btn = event.target.closest('.remove-variant-row');
        if (btn) btn.closest('tr').remove();
    });

    addVariantRow();
})();

document.querySelectorAll('.product-upload-form').forEach((form) => {
    form.addEventListener('submit', function (event) {
        const maxFileSize = 16 * 1024 * 1024;
        const maxTotalSize = 60 * 1024 * 1024;
        let totalSize = 0;

        for (const input of form.querySelectorAll('.product-image-input')) {
            for (const file of input.files) {
                totalSize += file.size;
                if (file.size > maxFileSize) {
                    event.preventDefault();
                    showToast(`${file.name} is larger than 16MB. Please upload a smaller image.`, 'danger');
                    return;
                }
            }
        }

        if (totalSize > maxTotalSize) {
            event.preventDefault();
            showToast('Total image upload is too large. Please keep all selected images under 60MB.', 'danger');
        }
    });
});
</script>
@endpush
@endsection
