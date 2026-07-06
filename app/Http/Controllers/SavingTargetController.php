<?php

namespace App\Http\Controllers;

use App\Http\Requests\SavingTargetRequest;
use App\Models\SavingTarget;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SavingTargetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $savingTargets = Auth::user()->savingTargets()
            ->orderBy('status', 'asc')
            ->orderBy('deadline', 'asc')
            ->get();

        return view('saving-targets.index', compact('savingTargets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SavingTargetRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        // Auto complete if current is equal or greater than target
        if ($data['current_amount'] >= $data['target_amount']) {
            $data['status'] = 'completed';
        }

        Auth::user()->savingTargets()->create($data);

        return redirect()->route('saving-targets.index')
            ->with('success', 'Target keuangan baru berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SavingTargetRequest $request, SavingTarget $savingTarget): RedirectResponse
    {
        if ($savingTarget->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validated();

        // Auto complete if current is equal or greater than target
        if ($data['current_amount'] >= $data['target_amount']) {
            $data['status'] = 'completed';
        }

        $savingTarget->update($data);

        return redirect()->route('saving-targets.index')
            ->with('success', 'Target keuangan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SavingTarget $savingTarget): RedirectResponse
    {
        if ($savingTarget->user_id !== Auth::id()) {
            abort(403);
        }

        $savingTarget->delete();

        return redirect()->route('saving-targets.index')
            ->with('success', 'Target keuangan berhasil dihapus.');
    }
}
