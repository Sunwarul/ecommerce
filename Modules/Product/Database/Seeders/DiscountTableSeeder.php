<?php

namespace Modules\Product\Database\Seeders;

use Modules\Product\Models\Discount;
use Illuminate\Database\Seeder;

class DiscountTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Discount::factory(15)->create();
    }
}
