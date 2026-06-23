@extends('layouts.admin')
@section('title', 'Edit Category')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit Category</h4>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>
<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-body">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Parent Category</label>
                <select name="parent_id" class="form-select">
                    <option value="">None (Top Level)</option>
                    @foreach($parents as $p)
                    <option value="{{ $p->id }}" {{ old('parent_id', $category->parent_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Image</label>
                @if($category->image)<img src="{{ asset('storage/'.$category->image) }}" height="60" class="d-block mb-2 rounded">@endif
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="active" {{ old('status', $category->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $category->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection
