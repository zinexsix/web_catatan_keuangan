@extends('layouts.app')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-1">Target Keuangan</h3>
            <p class="text-secondary mb-0">Tetapkan dan pantau target menabung/impian finansial Anda.</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createTargetModal">
            <i class="fas fa-plus me-2"></i> Tambah Target
        </button>
    </div>
</div>

<!-- Saving Targets Grid -->
<div class="row g-4">
    @forelse($savingTargets as $target)
        @php
            $percentage = $target->progress_percentage;
            $isCompleted = $target->status === 'completed' || $target->is_achieved;
            $isCancelled = $target->status === 'cancelled';
        @endphp
        <div class="col-md-6 col-lg-4">
            <div class="card glass-card border-0 shadow-sm h-100 position-relative">
                <div class="card-body p-4 d-flex flex-column">
                    <!-- Status Badge -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge bg-{{ $isCompleted ? 'warning' : ($isCancelled ? 'secondary' : 'primary') }}-subtle text-{{ $isCompleted ? 'warning-emphasis' : ($isCancelled ? 'secondary' : 'primary') }} px-3 py-2 rounded-pill small">
                            @if($isCompleted)
                                <i class="fas fa-trophy me-1 text-warning"></i> Tercapai
                            @elseif($isCancelled)
                                Dibatalkan
                            @else
                                Aktif
                            @endif
                        </span>
                        <div class="dropdown">
                            <button class="btn btn-link text-secondary p-0" id="actionsDropdown{{ $target->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="actionsDropdown{{ $target->id }}" style="border-radius: 12px;">
                                <li>
                                    <button class="dropdown-item edit-target-btn py-2"
                                        data-id="{{ $target->id }}"
                                        data-name="{{ $target->name }}"
                                        data-target="{{ $target->target_amount }}"
                                        data-current="{{ $target->current_amount }}"
                                        data-deadline="{{ $target->deadline->toDateString() }}"
                                        data-status="{{ $target->status }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editTargetModal">
                                        <i class="fas fa-edit me-2 text-muted"></i> Edit Target
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('saving-targets.destroy', $target->id) }}" method="POST" class="delete-target-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger py-2">
                                            <i class="fas fa-trash me-2"></i> Hapus Target
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Target Name -->
                    <h5 class="fw-bold mb-1 text-primary">{{ $target->name }}</h5>
                    <p class="text-secondary small mb-4">
                        <i class="far fa-calendar-alt me-1"></i> Deadline: {{ $target->deadline->translatedFormat('d M Y') }}
                        @if(!$isCompleted && !$isCancelled)
                            <span class="d-block text-{{ $target->is_overdue ? 'danger' : 'info' }} fw-semibold mt-1">
                                {{ $target->is_overdue ? 'Sudah Melewati Batas!' : $target->days_remaining . ' Hari Tersisa' }}
                            </span>
                        @endif
                    </p>

                    <!-- Nominal Details -->
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <span class="small text-secondary d-block">Terkumpul</span>
                            <strong class="text-success fs-5">Rp {{ number_format($target->current_amount, 0, ',', '.') }}</strong>
                        </div>
                        <div class="text-end">
                            <span class="small text-secondary d-block">Target</span>
                            <strong class="text-primary fs-5">Rp {{ number_format($target->target_amount, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mt-auto pt-3">
                        <div class="d-flex justify-content-between mb-1 small fw-semibold">
                            <span>Progress</span>
                            <span>{{ $percentage }}%</span>
                        </div>
                        <div class="progress rounded-pill" style="height: 10px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-{{ $isCompleted ? 'warning' : 'primary' }}" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        @if(!$isCompleted && !$isCancelled && $target->remaining_amount > 0)
                            <div class="mt-3 text-center bg-light p-2 rounded border border-dashed small">
                                Kurang <strong class="text-primary">Rp {{ number_format($target->remaining_amount, 0, ',', '.') }}</strong> lagi
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="empty-state">
                <i class="fas fa-piggy-bank mb-3 fs-1 text-secondary opacity-50"></i>
                <h5 class="fw-bold">Belum Ada Target Keuangan</h5>
                <p class="text-secondary small">Mulai tetapkan target menabung untuk impian liburan, beli gadget baru, atau dana darurat Anda.</p>
                <button class="btn btn-primary rounded-pill px-4 mt-2" data-bs-toggle="modal" data-bs-target="#createTargetModal">
                    Buat Target Pertama
                </button>
            </div>
        </div>
    @endforelse
</div>

<!-- Create Modal -->
<div class="modal fade" id="createTargetModal" tabindex="-1" aria-labelledby="createTargetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="createTargetModalLabel">Tambah Target Keuangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('saving-targets.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Target / Impian</label>
                        <input type="text" name="name" class="form-control" placeholder="cth: Liburan Bali, Dana Darurat, Laptop Baru" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="target_amount" class="form-label">Target Nominal (Rp)</label>
                            <input type="number" name="target_amount" class="form-control" placeholder="cth: 10000000" min="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="current_amount" class="form-label">Nominal Terkumpul (Rp)</label>
                            <input type="number" name="current_amount" class="form-control" placeholder="cth: 1000000" min="0" value="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="deadline" class="form-label">Batas Waktu (Deadline)</label>
                        <input type="date" name="deadline" class="form-control" min="{{ \Carbon\Carbon::now()->toDateString() }}" required>
                    </div>
                    <input type="hidden" name="status" value="active">
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Target</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editTargetModal" tabindex="-1" aria-labelledby="editTargetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="editTargetModalLabel">Edit Target Keuangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editTargetForm">
                @csrf
                @method('PUT')
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nama Target / Impian</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_target" class="form-label">Target Nominal (Rp)</label>
                            <input type="number" name="target_amount" id="edit_target" class="form-control" min="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_current" class="form-label">Nominal Terkumpul (Rp)</label>
                            <input type="number" name="current_amount" id="edit_current" class="form-control" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_deadline" class="form-label">Batas Waktu (Deadline)</label>
                        <input type="date" name="deadline" id="edit_deadline" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select name="status" id="edit_status" class="form-select" required>
                            <option value="active">Aktif</option>
                            <option value="completed">Tercapai / Selesai</option>
                            <option value="cancelled">Dibatalkan</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // ── Edit Modal Handling ──
    $('.edit-target-btn').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const target = $(this).data('target');
        const current = $(this).data('current');
        const deadline = $(this).data('deadline');
        const status = $(this).data('status');

        // Set action url
        $('#editTargetForm').attr('action', `/saving-targets/${id}`);
        
        // Populate inputs
        $('#edit_name').val(name);
        $('#edit_target').val(target);
        $('#edit_current').val(current);
        $('#edit_deadline').val(deadline);
        $('#edit_status').val(status);
    });

    // ── SweetAlert Delete Confirmation ──
    $('.delete-target-form').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Hapus Target Keuangan?',
            text: "Data target keuangan yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // ── Check if redirect parameter has action=create ──
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('action') === 'create') {
        const createModal = new bootstrap.Modal(document.getElementById('createTargetModal'));
        createModal.show();
    }
});
</script>
@endsection
