<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $types = ['income', 'expense'];
        $type = $this->faker->randomElement($types);

        $incomeCategories = [
            ['name' => 'Gaji', 'icon' => 'fa-money-bill-wave', 'color' => '#10b981'],
            ['name' => 'Bonus', 'icon' => 'fa-gift', 'color' => '#06b6d4'],
            ['name' => 'Investasi', 'icon' => 'fa-chart-line', 'color' => '#8b5cf6'],
            ['name' => 'Freelance', 'icon' => 'fa-laptop-code', 'color' => '#f59e0b'],
            ['name' => 'Lainnya', 'icon' => 'fa-plus-circle', 'color' => '#64748b'],
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
            ['name' => 'Kesehatan', 'icon' => 'fa-heartbeat', 'color' => '#ef4444'],
            ['name' => 'Hiburan', 'icon' => 'fa-film', 'color' => '#a855f7'],
            ['name' => 'Lainnya', 'icon' => 'fa-ellipsis-h', 'color' => '#64748b'],
        ];

        $category = $type === 'income'
            ? $this->faker->randomElement($incomeCategories)
            : $this->faker->randomElement($expenseCategories);

        return [
            'user_id' => User::factory(),
            'name' => $category['name'],
            'type' => $type,
            'color' => $category['color'],
            'icon' => $category['icon'],
        ];
    }
}
