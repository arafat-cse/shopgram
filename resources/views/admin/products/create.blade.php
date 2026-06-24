@extends('layouts.admin')
@section('title', isset($sourceProduct) && $sourceProduct ? 'Duplicate Product' : 'Add Product')
@section('content')
@php
    $prefill = $prefill ?? [];
@endphp
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">{{ isset($sourceProduct) && $sourceProduct ? 'Duplicate Product' : 'Add Product' }}</h4>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="product-upload-form">
    @csrf
    @if(!empty($prefill['duplicate_product_id']))
        <input type="hidden" name="duplicate_product_id" value="{{ $prefill['duplicate_product_id'] }}">
    @endif
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">Basic Info</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Product Name *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $prefill['name'] ?? '') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Category *</label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $prefill['category_id'] ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Brand</label>
                            <select name="brand_id" class="form-select">
                                <option value="">No Brand</option>
                                @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id', $prefill['brand_id'] ?? '') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Short Description</label>
                        <textarea name="short_description" class="form-control" rows="2">{{ old('short_description', $prefill['short_description'] ?? '') }}</textarea>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Full Description</label>
                        <textarea name="description" class="form-control" rows="5">{{ old('description', $prefill['description'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">Pricing & Stock</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Regular Price (৳) *</label>
                            <input type="number" name="regular_price" class="form-control" value="{{ old('regular_price', $prefill['regular_price'] ?? '') }}" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sale Price (৳)</label>
                            <input type="number" name="sale_price" class="form-control" value="{{ old('sale_price', $prefill['sale_price'] ?? '') }}" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Purchase Price (৳)</label>
                            <input type="number" name="purchase_price" class="form-control" value="{{ old('purchase_price', $prefill['purchase_price'] ?? '') }}" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control" value="{{ old('sku', $prefill['sku'] ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Stock Quantity *</label>
                            <input type="number" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', $prefill['stock_quantity'] ?? 0) }}" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Low Stock Alert At</label>
                            <input type="number" name="low_stock_threshold" class="form-control" value="{{ old('low_stock_threshold', $prefill['low_stock_threshold'] ?? 5) }}" min="0">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">SEO</div>
                <div class="card-body">
                    <div class="mb-3"><label class="form-label">Meta Title</label>
                        <input type="text" name="seo_title" class="form-control" value="{{ old('seo_title', $prefill['seo_title'] ?? '') }}"></div>
                    <div class="mb-3"><label class="form-label">Meta Description</label>
                        <textarea name="seo_description" class="form-control" rows="2">{{ old('seo_description', $prefill['seo_description'] ?? '') }}</textarea></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">Status</div>
                <div class="card-body">
                    <select name="status" class="form-select mb-3">
                        <option value="active" {{ old('status', $prefill['status'] ?? 'draft') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $prefill['status'] ?? 'draft') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="draft" {{ old('status', $prefill['status'] ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="is_featured" value="1" {{ old('is_featured', $prefill['is_featured'] ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label">Featured</label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="is_new_arrival" value="1" {{ old('is_new_arrival', $prefill['is_new_arrival'] ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label">New Arrival</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_best_selling" value="1" {{ old('is_best_selling', $prefill['is_best_selling'] ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label">Best Seller</label>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">Thumbnail</div>
                <div class="card-body">
                    @if(isset($sourceProduct) && $sourceProduct?->thumbnail)
                        <img src="{{ asset('storage/'.$sourceProduct->thumbnail) }}" class="img-fluid rounded mb-2" alt="">
                        <small class="text-muted d-block mb-2">This thumbnail will be copied when you save unless you upload a new one.</small>
                    @endif
                    <input type="file" name="thumbnail" class="form-control product-image-input" accept="image/jpeg,image/png,image/webp">
                    <small class="text-muted">JPEG/PNG/WebP, max 16MB</small>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white fw-bold">Product Gallery</div>
                <div class="card-body">
                    <input type="file" name="gallery[]" class="form-control product-image-input @error('gallery.*') is-invalid @enderror" accept="image/jpeg,image/png,image/webp" multiple>
                    @error('gallery.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted d-block mt-2">Upload 1-8 extra images. Each image max 16MB, total form upload max 64MB.</small>
                    @if(isset($sourceProduct) && $sourceProduct?->images->count())
                        <small class="text-muted d-block mt-2">Existing gallery images from the source product will be copied on save.</small>
                        <div class="row g-2 mt-2">
                            @foreach($sourceProduct->images as $image)
                                <div class="col-4">
                                    <img src="{{ asset('storage/'.$image->image_path) }}" class="w-100 rounded border" alt="" style="height:72px;object-fit:cover">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4">
        <button type="submit" class="btn btn-primary px-4">Save Product</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
    </div>
</form>
@push('scripts')
<script>
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
                    alert(`${file.name} is larger than 16MB. Please upload a smaller image.`);
                    return;
                }
            }
        }

        if (totalSize > maxTotalSize) {
            event.preventDefault();
            alert('Total image upload is too large. Please keep all selected images under 60MB.');
        }
    });
});
</script>
@endpush
@endsection
