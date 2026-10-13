<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the application dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;
        
        $lastMonthKey = Carbon::now()->startOfMonth()->subMonth()->format('Y-m');
        $currentMonthKey = $now->format('Y-m');

        // ── Load the last 6 months of transactions once and aggregate in PHP ──
        // (previously ~50 separate SUM/COUNT queries, which is slow against a remote database)
        $rows = $user->transactions()
            ->where('date', '>=', Carbon::now()->startOfMonth()->subMonths(5)->toDateString())
            ->where('date', '<', Carbon::now()->startOfMonth()->addMonth()->toDateString())
            ->toBase()
            ->get(['date', 'type', 'amount', 'category_id']);

        $monthly = [];   // 'Y-m' => ['income' => x, 'expense' => y, 'count' => n]
        $daily = [];     // day of current month => expense
        $byCategory = []; // category_id => expense in current month

        foreach ($rows as $row) {
            $date = substr((string) $row->date, 0, 10);
            $monthKey = substr($date, 0, 7);
            $amount = (float) $row->amount;

            $monthly[$monthKey] ??= ['income' => 0.0, 'expense' => 0.0, 'count' => 0];
            $monthly[$monthKey][$row->type] += $amount;
            $monthly[$monthKey]['count']++;

            if ($monthKey === $currentMonthKey && $row->type === 'expense') {
                $day = (int) substr($date, 8, 2);
                $daily[$day] = ($daily[$day] ?? 0) + $amount;
                $byCategory[$row->category_id] = ($byCategory[$row->category_id] ?? 0) + $amount;
            }
        }

        $month = fn (string $key, string $field) => $monthly[$key][$field] ?? 0;

        // ── Summary Cards Data ──
        $incomeCurrentMonth = (float) $month($currentMonthKey, 'income');
        $expenseCurrentMonth = (float) $month($currentMonthKey, 'expense');
        $netCurrentMonth = $incomeCurrentMonth - $expenseCurrentMonth;
        $transactionsCountCurrentMonth = (int) $month($currentMonthKey, 'count');

        $incomeLastMonth = (float) $month($lastMonthKey, 'income');
        $expenseLastMonth = (float) $month($lastMonthKey, 'expense');
        $netLastMonth = $incomeLastMonth - $expenseLastMonth;
        $transactionsCountLastMonth = (int) $month($lastMonthKey, 'count');

        // ── Percentage Difference Calculations ──
        $incomeDiff = $this->calculatePercentageDifference($incomeLastMonth, $incomeCurrentMonth);
        $expenseDiff = $this->calculatePercentageDifference($expenseLastMonth, $expenseCurrentMonth);
        $netDiff = $this->calculatePercentageDifference($netLastMonth, $netCurrentMonth);
        $countDiff = $this->calculatePercentageDifference($transactionsCountLastMonth, $transactionsCountCurrentMonth);

        // ── Budget Limit Checks ──
        // Get overall budget for current month if exists
        $overallBudget = Budget::where('user_id', $user->id)
            ->whereNull('category_id')
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->first();

        $budgetStatus = [
            'has_budget' => !is_null($overallBudget),
            'limit' => $overallBudget ? (float) $overallBudget->amount : 0,
            'spent' => $expenseCurrentMonth,
            'is_exceeded' => $overallBudget && ($expenseCurrentMonth > $overallBudget->amount),
            'is_warning' => $overallBudget && ($expenseCurrentMonth >= $overallBudget->amount * 0.8) && ($expenseCurrentMonth <= $overallBudget->amount),
            'percentage' => $overallBudget && $overallBudget->amount > 0 ? round(($expenseCurrentMonth / $overallBudget->amount) * 100, 1) : 0
        ];

        // ── Chart 1: Pemasukan vs Pengeluaran Bulanan (6 Months) ──
        $chartMonths = [];
        $incomeDataset = [];
        $expenseDataset = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->startOfMonth()->subMonths($i);
            $chartMonths[] = $date->translatedFormat('F Y');
            $incomeDataset[] = (float) $month($date->format('Y-m'), 'income');
            $expenseDataset[] = (float) $month($date->format('Y-m'), 'expense');
        }

        // ── Chart 2: Pengeluaran per Kategori (Current Month) ──
        $categories = Category::whereIn('id', array_keys($byCategory))->get()->keyBy('id');

        $categoryLabels = [];
        $categoryTotals = [];
        $categoryColors = [];

        foreach ($byCategory as $categoryId => $total) {
            if ($category = $categories->get($categoryId)) {
                $categoryLabels[] = $category->name;
                $categoryTotals[] = (float) $total;
                $categoryColors[] = $category->color;
            }
        }

        // ── Chart 3: Tren Pengeluaran Harian (Current Month) ──
        $dailyLabels = range(1, $now->daysInMonth);
        $dailyExpenses = array_map(fn ($day) => (float) ($daily[$day] ?? 0), $dailyLabels);

        // Recent Transactions list
        $recentTransactions = $user->transactions()
            ->with('category')
            ->latest('date')
            ->latest('id')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'incomeCurrentMonth', 'expenseCurrentMonth', 'netCurrentMonth', 'transactionsCountCurrentMonth',
            'incomeDiff', 'expenseDiff', 'netDiff', 'countDiff',
            'budgetStatus', 'recentTransactions',
            'chartMonths', 'incomeDataset', 'expenseDataset',
            'categoryLabels', 'categoryTotals', 'categoryColors',
            'dailyLabels', 'dailyExpenses'
        ));
    }

    /**
     * Helper to calculate percentage differences.
     */
    private function calculatePercentageDifference(float $old, float $new): array
    {
        if ($old == 0) {
            return [
                'val' => $new > 0 ? 100 : 0,
                'dir' => 'up',
                'formatted' => $new > 0 ? '+100%' : '0%'
            ];
        }

        $diff = (($new - $old) / $old) * 100;
        
        return [
            'val' => abs(round($diff, 1)),
            'dir' => $diff >= 0 ? 'up' : 'down',
            'formatted' => ($diff >= 0 ? '+' : '-') . abs(round($diff, 1)) . '%'
        ];
    }
}
