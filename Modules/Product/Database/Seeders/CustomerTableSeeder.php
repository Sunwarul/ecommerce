<?php

namespace Modules\Product\Database\Seeders;

use Modules\Product\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Customer::factory()->count(20)->create();
    }
}
