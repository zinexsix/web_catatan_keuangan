<form method="post" action="{{ route('password.update') }}" class="needs-validation">
    @csrf
    @method('put')

    <!-- Current Password -->
    <div class="mb-3">
        <label for="update_password_current_password" class="form-label fw-semibold text-secondary small">Kata Sandi Saat Ini</label>
        <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0 text-secondary"><i class="fas fa-key"></i></span>
            <input type="password" name="current_password" id="update_password_current_password" class="form-control border-start-0 @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password" placeholder="••••••••" required>
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- New Password -->
    <div class="mb-3">
        <label for="update_password_password" class="form-label fw-semibold text-secondary small">Kata Sandi Baru</label>
        <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0 text-secondary"><i class="fas fa-lock"></i></span>
            <input type="password" name="password" id="update_password_password" class="form-control border-start-0 @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password" placeholder="••••••••" required>
            @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Confirm Password -->
    <div class="mb-4">
        <label for="update_password_password_confirmation" class="form-label fw-semibold text-secondary small">Konfirmasi Kata Sandi Baru</label>
        <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0 text-secondary"><i class="fas fa-unlock-keyhole"></i></span>
            <input type="password" name="password_confirmation" id="update_password_password_confirmation" class="form-control border-start-0 @error('password_confirmation', 'updatePassword') is-invalid @enderror" autocomplete="new-password" placeholder="••••••••" required>
            @error('password_confirmation', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="d-flex align-items-center gap-3">
        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Sandi</button>

        @if (session('status') === 'password-updated')
            <span x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-success small fw-semibold"><i class="fas fa-circle-check me-1"></i>Sandi berhasil diubah.</span>
        @endif
    </div>
</form>
