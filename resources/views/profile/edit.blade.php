@extends('layouts.app')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12">
        <h3 class="fw-bold mb-1 text-primary">Pengaturan Profil</h3>
        <p class="text-secondary mb-0">Kelola informasi pribadi, ubah kata sandi, dan hapus akun Anda.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Informasi Profil -->
    <div class="col-lg-6">
        <div class="card glass-card border-0 shadow-sm mb-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="rounded-circle bg-primary-subtle text-primary p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-id-card fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-primary">Informasi Profil</h5>
                        <p class="text-secondary small mb-0">Perbarui nama dan alamat email akun Anda.</p>
                    </div>
                </div>
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>
    </div>

    <!-- Ubah Kata Sandi -->
    <div class="col-lg-6">
        <div class="card glass-card border-0 shadow-sm mb-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="rounded-circle bg-success-subtle text-success p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-key fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-primary">Ubah Kata Sandi</h5>
                        <p class="text-secondary small mb-0">Pastikan akun Anda menggunakan kata sandi yang aman.</p>
                    </div>
                </div>
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>

    <!-- Hapus Akun -->
    <div class="col-12">
        <div class="card glass-card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="rounded-circle bg-danger-subtle text-danger p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-user-xmark fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-danger">Hapus Akun</h5>
                        <p class="text-secondary small mb-0">Sekali Anda menghapus akun, semua data akan hilang secara permanen.</p>
                    </div>
                </div>
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection
