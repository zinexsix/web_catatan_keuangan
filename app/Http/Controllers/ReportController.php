<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Display report index with filters and statistics.
     */
    public function index(Request $request): View
    {
        $data = $this->getReportData($request);

        return view('reports.index', $data);
    }

    /**
     * Export report data as PDF.
     */
    public function exportPdf(Request $request)
    {
        $data = $this->getReportData($request);
        
        $pdf = Pdf::loadView('reports.pdf', $data);
        
        return $pdf->download('Laporan_Keuangan_' . now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Export report data as Excel/CSV.
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $data = $this->getReportData($request);
        $transactions = $data['transactions'];

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Laporan_Keuangan_' . now()->format('Ymd_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel compatibility
            fputs($file, "\xEF\xBB\xBF");
            
            // CSV Headers
            fputcsv($file, ['No', 'Tanggal', 'Judul Transaksi', 'Jenis', 'Kategori', 'Nominal (Rp)', 'Metode Pembayaran', 'Deskripsi']);

            $no = 1;
            foreach ($transactions as $tx) {
                fputcsv($file, [
                    $no++,
                    $tx->date->format('d-m-Y'),
                    $tx->title,
                    $tx->type === 'income' ? 'Pemasukan' : 'Pengeluaran',
                    $tx->category->name ?? 'Lainnya',
                    $tx->amount,
                    $tx->payment_method_label,
                    $tx->description ?: '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Render minimal print layout.
     */
    public function print(Request $request): View
    {
        $data = $this->getReportData($request);

        return view('reports.print', $data);
    }

    /**
     * Helper: Fetch and aggregate report data based on parameters.
     */
    protected function getReportData(Request $request): array
    {
        $user = Auth::user();
        $query = $user->transactions()->with('category');

        $timeframe = $request->input('timeframe', 'month');
        $startDate = null;
        $endDate = null;
        $titlePeriod = '';

        $now = Carbon::now();

        switch ($timeframe) {
            case 'today':
                $startDate = $now->copy()->startOfDay()->toDateString();
                $endDate = $now->copy()->endOfDay()->toDateString();
                $titlePeriod = $now->translatedFormat('d F Y');
                break;
            case 'week':
                $startDate = $now->copy()->startOfWeek()->toDateString();
                $endDate = $now->copy()->endOfWeek()->toDateString();
                $titlePeriod = $now->copy()->startOfWeek()->translatedFormat('d M') . ' - ' . $now->copy()->endOfWeek()->translatedFormat('d M Y');
                break;
            case 'year':
                $startDate = $now->copy()->startOfYear()->toDateString();
                $endDate = $now->copy()->endOfYear()->toDateString();
                $titlePeriod = 'Tahun ' . $now->year;
                break;
            case 'custom':
                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $startDate = $request->start_date;
                    $endDate = $request->end_date;
                    $titlePeriod = Carbon::parse($startDate)->translatedFormat('d M Y') . ' - ' . Carbon::parse($endDate)->translatedFormat('d M Y');
                } else {
                    // Fallback to month
                    $startDate = $now->copy()->startOfMonth()->toDateString();
                    $endDate = $now->copy()->endOfMonth()->toDateString();
                    $titlePeriod = $now->translatedFormat('F Y');
                }
                break;
            case 'month':
            default:
                $startDate = $now->copy()->startOfMonth()->toDateString();
                $endDate = $now->copy()->endOfMonth()->toDateString();
                $titlePeriod = $now->translatedFormat('F Y');
                break;
        }

        // Apply Date Range
        $query->betweenDates($startDate, $endDate);

        // Fetch all transactions in range
        $transactions = $query->orderBy('date', 'asc')->get();

        // Totals Calculations
        $totalIncome = (float) $transactions->where('type', 'income')->sum('amount');
        $totalExpense = (float) $transactions->where('type', 'expense')->sum('amount');
        $netIncome = $totalIncome - $totalExpense;

        // Group expense totals by category for chart
        $categoriesData = $transactions->where('type', 'expense')
            ->groupBy('category_id');

        $chartLabels = [];
        $chartTotals = [];
        $chartColors = [];

        foreach ($categoriesData as $catId => $items) {
            $category = $items->first()->category;
            if ($category) {
                $chartLabels[] = $category->name;
                $chartTotals[] = (float) $items->sum('amount');
                $chartColors[] = $category->color;
            }
        }

        return [
            'transactions' => $transactions,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netIncome' => $netIncome,
            'timeframe' => $timeframe,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'titlePeriod' => $titlePeriod,
            'chartLabels' => $chartLabels,
            'chartTotals' => $chartTotals,
            'chartColors' => $chartColors
        ];
    }
}
