<?php

namespace Modules\Product\Database\Seeders;

use Modules\Product\Models\Shopping;
use Illuminate\Database\Seeder;

class ShoppingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Shopping::factory(10)->create();
    }
}
