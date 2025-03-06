<?php

namespace Modules\Product\Database\Seeders;

use Modules\Product\Models\Inventory;
use Illuminate\Database\Seeder;

class InventoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Inventory::factory(10)->create();
    }
}
