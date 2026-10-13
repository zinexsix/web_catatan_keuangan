@extends('layouts.app')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-1">Tagihan Rutin</h3>
            <p class="text-secondary mb-0">Kelola dan pantau pengeluaran wajib bulanan Anda.</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createBillModal">
            <i class="fas fa-plus me-2"></i> Tambah Tagihan
        </button>
    </div>
</div>

<!-- Warning / Reminder Section -->
@php
    $upcomingBills = $bills->filter(function($b) { return $b->isDueSoon(3); });
    $overdueBills = $bills->where('status', 'overdue');
@endphp

@if($overdueBills->count() > 0)
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center" role="alert">
        <i class="fas fa-exclamation-triangle fs-4 me-3 text-danger"></i>
        <div>
            <strong>Tagihan Terlambat!</strong> Anda memiliki {{ $overdueBills->count() }} tagihan yang telah melewati tanggal jatuh tempo. Harap segera lakukan pembayaran.
        </div>
    </div>
@endif

@if($upcomingBills->count() > 0 && $overdueBills->count() == 0)
    <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center" role="alert">
        <i class="fas fa-bell-exclamation fs-4 me-3 text-warning"></i>
        <div>
            <strong>Pengingat Tagihan!</strong> Ada {{ $upcomingBills->count() }} tagihan yang akan jatuh tempo dalam waktu dekat (H-3). Jangan lupa membayar!
        </div>
    </div>
@endif

<!-- Bills Grid -->
<div class="row g-4">
    @forelse($bills as $bill)
        @php
            $isPaid = $bill->status === 'paid';
            $isOverdue = $bill->status === 'overdue';
        @endphp
        <div class="col-md-6 col-lg-4">
            <div class="card glass-card border-0 shadow-sm h-100">
                <div class="card-body p-4 d-flex flex-column">
                    <!-- Status Header & Options -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge bg-{{ $bill->status_badge }}-subtle text-{{ $bill->status_badge }} px-3 py-1.5 rounded-pill small">
                            {{ $bill->status_label }}
                        </span>
                        
                        <div class="dropdown">
                            <button class="btn btn-link text-secondary p-0" id="billDropdown{{ $bill->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="billDropdown{{ $bill->id }}" style="border-radius: 12px;">
                                <li>
                                    <button class="dropdown-item edit-bill-btn py-2"
                                        data-id="{{ $bill->id }}"
                                        data-name="{{ $bill->name }}"
                                        data-amount="{{ $bill->amount }}"
                                        data-due-day="{{ $bill->due_day }}"
                                        data-recurrence="{{ $bill->recurrence }}"
                                        data-status="{{ $bill->status }}"
                                        data-category="{{ $bill->category }}"
                                        data-icon="{{ $bill->icon }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editBillModal">
                                        <i class="fas fa-edit me-2 text-muted"></i> Edit Tagihan
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('bills.destroy', $bill->id) }}" method="POST" class="delete-bill-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger py-2">
                                            <i class="fas fa-trash me-2"></i> Hapus Tagihan
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Details Icon & Title -->
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white me-3" style="width: 48px; height: 48px;">
                            <i class="fas {{ $bill->icon }} fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-primary">{{ $bill->name }}</h5>
                            <span class="text-secondary small">Jatuh Tempo: Tanggal {{ $bill->due_day }} setiap bulan</span>
                        </div>
                    </div>

                    <!-- Nominal Amount -->
                    <div class="mb-4">
                        <span class="small text-secondary d-block">Nominal Tagihan</span>
                        <h4 class="fw-bold text-dark mb-0">{{ $bill->formatted_amount }}</h4>
                        <small class="text-muted">Periode: {{ ucfirst($bill->recurrence) }}</small>
                    </div>

                    <!-- Action Button at Bottom -->
                    <div class="mt-auto pt-3">
                        @if($isPaid)
                            <div class="d-grid">
                                <form action="{{ route('bills.reset', $bill->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-secondary w-100 rounded-pill">
                                        <i class="fas fa-rotate-left me-1"></i> Reset ke Belum Dibayar
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="d-grid">
                                <button class="btn btn-primary rounded-pill pay-bill-btn" data-id="{{ $bill->id }}" data-name="{{ $bill->name }}" data-amount="{{ $bill->formatted_amount }}">
                                    <i class="fas fa-credit-card me-1"></i> Bayar Sekarang
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="empty-state">
                <i class="fas fa-file-invoice-dollar mb-3 fs-1 text-secondary opacity-50"></i>
                <h5 class="fw-bold">Belum Ada Tagihan Rutin</h5>
                <p class="text-secondary small">Daftarkan tagihan rutin bulanan Anda seperti Listrik, WiFi, Netflix, Air, agar terpantau dan mendapat notifikasi pengingat.</p>
                <button class="btn btn-primary rounded-pill px-4 mt-2" data-bs-toggle="modal" data-bs-target="#createBillModal">
                    Tambah Tagihan Pertama
                </button>
            </div>
        </div>
    @endforelse
</div>

<!-- Icons Helper Data -->
@php
    $billIcons = [
        ['class' => 'fa-wifi', 'label' => 'Internet / WiFi'],
        ['class' => 'fa-bolt', 'label' => 'Listrik / Token'],
        ['class' => 'fa-tint', 'label' => 'Air PDAM'],
        ['class' => 'fa-tv', 'label' => 'Streaming TV (Netflix, dll)'],
        ['class' => 'fa-music', 'label' => 'Streaming Musik (Spotify, dll)'],
        ['class' => 'fa-heartbeat', 'label' => 'BPJS / Kesehatan'],
        ['class' => 'fa-mobile-alt', 'label' => 'Pulsa / Paket Data'],
        ['class' => 'fa-house', 'label' => 'Sewa / Kos'],
        ['class' => 'fa-car', 'label' => 'Cicilan Kendaraan'],
        ['class' => 'fa-file-invoice', 'label' => 'Lainnya']
    ];
@endphp

<!-- Create Modal -->
<div class="modal fade" id="createBillModal" tabindex="-1" aria-labelledby="createBillModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="createBillModalLabel">Tambah Tagihan Rutin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('bills.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Tagihan</label>
                        <input type="text" name="name" class="form-control" placeholder="cth: Tagihan Internet Indihome, BPJS" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="amount" class="form-label">Nominal (Rp)</label>
                            <input type="number" inputmode="numeric" name="amount" class="form-control" placeholder="cth: 350000" min="0.01" step="any" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="due_day" class="form-label">Tanggal Jatuh Tempo (1-31)</label>
                            <input type="number" name="due_day" class="form-control" placeholder="cth: 10" min="1" max="31" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="recurrence" class="form-label">Periode Ulang</label>
                            <select name="recurrence" class="form-select" required>
                                <option value="monthly">Bulanan</option>
                                <option value="yearly">Tahunan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Kategori Transaksi (Pencatatan)</label>
                            <input type="text" name="category" class="form-control" placeholder="cth: Internet, Listrik" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Representasi Icon</label>
                        <input type="hidden" name="icon" id="create_bill_icon" value="fa-file-invoice">
                        <div class="d-flex flex-wrap gap-2 border p-3 rounded bg-light" style="max-height: 150px; overflow-y: auto;">
                            @foreach($billIcons as $item)
                                <button type="button" class="btn btn-outline-secondary bill-icon-select px-3 py-2" data-icon="{{ $item['class'] }}" title="{{ $item['label'] }}">
                                    <i class="fas {{ $item['class'] }}"></i>
                                </button>
                            @endforeach
                        </div>
                        <div class="mt-2 small text-muted">Selected Icon: <i class="fas fa-file-invoice" id="create_bill_icon_preview"></i></div>
                    </div>
                    <input type="hidden" name="status" value="pending">
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Tagihan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editBillModal" tabindex="-1" aria-labelledby="editBillModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="editBillModalLabel">Edit Tagihan Rutin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editBillForm">
                @csrf
                @method('PUT')
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nama Tagihan</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_amount" class="form-label">Nominal (Rp)</label>
                            <input type="number" inputmode="numeric" name="amount" id="edit_amount" class="form-control" min="0.01" step="any" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_due_day" class="form-label">Tanggal Jatuh Tempo (1-31)</label>
                            <input type="number" name="due_day" id="edit_due_day" class="form-control" min="1" max="31" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_recurrence" class="form-label">Periode Ulang</label>
                            <select name="recurrence" id="edit_recurrence" class="form-select" required>
                                <option value="monthly">Bulanan</option>
                                <option value="yearly">Tahunan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_category" class="form-label">Kategori Transaksi (Pencatatan)</label>
                            <input type="text" name="category" id="edit_category" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Representasi Icon</label>
                        <input type="hidden" name="icon" id="edit_bill_icon" value="">
                        <div class="d-flex flex-wrap gap-2 border p-3 rounded bg-light" style="max-height: 150px; overflow-y: auto;">
                            @foreach($billIcons as $item)
                                <button type="button" class="btn btn-outline-secondary edit-bill-icon-select px-3 py-2" data-icon="{{ $item['class'] }}" title="{{ $item['label'] }}">
                                    <i class="fas {{ $item['class'] }}"></i>
                                </button>
                            @endforeach
                        </div>
                        <div class="mt-2 small text-muted">Selected Icon: <i class="fas fa-file-invoice" id="edit_bill_icon_preview"></i></div>
                    </div>
                    <input type="hidden" name="status" id="edit_status" value="">
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Pay Form helper -->
<form action="" method="POST" id="payBillForm" class="d-none">
    @csrf
    <input type="hidden" name="payment_method" id="pay_method_input">
</form>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // ── Create Modal Icon ──
    $('#createCategoryModal, #createBillModal').on('click', '.bill-icon-select', function() {
        const icon = $(this).data('icon');
        $('.bill-icon-select').removeClass('active btn-primary').addClass('btn-outline-secondary');
        $(this).addClass('active btn-primary').removeClass('btn-outline-secondary');
        $('#create_bill_icon').val(icon);
        $('#create_bill_icon_preview').attr('class', 'fas ' + icon);
    });

    // ── Edit Modal Handling ──
    $('.edit-bill-btn').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const amount = $(this).data('amount');
        const dueDay = $(this).data('due-day');
        const recurrence = $(this).data('recurrence');
        const status = $(this).data('status');
        const category = $(this).data('category');
        const icon = $(this).data('icon');

        // Set action url
        $('#editBillForm').attr('action', `/bills/${id}`);
        
        // Populate inputs
        $('#edit_name').val(name);
        $('#edit_amount').val(amount);
        $('#edit_due_day').val(dueDay);
        $('#edit_recurrence').val(recurrence);
        $('#edit_category').val(category);
        $('#edit_status').val(status);
        $('#edit_bill_icon').val(icon);
        $('#edit_bill_icon_preview').attr('class', 'fas ' + icon);

        // Highlight icon in list
        $('.edit-bill-icon-select').removeClass('active btn-primary').addClass('btn-outline-secondary');
        $(`.edit-bill-icon-select[data-icon="${icon}"]`).addClass('active btn-primary').removeClass('btn-outline-secondary');
    });

    // Edit Modal Icon Select
    $('#editBillModal').on('click', '.edit-bill-icon-select', function() {
        const icon = $(this).data('icon');
        $('.edit-bill-icon-select').removeClass('active btn-primary').addClass('btn-outline-secondary');
        $(this).addClass('active btn-primary').removeClass('btn-outline-secondary');
        $('#edit_bill_icon').val(icon);
        $('#edit_bill_icon_preview').attr('class', 'fas ' + icon);
    });

    // ── Pay Bill Confirmation (Modal overlay asking for payment method) ──
    $('.pay-bill-btn').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const amount = $(this).data('amount');
        
        Swal.fire({
            title: 'Bayar Tagihan?',
            html: `Membayar tagihan <strong>${name}</strong> sebesar <strong>${amount}</strong>.<br><br>Pilih Metode Pembayaran:`,
            input: 'select',
            inputOptions: {
                'transfer': 'Transfer Bank',
                'e-wallet': 'E-Wallet',
                'qris': 'QRIS',
                'cash': 'Cash/Tunai',
                'other': 'Lainnya'
            },
            inputPlaceholder: '-- Pilih Metode --',
            showCancelButton: true,
            confirmButtonText: 'Bayar & Catat Pengeluaran',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#6366f1',
            inputValidator: (value) => {
                if (!value) {
                    return 'Anda harus memilih metode pembayaran!'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Set form action and submit
                $('#payBillForm').attr('action', `/bills/${id}/pay`);
                $('#pay_method_input').val(result.value);
                $('#payBillForm').submit();
            }
        });
    });

    // ── SweetAlert Delete Confirmation ──
    $('.delete-bill-form').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Hapus Tagihan Rutin?',
            text: "Tagihan rutin yang dihapus tidak akan muncul lagi di periode berikutnya!",
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
});
</script>
@endsection
