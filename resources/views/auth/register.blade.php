@extends('layouts.auth')

@section('content')
<div class="glass-auth-card">
    <div class="auth-form-side">
        <!-- Register Header -->
        <div class="text-center mb-4">
            <div class="auth-brand-icon"><i class="fas fa-sack-dollar"></i></div>
            <h1 class="welcome-title">Daftar Akun</h1>
            <p class="welcome-subtitle">Buat akun Finance Manager Anda</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate>
            @csrf

            <!-- Nama Lengkap -->
            <div class="mb-3">
                <label for="name" class="form-label">
                    <i class="fas fa-id-card"></i> Nama Lengkap
                </label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Masukkan nama lengkap Anda" required autofocus>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Username -->
            <div class="mb-3">
                <label for="username" class="form-label">
                    <i class="fas fa-user-tag"></i> Username
                </label>
                <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="Masukkan username" required>
                @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i> Alamat Email
                </label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="nama@domain.com" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Nomor HP -->
            <div class="mb-3">
                <label for="phone" class="form-label">
                    <i class="fas fa-phone"></i> Nomor HP
                </label>
                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="Masukkan nomor HP" required>
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i> Password
                </label>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Konfirmasi Password -->
            <div class="mb-4">
                <label for="password_confirmation" class="form-label">
                    <i class="fas fa-circle-check"></i> Konfirmasi Password
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="••••••••" required>
            </div>

            <!-- Register button -->
            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-auth-primary">
                    <i class="fas fa-user-plus me-1"></i> Daftar Akun
                </button>
            </div>
        </form>

        <!-- Divider atau -->
        <div class="text-center my-3">
            <span class="text-muted small">atau</span>
        </div>

        <!-- Google Login -->
        <div class="d-grid mb-4">
            <a href="{{ route('auth.google') }}" class="btn btn-auth-google">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google Logo" class="me-2" width="18" height="18">
                Daftar dengan Google
            </a>
        </div>

        <!-- Back to Login Link -->
        <div class="text-center">
            <span class="text-muted small">Sudah punya akun?</span>
            <a href="{{ route('login') }}" class="auth-link ms-1 small">Masuk di sini</a>
        </div>
    </div>
</div>
@endsection
