@extends('layouts.app')
@section('title', 'Contact Us')
@section('content')
<div class="container py-5">
    <x-breadcrumb :items="[['label' => 'Contact Us']]" />
    <div class="row g-5">
        <div class="col-lg-5">
            <h2 class="fw-bold mb-3">Get In Touch</h2>
            <p class="text-muted mb-4">Have questions? We're here to help.</p>
            <ul class="list-unstyled">
                @if(Setting::get('contact_phone'))
                <li class="mb-3"><i class="bi bi-telephone text-primary me-2"></i>{{ Setting::get('contact_phone') }}</li>
                @endif
                @if(Setting::get('contact_email'))
                <li class="mb-3"><i class="bi bi-envelope text-primary me-2"></i>{{ Setting::get('contact_email') }}</li>
                @endif
                @if(Setting::get('address'))
                <li class="mb-3"><i class="bi bi-geo-alt text-primary me-2"></i>{{ Setting::get('address') }}</li>
                @endif
            </ul>
        </div>
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('contact.send') }}" method="POST">
                        @csrf
                        <x-alert />
                        <div class="mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subject *</label>
                            <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message *</label>
                            <textarea name="message" class="form-control" rows="5" required>{{ old('message') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
