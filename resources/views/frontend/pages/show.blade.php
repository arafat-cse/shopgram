@extends('layouts.app')
@section('title', $page->seo_title ?: $page->title)
@push('styles')
<style>
    .static-page-hero {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        padding: 52px 0 40px;
        margin-bottom: 0;
    }
    .static-page-hero h1 {
        color: #fff;
        font-weight: 800;
        font-size: clamp(1.6rem, 3vw, 2.4rem);
        margin: 0;
    }
    .static-page-hero .breadcrumb-item,
    .static-page-hero .breadcrumb-item a,
    .static-page-hero .breadcrumb-item.active { color: rgba(255,255,255,.6); font-size:.85rem; }
    .static-page-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.35); }

    .page-body { padding: 48px 0 64px; }

    .page-content-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,.07);
        padding: 40px 48px;
    }
    @media (max-width:575px) { .page-content-card { padding: 24px 20px; } }

    /* Rich content typography */
    .page-content h2 { font-size: 1.35rem; font-weight: 700; margin: 2rem 0 .75rem; color: #1e293b; border-bottom: 2px solid #f1f5f9; padding-bottom: .5rem; }
    .page-content h3 { font-size: 1.1rem; font-weight: 600; margin: 1.5rem 0 .5rem; color: #334155; }
    .page-content p  { color: #475569; line-height: 1.8; margin-bottom: 1rem; }
    .page-content ul,
    .page-content ol { color: #475569; line-height: 1.8; padding-left: 1.5rem; margin-bottom: 1rem; }
    .page-content li { margin-bottom: .35rem; }
    .page-content strong { color: #1e293b; }
    .page-content a  { color: #e91e63; text-decoration: underline; }
    .page-content blockquote {
        border-left: 4px solid #e91e63;
        background: #fdf2f8;
        padding: 12px 20px;
        border-radius: 0 8px 8px 0;
        color: #64748b;
        font-style: italic;
        margin: 1.5rem 0;
    }
    .page-content table { width:100%; border-collapse:collapse; margin-bottom:1rem; font-size:.9rem; }
    .page-content th { background:#f8fafc; font-weight:600; padding:10px 14px; border:1px solid #e2e8f0; }
    .page-content td { padding:9px 14px; border:1px solid #e2e8f0; color:#475569; }

    .page-sidebar-links a {
        display: block;
        padding: 10px 14px;
        border-radius: 8px;
        text-decoration: none;
        color: #475569;
        font-size: .9rem;
        transition: background .15s, color .15s;
    }
    .page-sidebar-links a:hover,
    .page-sidebar-links a.active { background: #fdf2f8; color: #e91e63; font-weight: 600; }
</style>
@endpush
@section('content')

{{-- Hero --}}
<div class="static-page-hero">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active">{{ $page->title }}</li>
            </ol>
        </nav>
        <h1>{{ $page->title }}</h1>
    </div>
</div>

{{-- Body --}}
<div class="page-body">
    <div class="container">
        <div class="row g-4">
            {{-- Sidebar — other footer pages --}}
            @if($footerPages->count() > 1)
            <div class="col-lg-3 order-lg-last">
                <div class="card border-0 shadow-sm p-3 sticky-top" style="top:90px">
                    <div class="fw-semibold small text-uppercase text-muted mb-2" style="letter-spacing:.06em">Other Pages</div>
                    <div class="page-sidebar-links">
                        @foreach($footerPages as $fp)
                            <a href="{{ route('page.show', $fp->slug) }}"
                               class="{{ $fp->slug === $page->slug ? 'active' : '' }}">
                                {{ $fp->title }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Main content --}}
            <div class="col-lg-{{ $footerPages->count() > 1 ? '9' : '10 offset-lg-1' }}">
                <div class="page-content-card">
                    <div class="page-content">
                        {!! $page->content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
