<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'currency' => 'INR',
                'symbol' => '₹',
                'amount_in_dollars' => 0.012, // Approx conversion
            ],
            [
                'currency' => 'Dollar',
                'symbol' => '$',
                'amount_in_dollars' => 1.00,
            ],
            [
                'currency' => 'EURO',
                'symbol' => '€',
                'amount_in_dollars' => 1.09, // Approx conversion
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}
