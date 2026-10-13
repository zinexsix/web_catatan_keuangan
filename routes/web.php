<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SavingTargetController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// Redirect welcome page to login if guest, else dashboard
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Google Authentication Routes (Public/Guest)
Route::middleware('guest')->group(function () {
    Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');
});

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CRUD Categories
    Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);

    // CRUD Transactions
    Route::resource('transactions', TransactionController::class)->except(['show']);
    Route::get('/receipts/{filename}', [TransactionController::class, 'receipt'])
        ->where('filename', '[A-Za-z0-9._-]+')
        ->name('receipts.show');

    // CRUD Saving Targets
    Route::resource('saving-targets', SavingTargetController::class)->except(['create', 'show', 'edit']);

    // CRUD Budgets
    Route::resource('budgets', BudgetController::class)->except(['create', 'show', 'edit']);

    // CRUD Bills
    Route::resource('bills', BillController::class)->except(['create', 'show', 'edit']);
    Route::post('/bills/{bill}/pay', [BillController::class, 'markPaid'])->name('bills.pay');
    Route::post('/bills/{bill}/reset', [BillController::class, 'resetBill'])->name('bills.reset');

    // Reports & Exports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
    Route::get('/reports/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
    Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');

    // App Preferences & DB Backup/Restore
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/theme', [SettingController::class, 'toggleTheme'])->name('settings.theme');
    Route::get('/settings/backup', [SettingController::class, 'backup'])->name('settings.backup');
    Route::post('/settings/restore', [SettingController::class, 'restore'])->name('settings.restore');

    // In-App Notifications
    Route::get('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
});

require __DIR__.'/auth.php';
