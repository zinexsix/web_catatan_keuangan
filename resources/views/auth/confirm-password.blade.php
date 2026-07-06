@extends('layouts.auth')

@section('content')
<div class="glass-auth-card">
    <div class="auth-form-side">
        <!-- Header -->
        <div class="text-center mb-4">
            <div class="auth-brand-icon"><i class="fas fa-shield-halved"></i></div>
            <h1 class="welcome-title">Konfirmasi Sandi</h1>
            <p class="welcome-subtitle">Ini adalah area aman aplikasi. Silakan konfirmasi kata sandi Anda sebelum melanjutkan.</p>
        </div>

        <form method="POST" action="{{ route('password.confirm') }}" class="needs-validation" novalidate>
            @csrf

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i> Kata Sandi
                </label>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required autocomplete="current-password" autofocus>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit button -->
            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-auth-primary">
                    <i class="fas fa-circle-check me-1"></i> Konfirmasi Kata Sandi
                </button>
            </div>
        </form>

        <div class="text-center">
            <a href="{{ route('dashboard') }}" class="auth-link small"><i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard</a>
        </div>
    </div>
</div>
@endsection
