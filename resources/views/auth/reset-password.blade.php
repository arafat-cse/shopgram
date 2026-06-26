@extends('layouts.app')
@section('title', 'Reset Password')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h4 class="fw-bold text-center mb-4">Reset Password</h4>
                    <x-alert />
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $email ?? old('email') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="passwordField"
                                    class="form-control @error('password') is-invalid @enderror" required>
                                <button type="button" class="btn btn-outline-secondary px-3" id="togglePassword"
                                    tabindex="-1" title="Show/hide password">
                                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="confirmPasswordField"
                                    class="form-control" required>
                                <button type="button" class="btn btn-outline-secondary px-3" id="toggleConfirmPassword"
                                    tabindex="-1" title="Show/hide password">
                                    <i class="bi bi-eye" id="toggleConfirmPasswordIcon"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function setupToggle(btnId, fieldId, iconId) {
        const btn  = document.getElementById(btnId);
        const field = document.getElementById(fieldId);
        const icon  = document.getElementById(iconId);
        if (!btn) return;
        btn.addEventListener('click', function () {
            const isPassword = field.type === 'password';
            field.type = isPassword ? 'text' : 'password';
            icon.classList.toggle('bi-eye',      !isPassword);
            icon.classList.toggle('bi-eye-slash',  isPassword);
        });
    }
    setupToggle('togglePassword',        'passwordField',        'togglePasswordIcon');
    setupToggle('toggleConfirmPassword', 'confirmPasswordField', 'toggleConfirmPasswordIcon');
</script>
@endsection

