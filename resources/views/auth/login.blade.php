@extends('layouts.auth')

@section('content')
<div class="glass-auth-card">
    <div class="auth-form-side">
        <!-- Selamat Datang Header -->
        <div class="text-center mb-4">
            <div class="auth-brand-icon"><i class="fas fa-sack-dollar"></i></div>
            <h1 class="welcome-title">Selamat Datang</h1>
            <p class="welcome-subtitle">Masuk ke akun Finance Manager Anda</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success border-0 small mb-4 py-2" role="alert">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger border-0 small mb-4 py-2" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
            @csrf

            <!-- Username / Email / Phone -->
            <div class="mb-3">
                <label for="login" class="form-label">
                    <i class="fas fa-user"></i> Username / Email / Phone
                </label>
                <input type="text" name="login" id="login" class="form-control @error('login') is-invalid @enderror" value="{{ old('login') }}" placeholder="Masukkan username, email, atau phone" required autofocus>
                @error('login')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i> Password
                </label>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Login button -->
            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-auth-primary">
                    <i class="fas fa-sign-in-alt me-1"></i> Login
                </button>
            </div>
        </form>

        @if (config('services.google.client_id'))
        <!-- Divider atau -->
        <div class="text-center my-3">
            <span class="text-muted small">atau</span>
        </div>

        <!-- Google Login -->
        <div class="d-grid mb-4">
            <a href="{{ route('auth.google') }}" class="btn btn-auth-google">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google Logo" class="me-2" width="18" height="18">
                Login dengan Google
            </a>
        </div>
        @endif

        <!-- Tips Login Box -->
        <div class="tips-box mb-4">
            <h6 class="fw-bold mb-2"><i class="fas fa-lightbulb me-1"></i> Tips Login:</h6>
            <ul>
                <li>Gunakan Google untuk login yang lebih cepat</li>
                <li>Atau masukkan username, email, atau nomor telepon</li>
            </ul>
        </div>

        <!-- Register Link -->
        <div class="text-center">
            <span class="text-muted small">Belum punya akun?</span>
            <a href="{{ route('register') }}" class="auth-link ms-1 small">Daftar di sini</a>
        </div>
    </div>
</div>
@endsection
