<?php

namespace Modules\Product\Database\Seeders;

use Modules\Product\Models\Cart;
use Illuminate\Database\Seeder;

class CartTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cart::factory(5)->create();
    }
}
