<?php

namespace Modules\Product\Database\Seeders;

use Modules\Product\Models\Order;
use Illuminate\Database\Seeder;

class OrderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        return Order::factory(10)->create();
    }
}
