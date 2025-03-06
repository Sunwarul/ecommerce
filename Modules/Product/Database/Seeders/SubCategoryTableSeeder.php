<?php

namespace Modules\Product\Database\Seeders;

use Modules\Product\Models\SubCategory;
use Illuminate\Database\Seeder;

class SubCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SubCategory::factory(100)->create();
    }
}
