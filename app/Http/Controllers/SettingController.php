<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
    public function toggleTheme(Request $request): RedirectResponse
    {
        $request->validate([
            'theme' => ['required', 'in:light,dark']
        ]);

        $setting = Auth::user()->getOrCreateSetting();
        $setting->update(['theme' => $request->theme]);

        return response()->json(['status' => 'success']);
    }

    /**
     * Premium Feature: Database Backup (SQL Export)
     */
    public function backup(): StreamedResponse
    {
        $fileName = 'backup_keuangan_' . now()->format('Ymd_His') . '.sql';
        
        $headers = [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function() {
            $tables = ['users', 'categories', 'transactions', 'budgets', 'saving_targets', 'bills', 'notifications', 'settings'];
            $out = fopen('php://output', 'w');

            fwrite($out, "-- Finance Manager Database Backup\n");
            fwrite($out, "-- Generated: " . now()->toDateTimeString() . "\n\n");
            fwrite($out, "SET FOREIGN_KEY_CHECKS=0;\n\n");

            foreach ($tables as $table) {
                if (!Schema::hasTable($table)) continue;

                // Drop table query
                fwrite($out, "DROP TABLE IF EXISTS `{$table}`;\n");
                
                // Create table schema
                $createTable = DB::select("SHOW CREATE TABLE `{$table}`");
                if (count($createTable) > 0) {
                    $createTableProp = 'Create Table';
                    $sql = $createTable[0]->$createTableProp ?? $createTable[0]->{'Create Table'};
                    fwrite($out, $sql . ";\n\n");
                }

                // Insert rows query
                $rows = DB::table($table)->get();
                foreach ($rows as $row) {
                    $rowArr = (array) $row;
                    $cols = implode('`, `', array_keys($rowArr));
                    
                    $vals = array_map(function($val) {
                        if (is_null($val)) return 'NULL';
                        return "'" . addslashes($val) . "'";
                    }, array_values($rowArr));
                    
                    $valsStr = implode(', ', $vals);
                    fwrite($out, "INSERT INTO `{$table}` (`{$cols}`) VALUES ({$valsStr});\n");
                }
                fwrite($out, "\n");
            }

            fwrite($out, "SET FOREIGN_KEY_CHECKS=1;\n");
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Premium Feature: Database Restore (SQL Import)
     */
    public function restore(Request $request): RedirectResponse
    {
        $request->validate([
            'backup_file' => ['required', 'file']
        ], [
            'backup_file.required' => 'File backup wajib diunggah.'
        ]);

        $file = $request->file('backup_file');
        
        if ($file->getClientOriginalExtension() !== 'sql') {
            return redirect()->route('settings.index')
                ->with('error', 'Format file salah. Wajib mengunggah file berekstensi .sql');
        }

        try {
            $sql = file_get_contents($file->getRealPath());

            // Disable foreign checks and run sql statements
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Split queries by semicolon to execute one-by-one, keeping multi-line safety
            // (Standard regex parsing queries cleanly)
            $queries = array_filter(array_map('trim', explode(";\n", $sql)));
            foreach ($queries as $query) {
                if (!empty($query)) {
                    DB::unprepared($query);
                }
            }
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return redirect()->route('settings.index')
                ->with('success', 'Database berhasil di-restore dari file cadangan.');
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return redirect()->route('settings.index')
                ->with('error', 'Gagal me-restore database: ' . $e->getMessage());
        }
    }
}
