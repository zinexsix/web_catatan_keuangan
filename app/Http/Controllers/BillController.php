<?php

namespace App\Http\Controllers;

use App\Http\Requests\BillRequest;
use App\Models\Bill;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $user = Auth::user();
        $today = now()->day;

        // Auto-check for overdue bills
        $user->bills()
            ->where('status', 'pending')
            ->where('due_day', '<', $today)
            ->update(['status' => 'overdue']);

        $bills = $user->bills()
            ->orderBy('status', 'asc')
            ->orderBy('due_day', 'asc')
            ->get();

        return view('bills.index', compact('bills'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BillRequest $request): RedirectResponse
    {
        Auth::user()->bills()->create($request->validated());

        return redirect()->route('bills.index')
            ->with('success', 'Tagihan rutin baru berhasil didaftarkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BillRequest $request, Bill $bill): RedirectResponse
    {
        if ($bill->user_id !== Auth::id()) {
            abort(403);
        }

        $bill->update($request->validated());

        return redirect()->route('bills.index')
            ->with('success', 'Tagihan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bill $bill): RedirectResponse
    {
        if ($bill->user_id !== Auth::id()) {
            abort(403);
        }

        $bill->delete();

        return redirect()->route('bills.index')
            ->with('success', 'Tagihan berhasil dihapus.');
    }

    /**
     * Custom Action: Mark bill as Paid & automatically log the Transaction.
     */
    public function markPaid(Request $request, Bill $bill): RedirectResponse
    {
        if ($bill->user_id !== Auth::id()) {
            abort(403);
        }

        // 1. Get or create matching category for the transaction log
        $categoryName = $bill->category ?: 'Lainnya';
        $category = Auth::user()->categories()
            ->expense()
            ->where('name', 'like', "%{$categoryName}%")
            ->first();

        if (!$category) {
            // Find first expense category or fallback
            $category = Auth::user()->categories()->expense()->first() 
                ?: Auth::user()->categories()->create([
                    'name' => $categoryName,
                    'type' => 'expense',
                    'color' => '#6366f1',
                    'icon' => $bill->icon
                ]);
        }

        // 2. Log transaction
        Auth::user()->transactions()->create([
            'category_id' => $category->id,
            'date' => now()->toDateString(),
            'type' => 'expense',
            'amount' => $bill->amount,
            'title' => 'Bayar Tagihan: ' . $bill->name,
            'description' => 'Pembayaran tagihan rutin ' . $bill->name . ' periode ' . now()->translatedFormat('F Y'),
            'payment_method' => $request->input('payment_method', 'transfer'),
        ]);

        // 3. Mark bill status as paid
        $bill->update(['status' => 'paid']);

        return redirect()->route('bills.index')
            ->with('success', 'Tagihan ' . $bill->name . ' berhasil dibayar dan dicatat ke pengeluaran.');
    }

    /**
     * Custom Action: Reset bill back to Pending status for the next month period.
     */
    public function resetBill(Bill $bill): RedirectResponse
    {
        if ($bill->user_id !== Auth::id()) {
            abort(403);
        }

        $bill->update(['status' => 'pending']);

        return redirect()->route('bills.index')
            ->with('success', 'Tagihan ' . $bill->name . ' berhasil direset untuk periode berikutnya.');
    }
}
