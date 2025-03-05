<?php

namespace Modules\Product\Database\Seeders;

use App\Models\ProductStock;
use Illuminate\Database\Seeder;

class ProductStockTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductStock::factory(20)->create();
    }
}
