@extends('layouts.auth')

@section('content')
<div class="glass-auth-card">
    <div class="auth-form-side">
        <!-- Header -->
        <div class="text-center mb-4">
            <div class="auth-brand-icon"><i class="fas fa-lock"></i></div>
            <h1 class="welcome-title">Reset Kata Sandi</h1>
            <p class="welcome-subtitle">Masukkan kata sandi baru untuk akun Anda.</p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="needs-validation" novalidate>
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div class="mb-3">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i> Alamat Email
                </label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $request->email) }}" required autocomplete="username" readonly>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i> Kata Sandi Baru
                </label>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required autocomplete="new-password" autofocus>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <label for="password_confirmation" class="form-label">
                    <i class="fas fa-unlock-keyhole"></i> Konfirmasi Kata Sandi Baru
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="••••••••" required autocomplete="new-password">
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit button -->
            <div class="d-grid">
                <button type="submit" class="btn btn-auth-primary">
                    <i class="fas fa-key me-1"></i> Perbarui Kata Sandi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
