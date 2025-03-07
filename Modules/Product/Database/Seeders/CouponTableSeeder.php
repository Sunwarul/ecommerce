<?php

namespace Modules\Product\Database\Seeders;

use Modules\Product\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Coupon::factory(20)->create();
    }
}
