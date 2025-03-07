<?php

namespace Modules\Product\Database\Seeders;

use Modules\Product\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Warehouse::factory(5)->create();
    }
}
