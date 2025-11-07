<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            [
                'title' => 'Bangladeshi Taka',
                'short_code' => 'BDT',
                'symbol' => '৳',
                'exchange_rate' => 1.00,
            ],
            [
                'title' => 'US Dollar',
                'short_code' => 'USD',
                'symbol' => '$',
                'exchange_rate' => 118.50,
            ],
            [
                'title' => 'Euro',
                'short_code' => 'EUR',
                'symbol' => '€',
                'exchange_rate' => 127.20,
            ],
            [
                'title' => 'British Pound',
                'short_code' => 'GBP',
                'symbol' => '£',
                'exchange_rate' => 146.80,
            ],
            [
                'title' => 'Indian Rupee',
                'short_code' => 'INR',
                'symbol' => '₹',
                'exchange_rate' => 1.45,
            ],
            [
                'title' => 'Japanese Yen',
                'short_code' => 'JPY',
                'symbol' => '¥',
                'exchange_rate' => 0.80,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}
