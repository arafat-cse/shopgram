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
                            <label class="form-label">Phone Number or Email</label>
                            <input type="text" name="login" class="form-control @error('login') is-invalid @enderror"
                                   value="{{ old('login') }}" required autofocus
                                   placeholder="01XXXXXXXXX or email@example.com">
                            @error('login')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label d-flex justify-content-between">
                                Password
                                <a href="{{ route('password.request') }}" class="small">Forgot password?</a>
                            </label>
                            <div class="input-group">
                                <input type="password" name="password" id="login-password" class="form-control @error('password') is-invalid @enderror" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('login-password', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>

                    <div class="my-3 d-flex align-items-center gap-2">
                        <hr class="flex-grow-1 m-0">
                        <span class="text-muted small px-1">or continue with</span>
                        <hr class="flex-grow-1 m-0">
                    </div>

                    <a href="{{ route('auth.google') }}" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2" style="border-radius:10px;font-weight:600;font-size:.9rem;padding:10px">
                        <svg width="18" height="18" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                            <path fill="none" d="M0 0h48v48H0z"/>
                        </svg>
                        Continue with Google
                    </a>

                    <hr class="mt-3">
                    <p class="text-center mb-0 small">Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>
@endpush
