@extends('layouts.app')
@section('title', 'Forgot Password')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h4 class="fw-bold text-center mb-1">Forgot Password</h4>
                    <p class="text-muted text-center small mb-4">Enter your email to receive a reset link.</p>
                    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                    <x-alert />
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
                    </form>
                    <p class="text-center mt-3 mb-0 small"><a href="{{ route('login') }}">Back to Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
