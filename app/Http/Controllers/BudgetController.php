<?php

namespace App\Http\Controllers;

use App\Http\Requests\BudgetRequest;
use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        // Filter by month/year, default is current month/year
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $budgets = $user->budgets()
            ->where('month', $month)
            ->where('year', $year)
            ->with('category')
            ->get();

        // Get expense categories for modal dropdown
        $categories = $user->categories()->expense()->get();

        return view('budgets.index', compact('budgets', 'categories', 'month', 'year'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BudgetRequest $request): RedirectResponse
    {
        Auth::user()->budgets()->create($request->validated());

        return redirect()->route('budgets.index', [
            'month' => $request->month,
            'year' => $request->year
        ])->with('success', 'Anggaran bulanan berhasil dibuat.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BudgetRequest $request, Budget $budget): RedirectResponse
    {
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        $budget->update($request->validated());

        return redirect()->route('budgets.index', [
            'month' => $budget->month,
            'year' => $budget->year
        ])->with('success', 'Anggaran bulanan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Budget $budget): RedirectResponse
    {
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        $month = $budget->month;
        $year = $budget->year;
        
        $budget->delete();

        return redirect()->route('budgets.index', [
            'month' => $month,
            'year' => $year
        ])->with('success', 'Anggaran bulanan berhasil dihapus.');
    }
}
