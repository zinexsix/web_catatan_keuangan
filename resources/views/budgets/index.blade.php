@extends('layouts.app')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div>
            <h3 class="fw-bold mb-1">Budget Bulanan</h3>
            <p class="text-secondary mb-0">Atur batasan pengeluaran Anda untuk mengontrol keuangan bulanan.</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4 mt-3 mt-md-0" data-bs-toggle="modal" data-bs-target="#createBudgetModal">
            <i class="fas fa-plus me-2"></i> Atur Anggaran
        </button>
    </div>
</div>

<!-- Period Selection Card -->
<div class="card glass-card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form action="{{ route('budgets.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="month" class="form-label small text-secondary">Pilih Bulan</label>
                <select name="month" id="month" class="form-select">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <label for="year" class="form-label small text-secondary">Pilih Tahun</label>
                <select name="year" id="year" class="form-select">
                    @for($y = now()->year - 2; $y <= now()->year + 5; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100 py-2"><i class="fas fa-calendar-check me-2"></i> Tampilkan Anggaran</button>
            </div>
        </form>
    </div>
</div>

<!-- Budgets Listing -->
<div class="row g-4">
    @forelse($budgets as $budget)
        @php
            $spent = $budget->spent_amount;
            $limit = $budget->amount;
            $percentage = $budget->usage_percentage;
            
            // Color logic
            if ($percentage >= 100) {
                $progressBarColor = 'danger';
                $statusLabel = 'Over Budget';
                $statusBadge = 'danger';
            } elseif ($percentage >= 80) {
                $progressBarColor = 'warning';
                $statusLabel = 'Mendekati Batas';
                $statusBadge = 'warning';
            } else {
                $progressBarColor = 'success';
                $statusLabel = 'Aman';
                $statusBadge = 'success';
            }
        @endphp
        <div class="col-md-6 col-lg-4">
            <div class="card glass-card border-0 shadow-sm h-100">
                <div class="card-body p-4 d-flex flex-column">
                    <!-- Category header & Options dropdown -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-{{ $statusBadge }}-subtle text-{{ $statusBadge }} px-3 py-1.5 rounded-pill small">
                            {{ $statusLabel }}
                        </span>
                        <div class="dropdown">
                            <button class="btn btn-link text-secondary p-0" id="budgetDropdown{{ $budget->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="budgetDropdown{{ $budget->id }}" style="border-radius: 12px;">
                                <li>
                                    <button class="dropdown-item edit-budget-btn py-2"
                                        data-id="{{ $budget->id }}"
                                        data-category-id="{{ $budget->category_id ?? '' }}"
                                        data-amount="{{ $budget->amount }}"
                                        data-month="{{ $budget->month }}"
                                        data-year="{{ $budget->year }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editBudgetModal">
                                        <i class="fas fa-edit me-2 text-muted"></i> Edit Anggaran
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('budgets.destroy', $budget->id) }}" method="POST" class="delete-budget-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger py-2">
                                            <i class="fas fa-trash me-2"></i> Hapus
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Category Icon & Name -->
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white me-3" style="width: 46px; height: 46px; background-color: {{ $budget->category->color ?? '#6366f1' }};">
                            <i class="fas {{ $budget->category->icon ?? 'fa-sack-dollar' }} fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-primary">
                                {{ $budget->category->name ?? 'Anggaran Keseluruhan' }}
                            </h5>
                            <small class="text-secondary">Periode: {{ \Carbon\Carbon::create(null, $budget->month, 1)->translatedFormat('F') }} {{ $budget->year }}</small>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <span class="small text-secondary d-block">Terpakai</span>
                            <strong class="text-{{ $progressBarColor }} fs-5">Rp {{ number_format($spent, 0, ',', '.') }}</strong>
                        </div>
                        <div class="col-6 text-end">
                            <span class="small text-secondary d-block">Batas Anggaran</span>
                            <strong class="text-primary fs-5">Rp {{ number_format($limit, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between mb-1 small fw-semibold">
                            <span>Sisa: Rp {{ number_format(max(0, $limit - $spent), 0, ',', '.') }}</span>
                            <span>{{ $percentage }}%</span>
                        </div>
                        <div class="progress rounded-pill" style="height: 10px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-{{ $progressBarColor }}" role="progressbar" style="width: {{ min(100, $percentage) }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="empty-state">
                <i class="fas fa-sack-dollar mb-3 fs-1 text-secondary opacity-50"></i>
                <h5 class="fw-bold">Belum Ada Anggaran untuk Periode Ini</h5>
                <p class="text-secondary small">Mulailah membatasi pengeluaran Anda dengan mengatur anggaran untuk semua kategori atau kategori spesifik.</p>
                <button class="btn btn-primary rounded-pill px-4 mt-2" data-bs-toggle="modal" data-bs-target="#createBudgetModal">
                    Atur Anggaran Pertama
                </button>
            </div>
        </div>
    @endforelse
</div>

<!-- Create Modal -->
<div class="modal fade" id="createBudgetModal" tabindex="-1" aria-labelledby="createBudgetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="createBudgetModalLabel">Atur Batas Anggaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('budgets.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <!-- Kategori Selector -->
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Kategori Pengeluaran</label>
                        <select name="category_id" class="form-select">
                            <option value="">Anggaran Keseluruhan (Semua Pengeluaran)</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted mt-1 d-block">Pilih kategori spesifik atau biarkan kosong untuk anggaran total belanja bulanan.</small>
                    </div>

                    <div class="row">
                        <!-- Bulan -->
                        <div class="col-6 mb-3">
                            <label for="create_month" class="form-label">Bulan</label>
                            <select name="month" id="create_month" class="form-select">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <!-- Tahun -->
                        <div class="col-6 mb-3">
                            <label for="create_year" class="form-label">Tahun</label>
                            <select name="year" id="create_year" class="form-select">
                                @for($y = now()->year - 2; $y <= now()->year + 5; $y++)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Nominal Anggaran -->
                    <div class="mb-3">
                        <label for="amount" class="form-label">Batas Anggaran Bulanan (Rp)</label>
                        <input type="number" inputmode="numeric" name="amount" class="form-control" placeholder="cth: 2000000" min="0.01" step="any" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Anggaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editBudgetModal" tabindex="-1" aria-labelledby="editBudgetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="editBudgetModalLabel">Edit Batas Anggaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editBudgetForm">
                @csrf
                @method('PUT')
                <div class="modal-body px-4">
                    <!-- Kategori Selector (Disabled in edit to prevent keys alteration) -->
                    <div class="mb-3">
                        <label for="edit_category_id" class="form-label">Kategori Pengeluaran</label>
                        <select name="category_id" id="edit_category_id" class="form-select" readonly style="pointer-events: none; background-color: #e9ecef;">
                            <option value="">Anggaran Keseluruhan (Semua Pengeluaran)</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <!-- Bulan -->
                        <div class="col-6 mb-3">
                            <label for="edit_month" class="form-label">Bulan</label>
                            <select name="month" id="edit_month" class="form-select" readonly style="pointer-events: none; background-color: #e9ecef;">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}">{{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}</option>
                                @endfor
                            </select>
                        </div>
                        <!-- Tahun -->
                        <div class="col-6 mb-3">
                            <label for="edit_year" class="form-label">Tahun</label>
                            <select name="year" id="edit_year" class="form-select" readonly style="pointer-events: none; background-color: #e9ecef;">
                                @for($y = now()->year - 2; $y <= now()->year + 5; $y++)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Nominal Anggaran -->
                    <div class="mb-3">
                        <label for="edit_amount" class="form-label">Batas Anggaran Bulanan (Rp)</label>
                        <input type="number" inputmode="numeric" name="amount" id="edit_amount" class="form-control" min="0.01" step="any" required>
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
    $('.edit-budget-btn').on('click', function() {
        const id = $(this).data('id');
        const categoryId = $(this).data('category-id');
        const amount = $(this).data('amount');
        const month = $(this).data('month');
        const year = $(this).data('year');

        // Set action url
        $('#editBudgetForm').attr('action', `/budgets/${id}`);
        
        // Populate inputs
        $('#edit_category_id').val(categoryId);
        $('#edit_amount').val(amount);
        $('#edit_month').val(month);
        $('#edit_year').val(year);
    });

    // ── SweetAlert Delete Confirmation ──
    $('.delete-budget-form').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Hapus Anggaran Bulanan?',
            text: "Data anggaran yang dihapus tidak dapat dipulihkan!",
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

    // ── Action Redirect modal trigger ──
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('action') === 'create') {
        const createModal = new bootstrap.Modal(document.getElementById('createBudgetModal'));
        createModal.show();
    }
});
</script>
@endsection
