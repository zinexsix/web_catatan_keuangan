<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}" class="needs-validation">
    @csrf
    @method('patch')

    <!-- Name -->
    <div class="mb-3">
        <label for="name" class="form-label fw-semibold text-secondary small">Nama Lengkap</label>
        <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0 text-secondary"><i class="fas fa-user"></i></span>
            <input type="text" name="name" id="name" class="form-control border-start-0 @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Email -->
    <div class="mb-4">
        <label for="email" class="form-label fw-semibold text-secondary small">Alamat Email</label>
        <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0 text-secondary"><i class="fas fa-envelope"></i></span>
            <input type="email" name="email" id="email" class="form-control border-start-0 @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mt-3 bg-warning-subtle text-warning-emphasis p-3 rounded-4 small">
                <span>Alamat email Anda belum terverifikasi.</span>
                <button form="send-verification" class="btn btn-link p-0 text-warning-emphasis text-decoration-underline fw-bold small align-baseline">Klik di sini untuk mengirim ulang email verifikasi.</button>

                @if (session('status') === 'verification-link-sent')
                    <div class="mt-2 text-success fw-bold small">Tautan verifikasi baru telah dikirim ke alamat email Anda.</div>
                @endif
            </div>
        @endif
    </div>

    <div class="d-flex align-items-center gap-3">
        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>

        @if (session('status') === 'profile-updated')
            <span x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-success small fw-semibold"><i class="fas fa-circle-check me-1"></i>Berhasil disimpan.</span>
        @endif
    </div>
</form>
