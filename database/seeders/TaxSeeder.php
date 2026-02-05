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
                'name' => 'Mushok 6.3 (15%)',
                'rate_type' => 'percent',
                'rate_value' => 15.0000,
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
        ];

        Tax::insert($taxes);
    }
}
