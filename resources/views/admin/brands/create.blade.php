@extends('layouts.admin')
@section('title', 'Add Brand')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Add Brand</h4>
    <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>
<div class="card border-0 shadow-sm" style="max-width:500px">
    <div class="card-body">
        <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3"><label class="form-label">Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
            <div class="mb-3"><label class="form-label">Logo</label>
                <input type="file" name="logo" class="form-control" accept="image/*"></div>
            <div class="mb-3"><label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
@endsection
