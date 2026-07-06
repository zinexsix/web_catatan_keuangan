@extends('layouts.auth')

@section('content')
<div class="glass-auth-card">
    <div class="auth-form-side">
        <!-- Header -->
        <div class="text-center mb-4">
            <div class="auth-brand-icon"><i class="fas fa-envelope-circle-check"></i></div>
            <h1 class="welcome-title">Verifikasi Email</h1>
            <p class="welcome-subtitle">Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda melalui tautan yang baru saja kami kirimkan.</p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success border-0 small mb-4 py-2" role="alert">
                Tautan verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran.
            </div>
        @endif

        <div class="d-grid gap-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-auth-primary w-100">
                    <i class="fas fa-rotate me-1"></i> Kirim Ulang Email Verifikasi
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="text-center">
                @csrf
                <button type="submit" class="btn btn-link text-danger small auth-link text-decoration-none">
                    <i class="fas fa-sign-out-alt me-1"></i> Keluar / Logout
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
