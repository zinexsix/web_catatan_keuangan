<div>
    <p class="text-secondary small mb-3">
        Setelah akun Anda dihapus, semua sumber daya dan data di dalamnya akan dihapus secara permanen. Silakan unduh cadangan data Anda terlebih dahulu jika diperlukan.
    </p>

    <button type="button" class="btn btn-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
        <i class="fas fa-trash-can me-2"></i>Hapus Akun Saya
    </button>

    <!-- Bootstrap 5 Modal -->
    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-card border-0">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-danger" id="confirmUserDeletionModalLabel">Hapus Akun Permanen?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    
                    <div class="modal-body px-4">
                        <p class="text-secondary small mb-3">
                            Apakah Anda yakin ingin menghapus akun Anda secara permanen? Sekali dihapus, seluruh data riwayat keuangan Anda tidak dapat dikembalikan. Silakan masukkan kata sandi Anda untuk mengonfirmasi tindakan ini.
                        </p>
                        
                        <!-- Password input -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold text-secondary small">Kata Sandi Anda</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0 text-secondary"><i class="fas fa-lock"></i></span>
                                <input type="password" name="password" id="password" class="form-control border-start-0 @error('password', 'userDeletion') is-invalid @enderror" placeholder="••••••••" required>
                                @error('password', 'userDeletion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer border-top-0 pb-4 px-4">
                        <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4">Hapus Akun Permanen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if($errors->userDeletion->isNotEmpty())
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var deleteModal = new bootstrap.Modal(document.getElementById('confirmUserDeletionModal'));
            deleteModal.show();
        });
    </script>
    @endpush
@endif
