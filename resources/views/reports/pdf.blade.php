<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan - Finance Manager</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0 0 5px 0;
            color: #0f172a;
        }
        .header p {
            margin: 0;
            color: #64748b;
        }
        .summary-box {
            width: 100%;
            margin-bottom: 35px;
        }
        .summary-box td {
            padding: 15px;
            border-radius: 8px;
            color: #fff;
            text-align: center;
        }
        .bg-income { background-color: #10b981; }
        .bg-expense { background-color: #ef4444; }
        .bg-net { background-color: #6366f1; }
        .summary-box td span {
            display: block;
            font-size: 10px;
            text-transform: uppercase;
            margin-bottom: 5px;
            opacity: 0.8;
        }
        .summary-box td strong {
            font-size: 16px;
        }
        table.details {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table.details th, table.details td {
            border-bottom: 1px solid #dee2e6;
            padding: 10px 8px;
            text-align: left;
        }
        table.details th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: bold;
        }
        .text-right { text-align: right; }
        .text-success { color: #10b981; }
        .text-danger { color: #ef4444; }
        .bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Catatan Keuangan</h2>
        <p>Aplikasi Finance Manager</p>
        <p style="margin-top: 5px; font-weight: bold;">Periode: {{ $titlePeriod }}</p>
    </div>

    <!-- Summary Box -->
    <table class="summary-box" cellspacing="10">
        <tr>
            <td class="bg-income" width="33%">
                <span>Total Pemasukan</span>
                <strong>Rp {{ number_format($totalIncome, 0, ',', '.') }}</strong>
            </td>
            <td class="bg-expense" width="33%">
                <span>Total Pengeluaran</span>
                <strong>Rp {{ number_format($totalExpense, 0, ',', '.') }}</strong>
            </td>
            <td class="bg-net" width="33%">
                <span>Pendapatan Bersih</span>
                <strong>Rp {{ number_format($netIncome, 0, ',', '.') }}</strong>
            </td>
        </tr>
    </table>

    <!-- Table Details -->
    <h3 style="color: #0f172a; margin-bottom: 5px;">Rincian Transaksi</h3>
    <table class="details">
        <thead>
            <tr>
                <th width="10%">Tanggal</th>
                <th width="30%">Judul Transaksi</th>
                <th width="20%">Kategori</th>
                <th width="15%">Metode</th>
                <th width="25%" class="text-right">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $tx)
                <tr>
                    <td>{{ $tx->date->format('d-m-Y') }}</td>
                    <td>{{ $tx->title }}</td>
                    <td>{{ $tx->category->name ?? 'Lainnya' }}</td>
                    <td>{{ $tx->payment_method_label }}</td>
                    <td class="text-right bold text-{{ $tx->type === 'income' ? 'success' : 'danger' }}">
                        {{ $tx->type === 'income' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">Tidak ada data transaksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
