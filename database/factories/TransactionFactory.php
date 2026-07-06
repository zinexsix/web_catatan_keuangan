<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['income', 'expense']);

        $incomeTitles = [
            'Gaji Bulanan', 'Bonus Tahunan', 'Proyek Freelance', 'Dividen Saham',
            'Penjualan Online', 'Komisi Penjualan', 'Uang Saku', 'Hadiah',
            'Return Investasi', 'Pendapatan Sewa',
        ];

        $expenseTitles = [
            'Makan Siang', 'Belanja Bulanan', 'Bensin', 'Bayar Listrik',
            'Bayar Internet', 'Bayar Air', 'Isi Pulsa', 'Beli Buku',
            'Obat', 'Nonton Bioskop', 'Bayar BPJS', 'Service Motor',
            'Laundry', 'Parkir', 'Ngopi', 'Beli Baju', 'Makan Malam',
            'Grab/Gojek', 'Belanja Online', 'Snack & Minuman',
        ];

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'type' => $type,
            'amount' => $type === 'income'
                ? $this->faker->randomElement([3000000, 4500000, 5000000, 7500000, 10000000, 15000000, 500000, 1000000, 2000000])
                : $this->faker->numberBetween(5000, 2000000),
            'title' => $type === 'income'
                ? $this->faker->randomElement($incomeTitles)
                : $this->faker->randomElement($expenseTitles),
            'description' => $this->faker->optional(0.5)->sentence(),
            'payment_method' => $this->faker->randomElement(['cash', 'transfer', 'e-wallet', 'qris', 'other']),
            'receipt_image' => null,
        ];
    }
}
