@extends('layouts.app')

@section('content')
@php
    $currencySymbol = auth()->user()->getOrCreateSetting()->currency ?? 'Rp';
    if (!function_exists('format_currency')) {
        function format_currency($amount, $symbol) {
            return $symbol . ' ' . number_format($amount, 0, ',', '.');
        }
    }
@endphp

<div class="row g-4 mb-4">
    <!-- Header Title -->
    <div class="col-12">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center align-items-start gap-3 w-100">
            <div>
                <h1 class="fw-bold mb-1 text-primary" style="font-size: 2.1rem; letter-spacing: -0.02em;">Dashboard</h1>
                <p class="text-secondary mb-0" style="font-size: 0.92rem;">Selamat datang kembali, <strong class="text-dark">{{ auth()->user()->name }}</strong>. Berikut ringkasan keuangan Anda.</p>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center bg-white p-3 rounded-4 shadow-sm border border-light" style="gap: 12px; min-width: 180px;">
                    <div class="d-flex flex-column text-start">
                        <span class="text-secondary" style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Bulan Ini</span>
                        <span class="fw-bold text-dark" style="font-size: 1.1rem; line-height: 1.2;">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
                    </div>
                    <div class="logo-box ms-auto bg-primary-subtle text-primary" style="width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chart-line fs-6"></i>
                    </div>
                </div>

                <a href="{{ route('transactions.create') }}" class="btn btn-primary rounded-pill px-4 py-2.5 shadow-sm d-none d-md-inline-block">
                    <i class="fas fa-plus me-2"></i> Tambah Transaksi
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Budget Exceeded or Warning Alert -->
@if($budgetStatus['has_budget'])
    @if($budgetStatus['is_exceeded'])
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 p-4" role="alert">
            <div class="d-flex align-items-start align-items-md-center flex-column flex-md-row">
                <div class="rounded-circle bg-danger-subtle p-3 me-3 mb-3 mb-md-0 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fas fa-exclamation-triangle fs-4 text-danger"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="alert-heading fw-bold mb-1 text-danger">Batas Anggaran Bulanan Terlewati!</h6>
                    <p class="mb-0 small text-secondary">Total pengeluaran Anda bulan ini (<strong>{{ format_currency($budgetStatus['spent'], $currencySymbol) }}</strong>) telah melebihi batas anggaran bulanan yang ditentukan sebesar (<strong>{{ format_currency($budgetStatus['limit'], $currencySymbol) }}</strong>).</p>
                    <div class="progress mt-2" style="height: 10px; border-radius: 10px;">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <a href="{{ route('budgets.index') }}" class="btn btn-danger rounded-pill px-4 ms-0 ms-md-4 mt-3 mt-md-0">Sesuaikan Anggaran</a>
            </div>
        </div>
    @elseif($budgetStatus['is_warning'])
        <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 p-4" role="alert">
            <div class="d-flex align-items-start align-items-md-center flex-column flex-md-row">
                <div class="rounded-circle bg-warning-subtle p-3 me-3 mb-3 mb-md-0 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fas fa-warning fs-4 text-warning"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="alert-heading fw-bold mb-1 text-warning">Peringatan Anggaran Bulanan!</h6>
                    <p class="mb-0 small text-secondary">Pengeluaran Anda bulan ini hampir mendekati limit anggaran bulanan. Total pengeluaran: <strong>{{ format_currency($budgetStatus['spent'], $currencySymbol) }}</strong> dari batas limit <strong>{{ format_currency($budgetStatus['limit'], $currencySymbol) }}</strong> (<strong>{{ $budgetStatus['percentage'] }}%</strong>).</p>
                    <div class="progress mt-2" style="height: 10px; border-radius: 10px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $budgetStatus['percentage'] }}%" aria-valuenow="{{ $budgetStatus['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <a href="{{ route('budgets.index') }}" class="btn btn-warning text-dark rounded-pill px-4 ms-0 ms-md-4 mt-3 mt-md-0">Kelola Anggaran</a>
            </div>
        </div>
    @else
        <div class="card glass-card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h6 class="fw-bold mb-0">Progress Anggaran Bulanan</h6>
                        <span class="text-secondary small">Batas anggaran: <strong>{{ format_currency($budgetStatus['limit'], $currencySymbol) }}</strong></span>
                    </div>
                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold">{{ $budgetStatus['percentage'] }}% Digunakan</span>
                </div>
                <div class="progress" style="height: 12px; border-radius: 10px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $budgetStatus['percentage'] }}%" aria-valuenow="{{ $budgetStatus['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="d-flex justify-content-between text-secondary small mt-2">
                    <span>Terpakai: <strong>{{ format_currency($budgetStatus['spent'], $currencySymbol) }}</strong></span>
                    <span>Tersisa: <strong>{{ format_currency(max(0, $budgetStatus['limit'] - $budgetStatus['spent']), $currencySymbol) }}</strong></span>
                </div>
            </div>
        </div>
    @endif
@else
    <div class="card glass-card border-0 shadow-sm mb-4">
        <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div>
                <h6 class="fw-bold mb-1 text-primary"><i class="fas fa-bullseye me-2"></i>Atur Anggaran Bulanan Anda!</h6>
                <p class="text-secondary mb-0 small">Anda belum menetapkan batas anggaran belanja bulanan untuk bulan ini. Menetapkan anggaran membantu Anda menghemat pengeluaran.</p>
            </div>
            <a href="{{ route('budgets.index') }}" class="btn btn-outline-primary rounded-pill px-4 mt-3 mt-md-0">Buat Anggaran Sekarang</a>
        </div>
    </div>
@endif

<!-- Summary Cards Grid -->
<div class="row g-4 mb-4">
    <!-- Pemasukan -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white" style="transition: transform 0.2s ease, box-shadow 0.2s ease;">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-semibold" style="font-size: 0.82rem; text-transform: capitalize;">Total Pemasukan</span>
                    <h3 class="fw-bold text-dark my-2" style="font-size: 1.6rem; letter-spacing: -0.02em;">{{ format_currency($incomeCurrentMonth, $currencySymbol) }}</h3>
                    <div class="d-flex align-items-center" style="font-size: 0.8rem;">
                        <span class="{{ $incomeDiff['dir'] === 'up' ? 'text-success' : 'text-danger' }} fw-bold me-1">
                            <i class="fas {{ $incomeDiff['dir'] === 'up' ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }} me-1"></i>{{ $incomeDiff['formatted'] }}
                        </span>
                        <span class="text-secondary">dari bulan lalu</span>
                    </div>
                </div>
                <div class="rounded-4 d-flex align-items-center justify-content-center bg-success-subtle text-success" style="width: 52px; height: 52px; flex-shrink: 0; border-radius: 14px !important;">
                    <i class="fas fa-dollar-sign fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Pengeluaran -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white" style="transition: transform 0.2s ease, box-shadow 0.2s ease;">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-semibold" style="font-size: 0.82rem; text-transform: capitalize;">Total Pengeluaran</span>
                    <h3 class="fw-bold text-dark my-2" style="font-size: 1.6rem; letter-spacing: -0.02em;">{{ format_currency($expenseCurrentMonth, $currencySymbol) }}</h3>
                    <div class="d-flex align-items-center" style="font-size: 0.8rem;">
                        <span class="{{ $expenseDiff['dir'] === 'down' ? 'text-success' : 'text-danger' }} fw-bold me-1">
                            <i class="fas {{ $expenseDiff['dir'] === 'up' ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }} me-1"></i>{{ $expenseDiff['formatted'] }}
                        </span>
                        <span class="text-secondary">dari bulan lalu</span>
                    </div>
                </div>
                <div class="rounded-4 d-flex align-items-center justify-content-center bg-danger-subtle text-danger" style="width: 52px; height: 52px; flex-shrink: 0; border-radius: 14px !important;">
                    <i class="fas fa-wallet fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Saldo Bersih / Pendapatan Bersih -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white" style="transition: transform 0.2s ease, box-shadow 0.2s ease;">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-semibold" style="font-size: 0.82rem; text-transform: capitalize;">Pendapatan Bersih</span>
                    <h3 class="fw-bold text-dark my-2" style="font-size: 1.6rem; letter-spacing: -0.02em;">{{ format_currency($netCurrentMonth, $currencySymbol) }}</h3>
                    <div class="d-flex align-items-center" style="font-size: 0.8rem;">
                        <span class="{{ $netDiff['dir'] === 'up' ? 'text-success' : 'text-danger' }} fw-bold me-1">
                            <i class="fas {{ $netDiff['dir'] === 'up' ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }} me-1"></i>{{ $netDiff['formatted'] }}
                        </span>
                        <span class="text-secondary">dari bulan lalu</span>
                    </div>
                </div>
                <div class="rounded-4 d-flex align-items-center justify-content-center bg-primary-subtle text-primary" style="width: 52px; height: 52px; flex-shrink: 0; border-radius: 14px !important;">
                    <i class="fas fa-chart-simple fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Jumlah Transaksi -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white" style="transition: transform 0.2s ease, box-shadow 0.2s ease;">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-semibold" style="font-size: 0.82rem; text-transform: capitalize;">Total Transaksi</span>
                    <h3 class="fw-bold text-dark my-2" style="font-size: 1.6rem; letter-spacing: -0.02em;">{{ $transactionsCountCurrentMonth }}</h3>
                    <div class="d-flex align-items-center" style="font-size: 0.8rem;">
                        <span class="{{ $countDiff['dir'] === 'up' ? 'text-success' : 'text-danger' }} fw-bold me-1">
                            <i class="fas {{ $countDiff['dir'] === 'up' ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }} me-1"></i>{{ $countDiff['formatted'] }}
                        </span>
                        <span class="text-secondary">dari bulan lalu</span>
                    </div>
                </div>
                <div class="rounded-4 d-flex align-items-center justify-content-center bg-info-subtle text-info" style="width: 52px; height: 52px; flex-shrink: 0; border-radius: 14px !important;">
                    <i class="fas fa-receipt fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row g-4 mb-4">
    <!-- Bar Chart (6 Months comparison) -->
    <div class="col-lg-8">
        <div class="card glass-card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h6 class="fw-bold mb-0">Pemasukan vs Pengeluaran</h6>
                        <span class="text-secondary small">Perbandingan 6 Bulan Terakhir</span>
                    </div>
                </div>
                <div style="height: 320px;">
                    <canvas id="monthlyComparisonChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Doughnut Chart (Categories current month) -->
    <div class="col-lg-4">
        <div class="card glass-card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h6 class="fw-bold mb-0">Pengeluaran Per Kategori</h6>
                        <span class="text-secondary small">Bulan Ini</span>
                    </div>
                </div>
                <div class="d-flex justify-content-center align-items-center" style="height: 320px; position: relative;">
                    @if(count($categoryLabels) > 0)
                        <canvas id="categoryDonutChart"></canvas>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-pie-chart text-muted opacity-50 fs-1 mb-2"></i>
                            <p class="text-secondary small mb-0">Belum ada pengeluaran bulan ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Daily Expense Trend Section -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card glass-card border-0 shadow-sm">
            <div class="card-body p-4">
                <div>
                    <h6 class="fw-bold mb-0">Tren Pengeluaran Harian</h6>
                    <span class="text-secondary small">Lacak pengeluaran Anda dari tanggal 1 hingga akhir bulan ini</span>
                </div>
                <div class="mt-4" style="height: 250px;">
                    <canvas id="dailyTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Section: Transactions & Quick Actions -->
<div class="row g-4 mb-4">
    <!-- Recent Transactions -->
    <div class="col-lg-8">
        <div class="card glass-card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold mb-0">Transaksi Terbaru</h6>
                        <span class="text-secondary small">5 transaksi terakhir yang Anda catat</span>
                    </div>
                    <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-light border rounded-pill px-3">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Transaksi</th>
                                <th>Kategori</th>
                                <th>Tanggal</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $tx)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white me-3" style="width: 38px; height: 38px; background-color: {{ $tx->category->color ?? '#6366f1' }};">
                                                <i class="fas {{ $tx->category->icon ?? 'fa-wallet' }}"></i>
                                            </div>
                                            <div>
                                                <strong class="text-primary d-block">{{ $tx->title }}</strong>
                                                <span class="text-secondary small d-none d-sm-inline">{{ Str::limit($tx->description, 35) ?: 'Tidak ada deskripsi' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill small">
                                            {{ $tx->category->name ?? 'Tanpa Kategori' }}
                                        </span>
                                    </td>
                                    <td><span class="text-secondary small">{{ \Carbon\Carbon::parse($tx->date)->translatedFormat('d M Y') }}</span></td>
                                    <td class="text-end">
                                        <strong class="{{ $tx->type === 'income' ? 'text-success' : 'text-danger' }}">
                                            {{ $tx->type === 'income' ? '+' : '-' }} {{ format_currency($tx->amount, $currencySymbol) }}
                                        </strong>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <div class="text-secondary small">
                                            <i class="fas fa-receipt opacity-50 fs-2 mb-2"></i>
                                            <p class="mb-0">Belum ada transaksi terdaftar.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Tools & Actions -->
    <div class="col-lg-4">
        <div class="card glass-card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Tautan Pintas</h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary border text-start p-3 rounded-4 hover-shadow transition">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary-subtle text-primary rounded-circle p-2 me-3"><i class="fas fa-file-invoice-dollar fs-5"></i></div>
                            <div>
                                <strong class="d-block small text-primary">Laporan Keuangan</strong>
                                <span class="text-secondary small" style="font-size: 0.75rem;">Ekspor PDF/Excel & Lacak Analitik</span>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary border text-start p-3 rounded-4 hover-shadow transition">
                        <div class="d-flex align-items-center">
                            <div class="bg-danger-subtle text-danger rounded-circle p-2 me-3"><i class="fas fa-calendar-day fs-5"></i></div>
                            <div>
                                <strong class="d-block small text-primary">Tagihan Rutin</strong>
                                <span class="text-secondary small" style="font-size: 0.75rem;">Pantau pengeluaran wajib bulanan Anda</span>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('saving-targets.index') }}" class="btn btn-outline-secondary border text-start p-3 rounded-4 hover-shadow transition">
                        <div class="d-flex align-items-center">
                            <div class="bg-success-subtle text-success rounded-circle p-2 me-3"><i class="fas fa-piggy-bank fs-5"></i></div>
                            <div>
                                <strong class="d-block small text-primary">Target Tabungan</strong>
                                <span class="text-secondary small" style="font-size: 0.75rem;">Rencanakan tabungan impian Anda</span>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary border text-start p-3 rounded-4 hover-shadow transition">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning-subtle text-warning-emphasis rounded-circle p-2 me-3"><i class="fas fa-sliders fs-5"></i></div>
                            <div>
                                <strong class="d-block small text-primary">Konfigurasi & Backup</strong>
                                <span class="text-secondary small" style="font-size: 0.75rem;">Backup database & atur preferensi</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Load ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Colors from CSS Variables
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const textSecColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';

    // 1. Monthly Comparison Chart (Bar Chart)
    const ctxMonthly = document.getElementById('monthlyComparisonChart').getContext('2d');
    new Chart(ctxMonthly, {
        type: 'bar',
        data: {
            labels: @json($chartMonths),
            datasets: [
                {
                    label: 'Pemasukan',
                    data: @json($incomeDataset),
                    backgroundColor: 'rgba(16, 185, 129, 0.85)',
                    borderColor: '#10b981',
                    borderWidth: 1,
                    borderRadius: 6
                },
                {
                    label: 'Pengeluaran',
                    data: @json($expenseDataset),
                    backgroundColor: 'rgba(239, 68, 68, 0.85)',
                    borderColor: '#ef4444',
                    borderWidth: 1,
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { color: textSecColor, font: { family: 'Outfit', weight: '500' } }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: textSecColor, font: { family: 'Outfit' } }
                },
                y: {
                    grid: { color: gridColor },
                    ticks: { color: textSecColor, font: { family: 'Outfit' } }
                }
            }
        }
    });

    // 2. Category Doughnut Chart
    @if(count($categoryLabels) > 0)
    const ctxCategory = document.getElementById('categoryDonutChart').getContext('2d');
    new Chart(ctxCategory, {
        type: 'doughnut',
        data: {
            labels: @json($categoryLabels),
            datasets: [{
                data: @json($categoryTotals),
                backgroundColor: @json($categoryColors),
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: textSecColor, font: { family: 'Outfit' }, boxWidth: 12 }
                }
            },
            cutout: '65%'
        }
    });
    @endif

    // 3. Daily Expense Trend Chart (Line Chart)
    const ctxDaily = document.getElementById('dailyTrendChart').getContext('2d');
    new Chart(ctxDaily, {
        type: 'line',
        data: {
            labels: @json($dailyLabels),
            datasets: [{
                label: 'Pengeluaran Harian',
                data: @json($dailyExpenses),
                fill: true,
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                borderColor: '#6366f1',
                borderWidth: 2,
                tension: 0.3,
                pointBackgroundColor: '#6366f1',
                pointRadius: 3,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: textSecColor, font: { family: 'Outfit', size: 10 } }
                },
                y: {
                    grid: { color: gridColor },
                    ticks: { color: textSecColor, font: { family: 'Outfit' } }
                }
            }
        }
    });
});
</script>
@endpush

