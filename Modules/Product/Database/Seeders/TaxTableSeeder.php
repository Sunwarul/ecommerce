<?php

namespace Modules\Product\Database\Seeders;

use Modules\Product\Models\Tax;
use Illuminate\Database\Seeder;

class TaxTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tax::factory(10)->create();
    }
}
