<?php

namespace Modules\Product\Database\Seeders;

use Modules\Product\Models\WarrantyGuarantee;
use Illuminate\Database\Seeder;

class WarrantyGuaranteeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        WarrantyGuarantee::factory(5)->create();
    }
}
