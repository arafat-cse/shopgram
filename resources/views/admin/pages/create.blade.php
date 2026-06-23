@extends('layouts.admin')
@section('title', 'Add Page')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Add Page</h4>
    <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>
<form action="{{ route('admin.pages.store') }}" method="POST">
    @csrf
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required></div>
                    <div class="mb-3"><label class="form-label">Content *</label>
                        <textarea name="content" class="form-control" rows="15" required>{{ old('content') }}</textarea></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="mb-3"><label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title') }}"></div>
                    <div><label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="2">{{ old('meta_description') }}</textarea></div>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4">
        <button type="submit" class="btn btn-primary">Publish Page</button>
    </div>
</form>
@endsection
