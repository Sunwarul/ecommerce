<?php

namespace Modules\Product\Database\Seeders;

use Modules\Product\Models\Unit;
use Illuminate\Database\Seeder;

class UnitTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Unit::factory(10)->create();
    }
}
