<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cetak Laporan Keuangan</title>
    <!-- Bootstrap 5 CSS for base classes -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #fff;
            color: #333;
            font-size: 14px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4 no-print border-bottom pb-3">
            <span class="text-muted">Layar cetak laporan. Tekan Ctrl+P jika dialog print tidak muncul otomatis.</span>
            <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print me-1"></i> Print Sekarang</button>
        </div>

        <div class="text-center mb-5">
            <h2 class="fw-bold mb-1">Laporan Catatan Keuangan</h2>
            <p class="text-secondary mb-0">Finance Manager Application</p>
            <h5 class="fw-bold text-primary mt-3">Periode: {{ $titlePeriod }}</h5>
        </div>

        <!-- Summary row -->
        <div class="row g-4 mb-5">
            <div class="col-4">
                <div class="border p-3 rounded text-center">
                    <span class="text-uppercase text-secondary small d-block mb-1">Total Pemasukan</span>
                    <h4 class="fw-bold text-success mb-0">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h4>
                </div>
            </div>
            <div class="col-4">
                <div class="border p-3 rounded text-center">
                    <span class="text-uppercase text-secondary small d-block mb-1">Total Pengeluaran</span>
                    <h4 class="fw-bold text-danger mb-0">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h4>
                </div>
            </div>
            <div class="col-4">
                <div class="border p-3 rounded text-center">
                    <span class="text-uppercase text-secondary small d-block mb-1">Pendapatan Bersih</span>
                    <h4 class="fw-bold text-primary mb-0">Rp {{ number_format($netIncome, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>

        <!-- Details table -->
        <h4 class="fw-bold mb-3">Detail Riwayat Transaksi</h4>
        <table class="table table-bordered align-middle">
            <thead>
                <tr class="table-light">
                    <th>Tanggal</th>
                    <th>Judul Transaksi</th>
                    <th>Kategori</th>
                    <th>Metode Pembayaran</th>
                    <th class="text-end">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                    <tr>
                        <td>{{ $tx->date->format('d-m-Y') }}</td>
                        <td>{{ $tx->title }}</td>
                        <td>{{ $tx->category->name ?? 'Lainnya' }}</td>
                        <td>{{ $tx->payment_method_label }}</td>
                        <td class="text-end fw-bold text-{{ $tx->type === 'income' ? 'success' : 'danger' }}">
                            {{ $tx->type === 'income' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Tidak ada data transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
