@extends('layouts.app')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div>
            <h3 class="fw-bold mb-1">Daftar Transaksi</h3>
            <p class="text-secondary mb-0">Kelola riwayat pemasukan dan pengeluaran Anda.</p>
        </div>
        <a href="{{ route('transactions.create') }}" class="btn btn-primary rounded-pill px-4 mt-3 mt-md-0 d-none d-md-inline-block">
            <i class="fas fa-plus me-2"></i> Tambah Transaksi
        </a>
    </div>
</div>

<!-- Filter & Search Panel -->
<div class="card glass-card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form action="{{ route('transactions.index') }}" method="GET" id="filterForm">
            @php
                $activeFilters = collect(['type', 'category_id', 'timeframe', 'sort'])->filter(fn ($key) => request()->filled($key))->count();
            @endphp
            <div class="row g-3">
                <!-- Search -->
                <div class="col-12 col-md-3">
                    <label class="form-label small text-secondary d-none d-md-block">Cari Transaksi</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 text-secondary">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" value="{{ request('search') }}" placeholder="Cari judul, deskripsi...">
                        <button type="button" class="btn btn-outline-secondary d-md-none" data-bs-toggle="collapse" data-bs-target="#moreFilters" aria-expanded="false" aria-controls="moreFilters">
                            <i class="fas fa-sliders"></i>
                            @if($activeFilters)
                                <span class="badge bg-primary rounded-pill ms-1">{{ $activeFilters }}</span>
                            @endif
                        </button>
                    </div>
                </div>

                <div class="col-12 col-md-9">
                <div class="collapse d-md-block" id="moreFilters">
                <div class="row g-3">
                <!-- Jenis -->
                <div class="col-6 col-md-3">
                    <label class="form-label small text-secondary">Jenis</label>
                    <select name="type" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Jenis</option>
                        <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                </div>

                <!-- Kategori -->
                <div class="col-6 col-md-3">
                    <label class="form-label small text-secondary">Kategori</label>
                    <select name="category_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }} ({{ $cat->type === 'income' ? 'Masuk' : 'Keluar' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Rentang Waktu -->
                <div class="col-6 col-md-3">
                    <label class="form-label small text-secondary">Waktu</label>
                    <select name="timeframe" id="timeframeSelect" class="form-select" onchange="toggleCustomDates(this.value)">
                        <option value="">Semua Waktu</option>
                        <option value="today" {{ request('timeframe') === 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="week" {{ request('timeframe') === 'week' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="month" {{ request('timeframe') === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="year" {{ request('timeframe') === 'year' ? 'selected' : '' }}>Tahun Ini</option>
                        <option value="custom" {{ request('timeframe') === 'custom' ? 'selected' : '' }}>Custom Tanggal</option>
                    </select>
                </div>

                <!-- Urutkan -->
                <div class="col-6 col-md-3">
                    <label class="form-label small text-secondary">Urutkan</label>
                    <select name="sort" class="form-select" onchange="this.form.submit()">
                        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="highest_amount" {{ request('sort') === 'highest_amount' ? 'selected' : '' }}>Nominal Terbesar</option>
                        <option value="lowest_amount" {{ request('sort') === 'lowest_amount' ? 'selected' : '' }}>Nominal Terkecil</option>
                    </select>
                </div>
                </div>
                </div>
                </div>
            </div>

            <!-- Custom Date Range Row -->
            <div class="row g-3 mt-2 {{ request('timeframe') === 'custom' ? '' : 'd-none' }}" id="customDatesRow">
                <div class="col-md-3">
                    <label class="form-label small text-secondary">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="startDate" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-secondary">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="endDate" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> Terapkan</button>
                </div>
            </div>
            
            @if(request()->hasAny(['search', 'type', 'category_id', 'timeframe', 'sort']))
            <div class="d-flex justify-content-end mt-3 gap-2">
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 btn-sm">Reset Filter</a>
            </div>
            @endif
        </form>
    </div>
</div>

<!-- Transactions Table -->
<div class="card glass-card border-0 shadow-sm">
    <div class="card-body p-4">
        <!-- Mobile list -->
        <div class="d-md-none mobile-list">
            @forelse($transactions as $tx)
                <div class="mobile-list-item">
                    <span class="mobile-list-icon" style="background-color: {{ $tx->category->color ?? '#6366f1' }};">
                        <i class="fas {{ $tx->category->icon ?? 'fa-tag' }}"></i>
                    </span>
                    <div class="mobile-list-body">
                        <div class="d-flex justify-content-between gap-2">
                            <strong class="text-truncate">{{ $tx->title }}</strong>
                            <span class="fw-bold text-nowrap text-{{ $tx->type === 'income' ? 'success' : 'danger' }}">
                                {{ $tx->type === 'income' ? '+' : '-' }} {{ $tx->formatted_amount }}
                            </span>
                        </div>
                        <div class="small text-secondary text-truncate">
                            {{ $tx->category->name ?? 'Tanpa Kategori' }} · {{ $tx->date->translatedFormat('d M Y') }} · {{ $tx->payment_method_label }}
                        </div>
                        <div class="mobile-list-actions">
                            @if($tx->receipt_image)
                                <button class="btn btn-sm btn-light preview-receipt-btn" data-url="{{ $tx->receipt_image }}">
                                    <i class="fas fa-image me-1"></i> Bukti
                                </button>
                            @endif
                            <a href="{{ route('transactions.edit', $tx->id) }}" class="btn btn-sm btn-light">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <form action="{{ route('transactions.destroy', $tx->id) }}" method="POST" class="d-inline delete-transaction-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light text-danger">
                                    <i class="fas fa-trash me-1"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-receipt fs-1 mb-3 text-secondary"></i>
                    <h6>Belum Ada Data Transaksi</h6>
                    <p class="text-secondary small mb-0">Ketuk tombol + untuk mencatat transaksi pertama Anda.</p>
                </div>
            @endforelse
        </div>

        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Kategori</th>
                        <th>Metode</th>
                        <th>Nominal</th>
                        <th width="80">Bukti</th>
                        <th class="text-end" width="130">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                        <tr>
                            <td>{{ $tx->date->translatedFormat('d M Y') }}</td>
                            <td>
                                <div>
                                    <strong class="text-primary">{{ $tx->title }}</strong>
                                    @if($tx->description)
                                        <p class="mb-0 text-muted small text-truncate" style="max-width: 250px;">{{ $tx->description }}</p>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="rounded-circle d-flex align-items-center justify-content-center text-white me-2" style="width: 32px; height: 32px; background-color: {{ $tx->category->color ?? '#6366f1' }}; font-size: 0.85rem;">
                                        <i class="fas {{ $tx->category->icon ?? 'fa-tag' }}"></i>
                                    </span>
                                    <span>{{ $tx->category->name ?? 'Tanpa Kategori' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary px-2 py-1.5 rounded">
                                    {{ $tx->payment_method_label }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-{{ $tx->type === 'income' ? 'success' : 'danger' }}">
                                    {{ $tx->type === 'income' ? '+' : '-' }} {{ $tx->formatted_amount }}
                                </span>
                            </td>
                            <td>
                                @if($tx->receipt_image)
                                    <button class="btn btn-sm btn-outline-info p-1 preview-receipt-btn" data-url="{{ $tx->receipt_image }}">
                                        <i class="fas fa-image fs-5"></i>
                                    </button>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('transactions.edit', $tx->id) }}" class="btn btn-sm btn-outline-primary border-0 me-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('transactions.destroy', $tx->id) }}" method="POST" class="d-inline delete-transaction-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state border-0 bg-transparent">
                                    <i class="fas fa-receipt fs-1 mb-3"></i>
                                    <h5>Belum Ada Data Transaksi</h5>
                                    <p class="text-secondary small">Mulailah mencatat pemasukan atau pengeluaran Anda untuk melihat riwayat di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Links -->
        <div class="mt-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <span class="small text-secondary">Menampilkan {{ $transactions->firstItem() ?? 0 }} - {{ $transactions->lastItem() ?? 0 }} dari {{ $transactions->total() }} data</span>
            <div>
                {{ $transactions->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleCustomDates(value) {
    const customRow = document.getElementById('customDatesRow');
    if (value === 'custom') {
        customRow.classList.remove('d-none');
    } else {
        customRow.classList.add('d-none');
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('filterForm').submit();
    }
}

$(document).ready(function() {
    // ── Preview Receipt Image ──
    $('.preview-receipt-btn').on('click', function() {
        const url = $(this).data('url');
        Swal.fire({
            title: 'Bukti Foto Transaksi',
            imageUrl: url,
            imageAlt: 'Bukti Foto Transaksi',
            confirmButtonColor: '#6366f1',
            confirmButtonText: 'Tutup'
        });
    });

    // ── Delete Transaction Form Confirmation ──
    $('.delete-transaction-form').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Hapus Transaksi?',
            text: "Data transaksi yang dihapus tidak dapat dipulihkan!",
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
