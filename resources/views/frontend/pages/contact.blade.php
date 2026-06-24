@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<style>
    .contact-info-card,
    .contact-form-card,
    .contact-text-card {
        background: #fff;
        border: 1px solid #eef0f4;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
    }

    .contact-icon {
        width: 44px;
        height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: rgba(233, 30, 99, 0.1);
        color: #e91e63;
        flex: 0 0 44px;
    }

    .contact-social-link {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid #eef0f4;
        color: #e91e63;
        background: #fff;
        transition: .2s ease;
    }

    .contact-social-link:hover {
        color: #fff;
        background: #e91e63;
        border-color: #e91e63;
        transform: translateY(-2px);
    }

    .contact-submit {
        background: #e91e63;
        border-color: #e91e63;
        font-weight: 700;
    }

    .contact-submit:hover,
    .contact-submit:focus {
        background: #d41458;
        border-color: #d41458;
    }
</style>

<div class="container py-4 py-lg-5">
    <x-breadcrumb :items="['Contact' => '#']" />

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="contact-info-card p-4 h-100">
                <h1 class="h3 fw-bold mb-3">Get In Touch</h1>
                <p class="text-muted mb-4">{{ $contactIntro }}</p>

                <div class="d-flex gap-3 mb-4">
                    <span class="contact-icon"><i class="bi bi-telephone"></i></span>
                    <div>
                        <div class="fw-semibold">Phone</div>
                        <div class="text-muted">{{ $contactPhone ?: '+880 1700-000000' }}</div>
                    </div>
                </div>

                <div class="d-flex gap-3 mb-4">
                    <span class="contact-icon"><i class="bi bi-envelope"></i></span>
                    <div>
                        <div class="fw-semibold">Email</div>
                        <div class="text-muted">{{ $contactEmail ?: 'support@shopgram.test' }}</div>
                    </div>
                </div>

                <div class="d-flex gap-3 mb-4">
                    <span class="contact-icon"><i class="bi bi-geo-alt"></i></span>
                    <div>
                        <div class="fw-semibold">Address</div>
                        <div class="text-muted">{{ $contactAddress ?: 'Dhaka, Bangladesh' }}</div>
                    </div>
                </div>

                <div class="d-flex gap-3 mb-4">
                    <span class="contact-icon"><i class="bi bi-clock"></i></span>
                    <div>
                        <div class="fw-semibold">Support Hours</div>
                        <div class="text-muted">{{ $supportHours }}</div>
                    </div>
                </div>

                @php
                    $icons = ['facebook' => 'bi-facebook', 'youtube' => 'bi-youtube', 'instagram' => 'bi-instagram', 'whatsapp' => 'bi-whatsapp'];
                    $hasSocial = collect($socialLinks)->filter()->isNotEmpty();
                @endphp
                @if($hasSocial)
                    <div class="pt-2">
                        <div class="fw-semibold mb-2">Follow Us</div>
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach($socialLinks as $name => $url)
                                @continue(blank($url))
                                <a href="{{ $name === 'whatsapp' && !str_starts_with($url, 'http') ? 'https://wa.me/' . preg_replace('/\D+/', '', $url) : $url }}"
                                   class="contact-social-link"
                                   target="_blank"
                                   rel="noopener"
                                   aria-label="{{ ucfirst($name) }}">
                                    <i class="bi {{ $icons[$name] }}"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-7">
            <div class="contact-form-card p-4">
                <h2 class="h4 fw-bold mb-3">Send Message</h2>
                <x-alert />

                <form action="{{ route('contact.send') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Subject *</label>
                            <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject') }}" required>
                            @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Message *</label>
                            <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="6" required>{{ old('message') }}</textarea>
                            @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary contact-submit mt-4 px-4">Send Message</button>
                </form>
            </div>
        </div>
    </div>

    @if($mission || $vision)
        <div class="row g-4 mt-1">
            @if($mission)
                <div class="col-md-6">
                    <div class="contact-text-card p-4 h-100">
                        <h2 class="h5 fw-bold mb-2">Our Mission</h2>
                        <p class="text-muted mb-0">{{ $mission }}</p>
                    </div>
                </div>
            @endif

            @if($vision)
                <div class="col-md-6">
                    <div class="contact-text-card p-4 h-100">
                        <h2 class="h5 fw-bold mb-2">Our Vision</h2>
                        <p class="text-muted mb-0">{{ $vision }}</p>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
