<?php

namespace App\Database\Seeders;

use App\Models\Shopping;
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
