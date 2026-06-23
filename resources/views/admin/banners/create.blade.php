@extends('layouts.admin')
@section('title', 'Add Banner')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Add Banner</h4>
    <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>
<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-body">
        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3"><label class="form-label">Title *</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required></div>
            <div class="mb-3"><label class="form-label">Subtitle</label>
                <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle') }}"></div>
            <div class="mb-3"><label class="form-label">Image *</label>
                <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp" required></div>
            <div class="mb-3"><label class="form-label">Link URL</label>
                <input type="url" name="link" class="form-control" value="{{ old('link') }}" placeholder="https://..."></div>
            <div class="mb-3"><label class="form-label">Button Text</label>
                <input type="text" name="button_text" class="form-control" value="{{ old('button_text', 'Shop Now') }}"></div>
            <div class="row g-3 mb-3">
                <div class="col-md-6"><label class="form-label">Position</label>
                    <select name="position" class="form-select">
                        <option value="hero">Hero Slider</option>
                        <option value="top">Top Banner</option>
                        <option value="sidebar">Sidebar</option>
                    </select>
                </div>
                <div class="col-md-6"><label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0"></div>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" name="is_active" value="1" checked>
                <label class="form-check-label">Active</label>
            </div>
            <button type="submit" class="btn btn-primary">Save Banner</button>
        </form>
    </div>
</div>
@endsection
