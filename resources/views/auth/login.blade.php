@extends('layouts.app')
@section('title', 'Login')
@push('styles')
<style>
    .login-orbit {
        width: 108px;
        height: 108px;
        position: relative;
        margin: 0 auto 1.25rem;
        border-radius: 50%;
        background: radial-gradient(circle at center, rgba(233, 30, 99, .14), rgba(233, 30, 99, .04) 48%, transparent 50%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-orbit::before,
    .login-orbit::after {
        content: "";
        position: absolute;
        inset: 10px;
        border-radius: 50%;
        border: 2px solid transparent;
        border-top-color: #e91e63;
        border-right-color: rgba(233, 30, 99, .28);
        animation: loginSpin 3.2s linear infinite;
    }

    .login-orbit::after {
        inset: 22px;
        border-top-color: rgba(245, 130, 31, .9);
        border-right-color: rgba(245, 130, 31, .22);
        animation-duration: 2.2s;
        animation-direction: reverse;
    }

    .login-lock {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: #fff;
        color: #e91e63;
        box-shadow: 0 12px 30px rgba(233, 30, 99, .18);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
        animation: loginFloat 2.4s ease-in-out infinite;
    }

    .login-spark {
        position: absolute;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #f5821f;
        box-shadow: 0 0 0 6px rgba(245, 130, 31, .12);
        animation: loginPulse 1.8s ease-in-out infinite;
    }

    .login-spark.one { top: 14px; right: 24px; }
    .login-spark.two { left: 18px; bottom: 24px; animation-delay: .45s; background: #e91e63; }

    @keyframes loginSpin {
        to { transform: rotate(360deg); }
    }

    @keyframes loginFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }

    @keyframes loginPulse {
        0%, 100% { transform: scale(.85); opacity: .65; }
        50% { transform: scale(1.15); opacity: 1; }
    }
</style>
@endpush
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="login-orbit" aria-hidden="true">
                        <span class="login-spark one"></span>
                        <span class="login-spark two"></span>
                        <span class="login-lock"><i class="bi bi-shield-lock fs-3"></i></span>
                    </div>

                    @if(session('info'))
                        <div class="alert alert-info">{{ session('info') }}</div>
                    @endif

                    <x-alert />

                    <form method="POST" action="{{ route('login.post') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" required autofocus>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label d-flex justify-content-between">
                                Password
                                <a href="{{ route('password.request') }}" class="small">Forgot password?</a>
                            </label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>

                    <hr>
                    <p class="text-center mb-0 small">Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
