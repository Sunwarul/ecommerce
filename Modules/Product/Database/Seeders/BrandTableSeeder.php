<?php

namespace Modules\Product\Database\Seeders;

use Modules\Product\Models\Brand;
use Illuminate\Database\Seeder;

class BrandTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Brand::factory(10)->create();
    }
}
