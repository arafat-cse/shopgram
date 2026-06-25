@extends('layouts.admin')
@section('title', 'Banners')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Banners</h4>
        <p class="text-muted small mb-0 mt-1">Hero banners → homepage slider &nbsp;·&nbsp; Promo banners → right promo card</p>
    </div>
    <a href="{{ route('admin.banners.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Banner</a>
</div>

@if($banners->isEmpty())
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <i class="bi bi-image text-muted" style="font-size:3rem;opacity:.3"></i>
        <p class="text-muted mt-3 mb-1 fw-semibold">No banners yet</p>
        <a href="{{ route('admin.banners.create') }}" class="btn btn-primary mt-2"><i class="bi bi-plus-lg me-1"></i> Add First Banner</a>
    </div>
</div>
@else

@foreach([['hero','Hero Banners (Homepage Slider)','primary'],['promo','Promo Banners (Right Card)','warning']] as [$type,$label,$color])
    @php $group = $banners->where('type', $type); @endphp
    @if($group->isNotEmpty())
    <div class="mb-5">
        <h6 class="fw-semibold text-uppercase text-muted mb-3" style="letter-spacing:.08em;font-size:.75rem;">
            <span class="badge bg-{{ $color }} me-1">{{ $group->count() }}</span> {{ $label }}
        </h6>
        <div class="row g-3">
            @foreach($group as $banner)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 {{ $banner->status === 'inactive' ? 'opacity-75' : '' }}">
                    {{-- Preview area --}}
                    <div class="position-relative" style="aspect-ratio:16/6;overflow:hidden;background:#f1f3f5;border-radius:.5rem .5rem 0 0;">
                        @if($banner->image)
                            <img src="{{ asset('storage/'.$banner->image) }}"
                                 alt="{{ $banner->title }}"
                                 style="width:100%;height:100%;object-fit:cover;">
                        @else
                            {{-- CSS fallback preview --}}
                            @php
                                $themes = ['hero-fb-1'=>'linear-gradient(135deg,#c0392b,#e67e22)','hero-fb-2'=>'linear-gradient(135deg,#1a237e,#3949ab)','hero-fb-3'=>'linear-gradient(135deg,#1b5e20,#00796b)','promo'=>'linear-gradient(135deg,#6a1b9a,#e91e63)'];
                                $bgKey = $type === 'promo' ? 'promo' : 'hero-fb-'.(($loop->index % 3)+1);
                                $bg = $themes[$bgKey] ?? $themes['hero-fb-1'];
                            @endphp
                            <div style="width:100%;height:100%;background:{{ $bg }};display:flex;align-items:center;justify-content:center;flex-direction:column;gap:4px;">
                                <div style="color:#fff;font-weight:700;font-size:clamp(.9rem,2vw,1.1rem);text-align:center;padding:0 12px;">{{ $banner->title }}</div>
                                @if($banner->subtitle)<div style="color:rgba(255,255,255,.75);font-size:.75rem;text-align:center;padding:0 12px;">{{ $banner->subtitle }}</div>@endif
                            </div>
                        @endif
                        <span class="position-absolute top-0 end-0 m-2 badge bg-{{ $banner->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($banner->status) }}
                        </span>
                        <span class="position-absolute top-0 start-0 m-2 badge bg-dark bg-opacity-60">#{{ $banner->sort_order }}</span>
                    </div>
                    {{-- Info --}}
                    <div class="card-body py-2 px-3">
                        <div class="fw-semibold small text-truncate">{{ $banner->title ?: '(no title)' }}</div>
                        @if($banner->subtitle)
                            <div class="text-muted" style="font-size:.78rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $banner->subtitle }}</div>
                        @endif
                        @if($banner->button_text)
                            <div class="mt-1">
                                <span class="badge bg-light text-dark border" style="font-size:.72rem;">
                                    <i class="bi bi-cursor me-1"></i>{{ $banner->button_text }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer bg-white border-top-0 d-flex gap-2 py-2 px-3">
                        <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-sm btn-outline-primary flex-fill">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <x-delete-button :action="route('admin.banners.destroy', $banner)" message="Delete this banner?" />
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
@endforeach

@endif

<div class="mt-2">{{ $banners->links() }}</div>
@endsection
