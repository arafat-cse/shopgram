@extends('layouts.admin')
@section('title', 'Add Footer Page')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Add Footer Page</h4>
    <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>
<form action="{{ route('admin.pages.store') }}" method="POST" id="pageForm">
    @csrf
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title *</label>
                        <input type="text" name="title" class="form-control form-control-lg" value="{{ old('title') }}" required placeholder="e.g. About Us">
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold">Content</label>
                        <p class="text-muted small mb-2">Type normally — use toolbar to format (bold, headings, lists, etc.)</p>
                    </div>
                    {{-- CKEditor target --}}
                    <textarea name="content" id="pageContent" class="form-control" rows="18">{{ old('content') }}</textarea>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold small">Page Settings</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status') !== 'inactive' ? 'selected' : '' }}>Active (Published)</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive (Draft)</option>
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="show_in_footer" value="1" id="showFooterCreate" class="form-check-input" {{ old('show_in_footer') ? 'checked' : '' }}>
                        <label class="form-check-label small fw-semibold" for="showFooterCreate">Show in footer</label>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label small text-muted">SEO Title</label>
                        <input type="text" name="seo_title" class="form-control form-control-sm" value="{{ old('seo_title') }}" placeholder="Leave blank to use page title">
                    </div>
                    <div>
                        <label class="form-label small text-muted">SEO Description</label>
                        <textarea name="seo_description" class="form-control form-control-sm" rows="3" placeholder="Short description for search engines">{{ old('seo_description') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4">Publish Page</button>
        <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
ClassicEditor.create(document.querySelector('#pageContent'), {
    toolbar: {
        items: [
            'heading', '|',
            'bold', 'italic', 'underline', '|',
            'bulletedList', 'numberedList', '|',
            'blockQuote', 'insertTable', '|',
            'link', '|',
            'undo', 'redo'
        ]
    },
    heading: {
        options: [
            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
            { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
        ]
    }
}).catch(err => console.error(err));
</script>
@endpush
@endsection
