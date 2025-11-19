<?php

namespace Database\Seeders;

use App\Models\Tax;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TaxSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $taxes = [
            [
                'name' => 'No Tax',
                'rate_type' => 'percent',
                'rate_value' => 0.0000,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'VAT 5%',
                'rate_type' => 'percent',
                'rate_value' => 5.0000,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'VAT 7.5%',
                'rate_type' => 'percent',
                'rate_value' => 7.5000,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'VAT 10%',
                'rate_type' => 'percent',
                'rate_value' => 10.0000,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Fixed Tax $2',
                'rate_type' => 'flat',
                'rate_value' => 2.0000,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        Tax::insert($taxes);
    }
}
