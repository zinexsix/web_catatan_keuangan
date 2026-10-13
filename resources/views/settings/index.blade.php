@extends('layouts.app')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12">
        <h3 class="fw-bold mb-1">Pengaturan Aplikasi</h3>
        <p class="text-secondary mb-0">Atur preferensi aplikasi Anda dan kelola data cadangan.</p>
    </div>
</div>

<div class="row g-4">
    <!-- General Settings Form -->
    <div class="col-md-6 col-12">
        <div class="card glass-card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0"><i class="fas fa-sliders-h me-2 text-primary"></i>Preferensi Umum</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Tema -->
                    <div class="mb-3">
                        <label for="theme" class="form-label fw-semibold">Tema Tampilan</label>
                        <select name="theme" id="theme" class="form-select">
                            <option value="light" {{ $setting->theme === 'light' ? 'selected' : '' }}>Terang (Light)</option>
                            <option value="dark" {{ $setting->theme === 'dark' ? 'selected' : '' }}>Gelap (Dark)</option>
                        </select>
                    </div>

                    <!-- Bahasa -->
                    <div class="mb-3">
                        <label for="language" class="form-label fw-semibold">Bahasa Aplikasi</label>
                        <select name="language" id="language" class="form-select">
                            <option value="id" {{ $setting->language === 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                        </select>
                    </div>

                    <!-- Format Mata Uang -->
                    <div class="mb-4">
                        <label for="currency" class="form-label fw-semibold">Format Mata Uang</label>
                        <select name="currency" id="currency" class="form-select">
                            <option value="IDR" {{ $setting->currency === 'IDR' ? 'selected' : '' }}>Rupiah (IDR)</option>
                        </select>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary py-2.5"><i class="fas fa-save me-1"></i> Simpan Preferensi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Backup & Restore Panel -->
    <div class="col-md-6 col-12">
        <div class="card glass-card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0"><i class="fas fa-database me-2 text-info"></i>Cadangkan & Pemulihan Data</h5>
            </div>
            <div class="card-body p-4">
                <!-- Backup Section -->
                <div class="mb-4 p-3 border border-dashed rounded bg-light">
                    <h6 class="fw-bold text-dark mb-1"><i class="fas fa-download me-1 text-success"></i> Backup Data</h6>
                    <p class="text-secondary small mb-3">Unduh cadangan data transaksi, kategori, anggaran, target tabungan, tagihan, dan preferensi akun Anda dalam bentuk file JSON.</p>
                    <a href="{{ route('settings.backup') }}" class="btn btn-success w-100 py-2"><i class="fas fa-cloud-arrow-down me-1"></i> Unduh File Backup</a>
                </div>

                <!-- Restore Section -->
                <div class="p-3 border border-dashed rounded bg-light">
                    <h6 class="fw-bold text-dark mb-1"><i class="fas fa-upload me-1 text-danger"></i> Restore Data</h6>
                    <p class="text-secondary small mb-3">Pulihkan data akun Anda dari file cadangan (.json) yang telah Anda unduh sebelumnya.</p>
                    
                    <form action="{{ route('settings.restore') }}" method="POST" enctype="multipart/form-data" id="restoreForm">
                        @csrf
                        <div class="mb-3">
                            <input type="file" name="backup_file" id="backup_file" class="form-control" accept=".json,application/json" required>
                        </div>
                        <button type="submit" class="btn btn-danger w-100 py-2"><i class="fas fa-clock-rotate-left me-1"></i> Pulihkan Data</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // ── Restore File Alert Trigger ──
    $('#restoreForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Pulihkan Data?',
            text: "Proses ini akan menimpa seluruh data transaksi, kategori, target tabungan, dan anggaran Anda yang ada saat ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, timpa & pulihkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endsection
