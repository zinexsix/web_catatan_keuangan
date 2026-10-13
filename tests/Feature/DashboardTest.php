<?php

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

test('dashboard aggregates transactions per month, day and category', function () {
    Carbon::setTestNow('2026-10-15 10:00:00');

    $user = User::factory()->create();
    $food = Category::create(['user_id' => $user->id, 'name' => 'Makan', 'type' => 'expense', 'color' => '#ff0000']);
    $salary = Category::create(['user_id' => $user->id, 'name' => 'Gaji', 'type' => 'income']);

    $add = fn (Category $cat, string $date, int $amount) => Transaction::create([
        'user_id' => $user->id, 'category_id' => $cat->id, 'date' => $date, 'type' => $cat->type,
        'amount' => $amount, 'title' => 'x', 'payment_method' => 'cash',
    ]);

    $add($salary, '2026-10-01', 1000000);
    $add($food, '2026-10-02', 20000);
    $add($food, '2026-10-31', 30000);   // last day of the month must be included
    $add($food, '2026-09-10', 50000);   // last month
    $add($food, '2026-04-30', 99000);   // 6 months ago: outside the chart window
    $add($food, '2026-11-01', 77000);   // next month: excluded

    $response = $this->actingAs($user)->get('/dashboard')->assertOk();

    expect($response->viewData('incomeCurrentMonth'))->toBe(1000000.0)
        ->and($response->viewData('expenseCurrentMonth'))->toBe(50000.0)
        ->and($response->viewData('transactionsCountCurrentMonth'))->toBe(3)
        ->and($response->viewData('expenseDataset'))->toBe([0.0, 0.0, 0.0, 0.0, 50000.0, 50000.0])
        ->and($response->viewData('categoryLabels'))->toBe(['Makan'])
        ->and($response->viewData('categoryTotals'))->toBe([50000.0])
        ->and($response->viewData('dailyExpenses')[1])->toBe(20000.0)
        ->and($response->viewData('dailyExpenses')[30])->toBe(30000.0)
        ->and($response->viewData('expenseDiff')['formatted'])->toBe('+0%');

    Carbon::setTestNow();
});
