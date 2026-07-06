@extends('layouts.app')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12">
        <h3 class="fw-bold mb-1">Laporan Keuangan</h3>
        <p class="text-secondary mb-0">Lihat analisis pengeluaran dan pemasukan Anda secara mendalam.</p>
    </div>
</div>

<!-- Report Period Filter Card -->
<div class="card glass-card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form action="{{ route('reports.index') }}" method="GET" id="reportFilterForm">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="timeframe" class="form-label small text-secondary">Rentang Waktu</label>
                    <select name="timeframe" id="timeframeSelect" class="form-select" onchange="toggleCustomDates(this.value)">
                        <option value="today" {{ request('timeframe') === 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="week" {{ request('timeframe') === 'week' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="month" {{ request('timeframe') === 'month' || !request('timeframe') ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="year" {{ request('timeframe') === 'year' ? 'selected' : '' }}>Tahun Ini</option>
                        <option value="custom" {{ request('timeframe') === 'custom' ? 'selected' : '' }}>Custom Tanggal</option>
                    </select>
                </div>

                <div class="col-md-3 custom-date-input {{ request('timeframe') === 'custom' ? '' : 'd-none' }}">
                    <label for="start_date" class="form-label small text-secondary">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="startDate" class="form-control" value="{{ request('start_date') }}">
                </div>

                <div class="col-md-3 custom-date-input {{ request('timeframe') === 'custom' ? '' : 'd-none' }}">
                    <label for="end_date" class="form-label small text-secondary">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="endDate" class="form-control" value="{{ request('end_date') }}">
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 py-2"><i class="fas fa-arrows-rotate me-2"></i> Terapkan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Export Action Buttons -->
<div class="d-flex justify-content-end gap-2 mb-4">
    <!-- PDF Export -->
    <a href="{{ route('reports.pdf', request()->query()) }}" class="btn btn-outline-danger px-3 py-2 rounded-pill small">
        <i class="fas fa-file-pdf me-1"></i> Cetak PDF
    </a>
    <!-- Excel Export -->
    <a href="{{ route('reports.excel', request()->query()) }}" class="btn btn-outline-success px-3 py-2 rounded-pill small">
        <i class="fas fa-file-excel me-1"></i> Ekspor Excel
    </a>
    <!-- Print Window -->
    <a href="{{ route('reports.print', request()->query()) }}" target="_blank" class="btn btn-outline-primary px-3 py-2 rounded-pill small">
        <i class="fas fa-print me-1"></i> Print Laporan
    </a>
</div>

<!-- Aggregated Summary Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card sum-card income">
            <div class="card-body p-4 text-center">
                <span class="text-white-50 small text-uppercase fw-semibold d-block mb-1">Total Pemasukan</span>
                <h3 class="fw-bold mb-0">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card sum-card expense">
            <div class="card-body p-4 text-center">
                <span class="text-white-50 small text-uppercase fw-semibold d-block mb-1">Total Pengeluaran</span>
                <h3 class="fw-bold mb-0">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card sum-card net">
            <div class="card-body p-4 text-center">
                <span class="text-white-50 small text-uppercase fw-semibold d-block mb-1">Pendapatan Bersih</span>
                <h3 class="fw-bold mb-0">Rp {{ number_format($netIncome, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Expense Category Chart -->
    <div class="col-lg-5 col-12">
        <div class="card glass-card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0"><i class="fas fa-chart-pie me-2 text-danger"></i>Distribusi Pengeluaran</h5>
            </div>
            <div class="card-body p-4 d-flex align-items-center justify-content-center">
                @if(count($chartLabels) > 0)
                    <div style="height: 280px; width: 100%;">
                        <canvas id="reportCategoryDistributionChart"></canvas>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-chart-pie fs-1 text-muted opacity-25 mb-3"></i>
                        <p class="text-secondary small mb-0">Tidak ada pengeluaran pada periode ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Details Table -->
    <div class="col-lg-7 col-12">
        <div class="card glass-card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0"><i class="fas fa-list me-2 text-primary"></i>Detail Transaksi Periode</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th class="text-end">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $tx)
                                <tr>
                                    <td>{{ $tx->date->format('d-m-Y') }}</td>
                                    <td>
                                        <strong class="text-primary">{{ $tx->title }}</strong>
                                        <span class="d-block text-secondary small">{{ $tx->payment_method_label }}</span>
                                    </td>
                                    <td>{{ $tx->category->name ?? 'Lainnya' }}</td>
                                    <td class="text-end fw-bold text-{{ $tx->type === 'income' ? 'success' : 'danger' }}">
                                        {{ $tx->type === 'income' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        Tidak ada data transaksi pada periode ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleCustomDates(value) {
    const inputs = document.querySelectorAll('.custom-date-input');
    if (value === 'custom') {
        inputs.forEach(el => el.classList.remove('d-none'));
    } else {
        inputs.forEach(el => el.classList.add('d-none'));
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('reportFilterForm').submit();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('reportCategoryDistributionChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    data: {!! json_encode($chartTotals) !!},
                    backgroundColor: {!! json_encode($chartColors) !!},
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
});
</script>
@endsection
