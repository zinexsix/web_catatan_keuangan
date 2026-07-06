<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\Budget;
use App\Models\Category;
use App\Models\SavingTarget;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ── Create Demo User ────────────────────────────
        $user = User::create([
            'name' => 'Demo User',
            'username' => 'demo',
            'email' => 'demo@finance.com',
            'phone' => '081234567890',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // Create user settings
        Setting::create([
            'user_id' => $user->id,
            'theme' => 'light',
            'language' => 'id',
            'currency' => 'IDR',
        ]);

        // ── Create Categories ───────────────────────────
        $incomeCategories = [
            ['name' => 'Gaji', 'icon' => 'fa-money-bill-wave', 'color' => '#10b981'],
            ['name' => 'Bonus', 'icon' => 'fa-gift', 'color' => '#06b6d4'],
            ['name' => 'Investasi', 'icon' => 'fa-chart-line', 'color' => '#8b5cf6'],
            ['name' => 'Freelance', 'icon' => 'fa-laptop-code', 'color' => '#f59e0b'],
            ['name' => 'Pendapatan Lainnya', 'icon' => 'fa-plus-circle', 'color' => '#64748b'],
        ];

        $expenseCategories = [
            ['name' => 'Makanan', 'icon' => 'fa-utensils', 'color' => '#ef4444'],
            ['name' => 'Transportasi', 'icon' => 'fa-car', 'color' => '#f97316'],
            ['name' => 'Belanja', 'icon' => 'fa-shopping-bag', 'color' => '#ec4899'],
            ['name' => 'Listrik', 'icon' => 'fa-bolt', 'color' => '#eab308'],
            ['name' => 'Internet', 'icon' => 'fa-wifi', 'color' => '#3b82f6'],
            ['name' => 'Air', 'icon' => 'fa-tint', 'color' => '#06b6d4'],
            ['name' => 'Pulsa', 'icon' => 'fa-mobile-alt', 'color' => '#8b5cf6'],
            ['name' => 'Pendidikan', 'icon' => 'fa-graduation-cap', 'color' => '#6366f1'],
            ['name' => 'Kesehatan', 'icon' => 'fa-heartbeat', 'color' => '#14b8a6'],
            ['name' => 'Hiburan', 'icon' => 'fa-film', 'color' => '#a855f7'],
            ['name' => 'Pengeluaran Lainnya', 'icon' => 'fa-ellipsis-h', 'color' => '#64748b'],
        ];

        $createdCategories = ['income' => [], 'expense' => []];

        foreach ($incomeCategories as $cat) {
            $category = Category::create([
                'user_id' => $user->id,
                'name' => $cat['name'],
                'type' => 'income',
                'color' => $cat['color'],
                'icon' => $cat['icon'],
            ]);
            $createdCategories['income'][] = $category;
        }

        foreach ($expenseCategories as $cat) {
            $category = Category::create([
                'user_id' => $user->id,
                'name' => $cat['name'],
                'type' => 'expense',
                'color' => $cat['color'],
                'icon' => $cat['icon'],
            ]);
            $createdCategories['expense'][] = $category;
        }

        // ── Create Transactions (6 months) ──────────────
        $incomeTitles = [
            'Gaji Bulanan', 'Bonus Kuartal', 'Proyek Freelance', 'Dividen Saham',
            'Penjualan Online', 'Komisi', 'Return Investasi',
        ];

        $expenseTitles = [
            'Makan Siang', 'Belanja Bulanan', 'Bensin', 'Bayar Listrik',
            'Bayar Internet', 'Bayar Air', 'Isi Pulsa', 'Beli Buku',
            'Obat', 'Nonton Bioskop', 'Service Motor', 'Laundry',
            'Parkir', 'Ngopi', 'Beli Baju', 'Makan Malam',
            'Grab/Gojek', 'Belanja Online', 'Snack', 'Bayar BPJS',
        ];

        $paymentMethods = ['cash', 'transfer', 'e-wallet', 'qris', 'other'];

        // Generate 6 months of transactions
        for ($monthOffset = 5; $monthOffset >= 0; $monthOffset--) {
            $date = Carbon::now()->subMonths($monthOffset);
            $month = $date->month;
            $year = $date->year;
            $daysInMonth = $date->daysInMonth;

            // Monthly salary (income)
            Transaction::create([
                'user_id' => $user->id,
                'category_id' => $createdCategories['income'][0]->id, // Gaji
                'date' => Carbon::create($year, $month, 1),
                'type' => 'income',
                'amount' => fake()->randomElement([7500000, 8000000, 8500000, 10000000, 12000000]),
                'title' => 'Gaji Bulanan',
                'description' => 'Gaji bulan ' . $date->translatedFormat('F Y'),
                'payment_method' => 'transfer',
            ]);

            // Random additional income (1-3 per month)
            $additionalIncomeCount = fake()->numberBetween(1, 3);
            for ($i = 0; $i < $additionalIncomeCount; $i++) {
                $category = fake()->randomElement($createdCategories['income']);
                Transaction::create([
                    'user_id' => $user->id,
                    'category_id' => $category->id,
                    'date' => Carbon::create($year, $month, fake()->numberBetween(1, $daysInMonth)),
                    'type' => 'income',
                    'amount' => fake()->randomElement([500000, 1000000, 1500000, 2000000, 3000000, 5000000]),
                    'title' => fake()->randomElement($incomeTitles),
                    'description' => fake()->optional(0.5)->sentence(),
                    'payment_method' => fake()->randomElement($paymentMethods),
                ]);
            }

            // Random expenses (15-25 per month)
            $expenseCount = fake()->numberBetween(15, 25);
            for ($i = 0; $i < $expenseCount; $i++) {
                $category = fake()->randomElement($createdCategories['expense']);
                Transaction::create([
                    'user_id' => $user->id,
                    'category_id' => $category->id,
                    'date' => Carbon::create($year, $month, fake()->numberBetween(1, $daysInMonth)),
                    'type' => 'expense',
                    'amount' => fake()->randomElement([
                        15000, 20000, 25000, 30000, 35000, 45000, 50000,
                        75000, 100000, 150000, 200000, 250000, 300000,
                        500000, 750000, 1000000, 1500000,
                    ]),
                    'title' => fake()->randomElement($expenseTitles),
                    'description' => fake()->optional(0.3)->sentence(),
                    'payment_method' => fake()->randomElement($paymentMethods),
                ]);
            }
        }

        // ── Create Budgets ──────────────────────────────
        $currentMonth = now()->month;
        $currentYear = now()->year;

        Budget::create([
            'user_id' => $user->id,
            'category_id' => null, // Overall budget
            'month' => $currentMonth,
            'year' => $currentYear,
            'amount' => 5000000,
        ]);

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $createdCategories['expense'][0]->id, // Makanan
            'month' => $currentMonth,
            'year' => $currentYear,
            'amount' => 1500000,
        ]);

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $createdCategories['expense'][1]->id, // Transportasi
            'month' => $currentMonth,
            'year' => $currentYear,
            'amount' => 800000,
        ]);

        // ── Create Saving Targets ───────────────────────
        SavingTarget::create([
            'user_id' => $user->id,
            'name' => 'Dana Darurat',
            'target_amount' => 50000000,
            'current_amount' => 15000000,
            'deadline' => now()->addMonths(12),
            'status' => 'active',
        ]);

        SavingTarget::create([
            'user_id' => $user->id,
            'name' => 'Liburan Bali',
            'target_amount' => 10000000,
            'current_amount' => 4500000,
            'deadline' => now()->addMonths(3),
            'status' => 'active',
        ]);

        SavingTarget::create([
            'user_id' => $user->id,
            'name' => 'Laptop Baru',
            'target_amount' => 15000000,
            'current_amount' => 15000000,
            'deadline' => now()->subMonth(),
            'status' => 'completed',
        ]);

        // ── Create Bills ────────────────────────────────
        $bills = [
            ['name' => 'Internet', 'amount' => 350000, 'due_day' => 5, 'category' => 'Internet', 'icon' => 'fa-wifi'],
            ['name' => 'Listrik', 'amount' => 450000, 'due_day' => 20, 'category' => 'Listrik', 'icon' => 'fa-bolt'],
            ['name' => 'Air PDAM', 'amount' => 150000, 'due_day' => 15, 'category' => 'Air', 'icon' => 'fa-tint'],
            ['name' => 'Netflix', 'amount' => 186000, 'due_day' => 10, 'category' => 'Hiburan', 'icon' => 'fa-film'],
            ['name' => 'Spotify', 'amount' => 54990, 'due_day' => 12, 'category' => 'Hiburan', 'icon' => 'fa-music'],
            ['name' => 'BPJS Kesehatan', 'amount' => 150000, 'due_day' => 10, 'category' => 'Kesehatan', 'icon' => 'fa-heartbeat'],
        ];

        foreach ($bills as $bill) {
            Bill::create(array_merge($bill, [
                'user_id' => $user->id,
                'recurrence' => 'monthly',
                'status' => 'pending',
            ]));
        }
    }
}
