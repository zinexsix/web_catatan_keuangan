<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource with filters, search, sorting.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        $query = $user->transactions()->with('category');

        // ── 1. Search Filter ──
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // ── 2. Type Filter ──
        if ($request->filled('type') && in_array($request->type, ['income', 'expense'])) {
            $query->ofType($request->type);
        }

        // ── 3. Category Filter ──
        if ($request->filled('category_id')) {
            $query->inCategory((int) $request->category_id);
        }

        // ── 4. Date Filters ──
        if ($request->filled('timeframe')) {
            $now = Carbon::now();
            switch ($request->timeframe) {
                case 'today':
                    $query->onDate($now->toDateString());
                    break;
                case 'week':
                    $query->betweenDates($now->startOfWeek()->toDateString(), $now->endOfWeek()->toDateString());
                    break;
                case 'month':
                    $query->inMonth($now->month, $now->year);
                    break;
                case 'year':
                    $query->inYear($now->year);
                    break;
                case 'custom':
                    if ($request->filled('start_date') && $request->filled('end_date')) {
                        $query->betweenDates($request->start_date, $request->end_date);
                    }
                    break;
            }
        }

        // ── 5. Sorting ──
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('date', 'asc')->orderBy('id', 'asc');
                break;
            case 'highest_amount':
                $query->orderBy('amount', 'desc');
                break;
            case 'lowest_amount':
                $query->orderBy('amount', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('date', 'desc')->orderBy('id', 'desc');
                break;
        }

        $transactions = $query->paginate(10)->withQueryString();
        
        $categories = $user->categories()->get();

        return view('transactions.index', compact('transactions', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $user = Auth::user();
        $categories = $user->categories()->get();
        $defaultType = $request->input('type', 'expense');

        return view('transactions.create', compact('categories', 'defaultType'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransactionRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        // Handle File Upload
        if ($request->hasFile('receipt_image')) {
            $path = $request->file('receipt_image')->store('receipts', 'public');
            $data['receipt_image'] = Storage::url($path);
        }

        $transaction = $user->transactions()->create($data);

        // Check if transaction is expense and budget limit check is needed
        if ($transaction->type === 'expense') {
            $this->checkBudgetNotification($transaction);
        }

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dicatat.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction): View
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $categories = Auth::user()->categories()->get();

        return view('transactions.edit', compact('transaction', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validated();

        // Handle File Upload
        if ($request->hasFile('receipt_image')) {
            // Delete old file if exists
            if ($transaction->receipt_image) {
                $oldPath = str_replace('/storage/', '', $transaction->receipt_image);
                Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('receipt_image')->store('receipts', 'public');
            $data['receipt_image'] = Storage::url($path);
        }

        $transaction->update($data);

        if ($transaction->type === 'expense') {
            $this->checkBudgetNotification($transaction);
        }

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction): RedirectResponse
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        // Delete file if exists
        if ($transaction->receipt_image) {
            $oldPath = str_replace('/storage/', '', $transaction->receipt_image);
            Storage::disk('public')->delete($oldPath);
        }

        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }

    /**
     * Verify if monthly budgets are crossed and fire in-app alerts/notifications.
     */
    protected function checkBudgetNotification(Transaction $transaction): void
    {
        $user = Auth::user();
        $date = Carbon::parse($transaction->date);
        $month = $date->month;
        $year = $date->year;

        // Check if there is an overall budget for this month
        $overallBudget = Budget::where('user_id', $user->id)
            ->whereNull('category_id')
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if ($overallBudget) {
            $spent = $overallBudget->spent_amount;
            $limit = (float) $overallBudget->amount;

            if ($spent > $limit) {
                // Create Exceeded Notification
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Anggaran Bulanan Terlewati!',
                    'message' => 'Pengeluaran bulanan Anda sebesar Rp ' . number_format($spent, 0, ',', '.') . ' telah melebihi batas anggaran Rp ' . number_format($limit, 0, ',', '.'),
                    'type' => 'danger',
                    'data' => ['url' => route('budgets.index')]
                ]);
            } elseif ($spent >= $limit * 0.8) {
                // Create Warning Notification
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Anggaran Hampir Habis!',
                    'message' => 'Pengeluaran bulanan Anda sebesar Rp ' . number_format($spent, 0, ',', '.') . ' telah mencapai ' . round(($spent/$limit)*100, 1) . '% dari anggaran bulanan.',
                    'type' => 'warning',
                    'data' => ['url' => route('budgets.index')]
                ]);
            }
        }
    }
}
