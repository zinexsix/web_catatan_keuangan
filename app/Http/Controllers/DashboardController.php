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
        
        $lastMonthDate = Carbon::now()->subMonth();
        $lastMonth = $lastMonthDate->month;
        $lastMonthYear = $lastMonthDate->year;

        // ── Transactions Query ──
        $transactionsQuery = $user->transactions();
        
        // ── Summary Cards Data ──
        // Current Month Pemasukan & Pengeluaran
        $incomeCurrentMonth = (float) $user->transactions()
            ->income()
            ->inMonth($currentMonth, $currentYear)
            ->sum('amount');
            
        $expenseCurrentMonth = (float) $user->transactions()
            ->expense()
            ->inMonth($currentMonth, $currentYear)
            ->sum('amount');

        $netCurrentMonth = $incomeCurrentMonth - $expenseCurrentMonth;
        
        $transactionsCountCurrentMonth = $user->transactions()
            ->inMonth($currentMonth, $currentYear)
            ->count();

        // Last Month Pemasukan & Pengeluaran
        $incomeLastMonth = (float) $user->transactions()
            ->income()
            ->inMonth($lastMonth, $lastMonthYear)
            ->sum('amount');
            
        $expenseLastMonth = (float) $user->transactions()
            ->expense()
            ->inMonth($lastMonth, $lastMonthYear)
            ->sum('amount');

        $netLastMonth = $incomeLastMonth - $expenseLastMonth;
        
        $transactionsCountLastMonth = $user->transactions()
            ->inMonth($lastMonth, $lastMonthYear)
            ->count();

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
            $date = Carbon::now()->subMonths($i);
            $chartMonths[] = $date->translatedFormat('F Y');
            
            $incomeDataset[] = (float) $user->transactions()
                ->income()
                ->inMonth($date->month, $date->year)
                ->sum('amount');
                
            $expenseDataset[] = (float) $user->transactions()
                ->expense()
                ->inMonth($date->month, $date->year)
                ->sum('amount');
        }

        // ── Chart 2: Pengeluaran per Kategori (Current Month) ──
        $categoriesData = $user->transactions()
            ->expense()
            ->inMonth($currentMonth, $currentYear)
            ->selectRaw('category_id, sum(amount) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get();

        $categoryLabels = [];
        $categoryTotals = [];
        $categoryColors = [];
        
        foreach ($categoriesData as $data) {
            if ($data->category) {
                $categoryLabels[] = $data->category->name;
                $categoryTotals[] = (float) $data->total;
                $categoryColors[] = $data->category->color;
            }
        }

        // ── Chart 3: Tren Pengeluaran Harian / Bulanan ──
        // (Let's make this Daily Expense trend for Current Month to make it premium and highly detailed)
        $daysInMonth = $now->daysInMonth;
        $dailyLabels = [];
        $dailyExpenses = [];
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dailyLabels[] = $day;
            $dailyExpenses[] = (float) $user->transactions()
                ->expense()
                ->whereDate('date', Carbon::create($currentYear, $currentMonth, $day))
                ->sum('amount');
        }

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
