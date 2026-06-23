@extends('layouts.admin')
@section('title', 'Edit Product')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit: {{ Str::limit($product->name, 40) }}</h4>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
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
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_best_seller" value="1" {{ old('is_best_seller', $product->is_best_seller) ? 'checked' : '' }}>
                        <label class="form-check-label">Best Seller</label>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">Thumbnail</div>
                <div class="card-body">
                    @if($product->thumbnail)
                    <img src="{{ asset('storage/'.$product->thumbnail) }}" class="img-fluid rounded mb-2" alt="">
                    @endif
                    <input type="file" name="thumbnail" class="form-control" accept="image/jpeg,image/png,image/webp">
                    <small class="text-muted">Leave blank to keep current</small>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4">
        <button type="submit" class="btn btn-primary px-4">Update Product</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
    </div>
</form>
@endsection
