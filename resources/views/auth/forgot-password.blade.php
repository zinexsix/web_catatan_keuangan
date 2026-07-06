@extends('layouts.auth')

@section('content')
<div class="glass-auth-card">
    <div class="auth-form-side">
        <!-- Header -->
        <div class="text-center mb-4">
            <div class="auth-brand-icon"><i class="fas fa-key"></i></div>
            <h1 class="welcome-title">Lupa Kata Sandi?</h1>
            <p class="welcome-subtitle">Masukkan alamat email Anda untuk menerima tautan pemulihan kata sandi.</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success border-0 small mb-4 py-2" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="needs-validation" novalidate>
            @csrf

            <!-- Email Address -->
            <div class="mb-4">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i> Alamat Email
                </label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="nama@domain.com" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit button -->
            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-auth-primary">
                    <i class="fas fa-paper-plane me-1"></i> Kirim Tautan Pemulihan
                </button>
            </div>
        </form>

        <!-- Back to Login -->
        <div class="text-center mt-4">
            <a href="{{ route('login') }}" class="auth-link small"><i class="fas fa-arrow-left me-1"></i> Kembali ke Login</a>
        </div>
    </div>
</div>
@endsection
