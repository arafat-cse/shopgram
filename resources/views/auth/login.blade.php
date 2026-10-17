@extends('layouts.app')
@section('title', 'Login')
@push('styles')
<style>
    .auth-wrap {
        min-height: calc(100vh - 220px);
        display: flex;
        align-items: center;
        padding: 2.5rem 0;
    }

    .auth-card {
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 24px 60px rgba(109, 40, 217, .14);
        background: #fff;
    }

    .auth-side {
        background: linear-gradient(160deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: #fff;
        padding: 3rem 2.5rem;
        position: relative;
        overflow: hidden;
        min-height: 100%;
    }

    .auth-side::before {
        content: "";
        position: absolute;
        width: 280px;
        height: 280px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .08);
        top: -90px;
        right: -90px;
    }

    .auth-side::after {
        content: "";
        position: absolute;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .06);
        bottom: -60px;
        left: -50px;
    }

    .auth-side-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        background: rgba(255, 255, 255, .16);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        position: relative;
        z-index: 1;
    }

    .auth-side h3, .auth-side p, .auth-feature { position: relative; z-index: 1; }

    .auth-feature {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        margin-bottom: 1.1rem;
    }

    .auth-feature .feat-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: rgba(255, 255, 255, .15);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: .95rem;
    }

    .auth-form-side {
        padding: 3rem 2.5rem;
    }

    .input-icon-group {
        position: relative;
    }

    .input-icon-group .field-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        z-index: 3;
        pointer-events: none;
    }

    .input-icon-group .form-control {
        padding-left: 40px;
    }

    .auth-divider {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 1.4rem 0;
        color: #94a3b8;
        font-size: .82rem;
    }

    .auth-divider hr { flex-grow: 1; margin: 0; }

    .btn-google {
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        font-weight: 600;
        font-size: .9rem;
        padding: 10px;
        transition: border-color .15s, background .15s;
    }

    .btn-google:hover { background: #f8fafc; border-color: #cbd5e1; }

    .auth-submit-btn {
        border-radius: 12px;
        padding: 11px;
        font-weight: 700;
        background: var(--primary);
        border-color: var(--primary);
    }

    .auth-submit-btn:hover { background: var(--primary-dark); border-color: var(--primary-dark); }

    @media (max-width: 767.98px) {
        .auth-side { display: none; }
        .auth-form-side { padding: 2rem 1.5rem; }
    }
</style>
@endpush
@section('content')
<div class="container">
    <div class="auth-wrap">
        <div class="row justify-content-center w-100">
            <div class="col-lg-9 col-xl-8">
                <div class="auth-card row g-0">
                    <div class="col-md-5 auth-side">
                        <div class="auth-side-icon"><i class="bi bi-shield-lock-fill fs-3"></i></div>
                        <h3 class="fw-bold mb-2">Welcome back</h3>
                        <p class="mb-4" style="opacity:.85;font-size:.92rem">
                            Login to track orders, manage your wishlist, and check out faster.
                        </p>

                        <div class="auth-feature">
                            <span class="feat-icon"><i class="bi bi-truck"></i></span>
                            <div>
                                <div class="fw-semibold small">Order Tracking</div>
                                <div class="small" style="opacity:.75">Real-time status on every order</div>
                            </div>
                        </div>
                        <div class="auth-feature">
                            <span class="feat-icon"><i class="bi bi-heart"></i></span>
                            <div>
                                <div class="fw-semibold small">Saved Wishlist</div>
                                <div class="small" style="opacity:.75">Pick up right where you left off</div>
                            </div>
                        </div>
                        <div class="auth-feature">
                            <span class="feat-icon"><i class="bi bi-lightning-charge-fill"></i></span>
                            <div>
                                <div class="fw-semibold small">Faster Checkout</div>
                                <div class="small" style="opacity:.75">Your details, saved securely</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7 auth-form-side">
                        <h4 class="fw-bold mb-1">Login to your account</h4>
                        <p class="text-muted small mb-4">Enter your details to continue</p>

                        @if(session('info'))
                            <div class="alert alert-info">{{ session('info') }}</div>
                        @endif

                        <x-alert />

                        <form method="POST" action="{{ route('login.post') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Phone Number or Email</label>
                                <div class="input-icon-group">
                                    <i class="bi bi-person field-icon"></i>
                                    <input type="text" name="login" class="form-control @error('login') is-invalid @enderror"
                                           value="{{ old('login') }}" required autofocus
                                           placeholder="01XXXXXXXXX or email@example.com">
                                </div>
                                @error('login')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label d-flex justify-content-between">
                                    Password
                                    <a href="{{ route('password.request') }}" class="small">Forgot password?</a>
                                </label>
                                <div class="input-icon-group">
                                    <i class="bi bi-lock field-icon"></i>
                                    <div class="input-group">
                                        <input type="password" name="password" id="login-password" class="form-control @error('password') is-invalid @enderror" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('login-password', this)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" name="remember" id="remember">
                                <label class="form-check-label small" for="remember">Remember me</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 auth-submit-btn">Login</button>
                        </form>

                        <div class="auth-divider">
                            <hr><span>or continue with</span><hr>
                        </div>

                        <a href="{{ route('auth.google') }}" class="btn btn-google w-100 d-flex align-items-center justify-content-center gap-2">
                            <svg width="18" height="18" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                                <path fill="none" d="M0 0h48v48H0z"/>
                            </svg>
                            Continue with Google
                        </a>

                        <p class="text-center mt-4 mb-0 small">Don't have an account? <a href="{{ route('register') }}" class="fw-semibold">Register here</a></p>
                    </div>
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
