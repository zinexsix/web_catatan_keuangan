@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-1">Daftar Kategori</h3>
            <p class="text-secondary mb-0">Kelola kategori pemasukan dan pengeluaran Anda.</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
            <i class="fas fa-plus me-2"></i> Tambah Kategori
        </button>
    </div>
</div>

<div class="card glass-card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="categoriesTable">
                <thead>
                    <tr>
                        <th width="56">Icon</th>
                        <th>Nama Kategori</th>
                        <th class="d-none d-md-table-cell">Jenis</th>
                        <th class="d-none d-md-table-cell">Warna</th>
                        <th class="d-none d-md-table-cell">Jumlah Transaksi</th>
                        <th class="text-end" width="110">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 42px; height: 42px; background-color: {{ $category->color }};">
                                    <i class="fas {{ $category->icon }}"></i>
                                </div>
                            </td>
                            <td>
                                <strong class="text-primary">{{ $category->name }}</strong>
                                <div class="d-md-none small text-secondary">
                                    {{ $category->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }} · {{ $category->transactions_count }} transaksi
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <span class="badge bg-{{ $category->type === 'income' ? 'success' : 'danger' }}-subtle text-{{ $category->type === 'income' ? 'success' : 'danger' }} px-3 py-2 rounded-pill small text-uppercase">
                                    {{ $category->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                </span>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <div class="d-flex align-items-center">
                                    <span class="d-inline-block rounded border me-2" style="width: 20px; height: 20px; background-color: {{ $category->color }};"></span>
                                    <code>{{ $category->color }}</code>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">{{ $category->transactions_count }} Transaksi</td>
                            <td class="text-end text-nowrap">
                                <button class="btn btn-sm btn-outline-primary border-0 me-1 edit-category-btn" 
                                    data-id="{{ $category->id }}"
                                    data-name="{{ $category->name }}"
                                    data-type="{{ $category->type }}"
                                    data-color="{{ $category->color }}"
                                    data-icon="{{ $category->icon }}"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editCategoryModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline delete-category-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <!-- Empty state handled by table but we can show default -->
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Icons Helper Data -->
@php
    $icons = [
        'fa-utensils', 'fa-car', 'fa-shopping-bag', 'fa-bolt', 'fa-wifi', 
        'fa-tint', 'fa-mobile-alt', 'fa-graduation-cap', 'fa-heartbeat', 
        'fa-film', 'fa-money-bill-wave', 'fa-gift', 'fa-chart-line', 
        'fa-laptop-code', 'fa-plus-circle', 'fa-ellipsis-h', 'fa-house',
        'fa-shirt', 'fa-plane', 'fa-gamepad', 'fa-briefcase', 'fa-coins'
    ];
@endphp

<!-- Create Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="createCategoryModalLabel">Tambah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Kategori</label>
                        <input type="text" name="name" class="form-control" placeholder="cth: Makanan, Transportasi" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-block">Jenis Kategori</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="type_income" value="income" checked>
                            <label class="form-check-label" for="type_income">Pemasukan</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="type_expense" value="expense">
                            <label class="form-check-label" for="type_expense">Pengeluaran</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="color" class="form-label">Warna Representasi</label>
                        <input type="color" name="color" class="form-control form-control-color w-100" value="#6366f1" style="height: 45px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pilih Icon (FontAwesome)</label>
                        <input type="hidden" name="icon" id="create_icon_input" value="fa-tag">
                        <div class="d-flex flex-wrap gap-2 border p-3 rounded bg-light" style="max-height: 150px; overflow-y: auto;">
                            @foreach($icons as $icon)
                                <button type="button" class="btn btn-outline-secondary icon-select-btn px-3 py-2" data-icon="{{ $icon }}">
                                    <i class="fas {{ $icon }}"></i>
                                </button>
                            @endforeach
                        </div>
                        <div class="mt-2 small text-muted">Selected Icon: <i class="fas fa-tag id="create_icon_preview"></i> <span id="create_icon_name">fa-tag</span></div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="editCategoryModalLabel">Edit Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editCategoryForm">
                @csrf
                @method('PUT')
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nama Kategori</label>
                        <input type="text" name="name" id="edit_name" class="form-control" placeholder="cth: Makanan, Transportasi" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-block">Jenis Kategori</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="edit_type_income" value="income">
                            <label class="form-check-label" for="edit_type_income">Pemasukan</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="edit_type_expense" value="expense">
                            <label class="form-check-label" for="edit_type_expense">Pengeluaran</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_color" class="form-label">Warna Representasi</label>
                        <input type="color" name="color" id="edit_color" class="form-control form-control-color w-100" style="height: 45px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pilih Icon (FontAwesome)</label>
                        <input type="hidden" name="icon" id="edit_icon_input" value="">
                        <div class="d-flex flex-wrap gap-2 border p-3 rounded bg-light" style="max-height: 150px; overflow-y: auto;">
                            @foreach($icons as $icon)
                                <button type="button" class="btn btn-outline-secondary icon-select-btn px-3 py-2" data-icon="{{ $icon }}">
                                    <i class="fas {{ $icon }}"></i>
                                </button>
                            @endforeach
                        </div>
                        <div class="mt-2 small text-muted">Selected Icon: <i class="fas fa-tag" id="edit_icon_preview"></i> <span id="edit_icon_name"></span></div>
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
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    // ── DataTables ──
    $('#categoriesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/id.json',
        },
        pageLength: 10,
        responsive: true
    });

    // ── Create Modal Icon Selection ──
    $('#createCategoryModal .icon-select-btn').on('click', function() {
        const icon = $(this).data('icon');
        $('#createCategoryModal .icon-select-btn').removeClass('active btn-primary').addClass('btn-outline-secondary');
        $(this).addClass('active btn-primary').removeClass('btn-outline-secondary');
        $('#create_icon_input').val(icon);
        $('#create_icon_preview').attr('class', 'fas ' + icon);
        $('#create_icon_name').text(icon);
    });

    // ── Edit Modal Handling ──
    $(document).on('click', '.edit-category-btn', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const type = $(this).data('type');
        const color = $(this).data('color');
        const icon = $(this).data('icon');

        // Set action url
        $('#editCategoryForm').attr('action', `/categories/${id}`);
        
        // Populate inputs
        $('#edit_name').val(name);
        $('#edit_color').val(color);
        
        if (type === 'income') {
            $('#edit_type_income').prop('checked', true);
        } else {
            $('#edit_type_expense').prop('checked', true);
        }

        $('#edit_icon_input').val(icon);
        $('#edit_icon_preview').attr('class', 'fas ' + icon);
        $('#edit_icon_name').text(icon);

        // Highlight selected icon in edit modal list
        $('#editCategoryModal .icon-select-btn').removeClass('active btn-primary').addClass('btn-outline-secondary');
        $(`#editCategoryModal .icon-select-btn[data-icon="${icon}"]`).addClass('active btn-primary').removeClass('btn-outline-secondary');
    });

    // Edit Modal Icon Selection
    $('#editCategoryModal .icon-select-btn').on('click', function() {
        const icon = $(this).data('icon');
        $('#editCategoryModal .icon-select-btn').removeClass('active btn-primary').addClass('btn-outline-secondary');
        $(this).addClass('active btn-primary').removeClass('btn-outline-secondary');
        $('#edit_icon_input').val(icon);
        $('#edit_icon_preview').attr('class', 'fas ' + icon);
        $('#edit_icon_name').text(icon);
    });

    // ── SweetAlert Delete Confirmation ──
    $(document).on('submit', '.delete-category-form', function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Semua transaksi di bawah kategori ini juga akan terhapus secara otomatis!",
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
