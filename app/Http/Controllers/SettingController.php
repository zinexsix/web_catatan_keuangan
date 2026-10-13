<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SettingController extends Controller
{
    /**
     * Display general settings page.
     */
    public function index(): View
    {
        $setting = Auth::user()->getOrCreateSetting();

        return view('settings.index', compact('setting'));
    }

    /**
     * Update user general preferences.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'theme' => ['required', 'in:light,dark'],
            'language' => ['required', 'string', 'max:5'],
            'currency' => ['required', 'string', 'max:10'],
        ]);

        $setting = Auth::user()->getOrCreateSetting();
        $setting->update($request->only('theme', 'language', 'currency'));

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan preferensi berhasil disimpan.');
    }

    /**
     * Toggle dark/light theme asynchronously (called from JS).
     */
    public function toggleTheme(Request $request): JsonResponse
    {
        $request->validate([
            'theme' => ['required', 'in:light,dark']
        ]);

        $setting = Auth::user()->getOrCreateSetting();
        $setting->update(['theme' => $request->theme]);

        return response()->json(['status' => 'success']);
    }

    /**
     * Tables included in a backup, mapped to the columns exported per row.
     * Only rows owned by the current user are ever read or written.
     */
    protected const BACKUP_TABLES = [
        'categories' => ['id', 'name', 'type', 'color', 'icon', 'created_at', 'updated_at'],
        'transactions' => ['category_id', 'date', 'type', 'amount', 'title', 'description', 'payment_method', 'receipt_image', 'created_at', 'updated_at'],
        'budgets' => ['category_id', 'month', 'year', 'amount', 'created_at', 'updated_at'],
        'saving_targets' => ['name', 'target_amount', 'current_amount', 'deadline', 'status', 'created_at', 'updated_at'],
        'bills' => ['name', 'amount', 'due_day', 'recurrence', 'status', 'category', 'icon', 'created_at', 'updated_at'],
        'notifications' => ['title', 'message', 'type', 'is_read', 'data', 'created_at', 'updated_at'],
        'settings' => ['theme', 'language', 'currency'],
    ];

    /**
     * Premium Feature: Backup (JSON export of the current user's data only)
     */
    public function backup(): StreamedResponse
    {
        $userId = Auth::id();
        $fileName = 'backup_keuangan_' . now()->format('Ymd_His') . '.json';

        $data = [];
        foreach (self::BACKUP_TABLES as $table => $columns) {
            $data[$table] = DB::table($table)
                ->where('user_id', $userId)
                ->get($columns)
                ->map(fn ($row) => (array) $row)
                ->all();
        }

        $payload = [
            'app' => 'catatan-keuangan',
            'version' => 1,
            'exported_at' => now()->toIso8601String(),
            'data' => $data,
        ];

        return response()->streamDownload(function () use ($payload) {
            echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $fileName, ['Content-Type' => 'application/json']);
    }

    /**
     * Premium Feature: Restore (replaces the current user's data from a JSON backup)
     */
    public function restore(Request $request): RedirectResponse
    {
        $request->validate([
            'backup_file' => ['required', 'file', 'max:10240'],
        ], [
            'backup_file.required' => 'File backup wajib diunggah.',
            'backup_file.max' => 'Ukuran file backup maksimal 10MB.',
        ]);

        $payload = json_decode(file_get_contents($request->file('backup_file')->getRealPath()), true);

        if (! is_array($payload) || ($payload['app'] ?? null) !== 'catatan-keuangan' || ! is_array($payload['data'] ?? null)) {
            return redirect()->route('settings.index')
                ->with('error', 'Format file salah. Wajib mengunggah file backup .json dari aplikasi ini.');
        }

        $userId = Auth::id();
        $data = $payload['data'];
        $rows = fn (string $table) => array_filter((array) ($data[$table] ?? []), 'is_array');

        // Receipt files currently owned by this user; restored rows may only reference these.
        $ownedReceipts = DB::table('transactions')->where('user_id', $userId)
            ->pluck('receipt_image')
            ->map(fn ($value) => Transaction::receiptPath($value))
            ->filter()
            ->all();
        $keptReceipts = [];

        try {
            DB::transaction(function () use ($userId, $rows, $ownedReceipts, &$keptReceipts) {
                foreach (['transactions', 'budgets', 'categories', 'saving_targets', 'bills', 'notifications'] as $table) {
                    DB::table($table)->where('user_id', $userId)->delete();
                }

                $categoryMap = [];
                foreach ($rows('categories') as $row) {
                    $values = Arr::only($row, array_diff(self::BACKUP_TABLES['categories'], ['id']));
                    $categoryMap[$row['id'] ?? null] = DB::table('categories')->insertGetId($values + ['user_id' => $userId]);
                }

                foreach ($rows('transactions') as $row) {
                    $categoryId = $categoryMap[$row['category_id'] ?? null] ?? null;
                    if (! $categoryId) {
                        continue;
                    }

                    $values = Arr::only($row, self::BACKUP_TABLES['transactions']);
                    $receipt = Transaction::receiptPath($values['receipt_image'] ?? null);
                    $values['receipt_image'] = in_array($receipt, $ownedReceipts, true) ? $receipt : null;
                    if ($values['receipt_image']) {
                        $keptReceipts[] = $values['receipt_image'];
                    }

                    DB::table('transactions')->insert(['category_id' => $categoryId, 'user_id' => $userId] + $values);
                }

                foreach ($rows('budgets') as $row) {
                    $values = Arr::only($row, self::BACKUP_TABLES['budgets']);
                    if (! empty($values['category_id'])) {
                        if (! isset($categoryMap[$values['category_id']])) {
                            continue;
                        }
                        $values['category_id'] = $categoryMap[$values['category_id']];
                    }

                    DB::table('budgets')->insert($values + ['user_id' => $userId]);
                }

                foreach (['saving_targets', 'bills', 'notifications'] as $table) {
                    foreach ($rows($table) as $row) {
                        DB::table($table)->insert(Arr::only($row, self::BACKUP_TABLES[$table]) + ['user_id' => $userId]);
                    }
                }

                if ($setting = $rows('settings')[0] ?? null) {
                    Auth::user()->getOrCreateSetting()->update(Arr::only($setting, self::BACKUP_TABLES['settings']));
                }
            });
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('settings.index')
                ->with('error', 'Gagal me-restore data. Pastikan file backup valid dan tidak diubah.');
        }

        // Remove receipt files no longer referenced after the restore.
        foreach (array_diff($ownedReceipts, $keptReceipts) as $path) {
            Transaction::deleteReceipt($path);
        }

        return redirect()->route('settings.index')
            ->with('success', 'Data berhasil di-restore dari file cadangan.');
    }
}
