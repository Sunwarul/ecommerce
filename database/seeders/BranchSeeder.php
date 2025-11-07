<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use Illuminate\Support\Str;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'name' => 'Main Branch',
                'address' => '123 Main Street, Dhaka',
                'code' => 'BR-' . Str::upper(Str::random(4)),
                'phone' => '01710000000',
                'is_active' => true,
            ],
            [
                'name' => 'Uttara Branch',
                'address' => 'House 12, Road 7, Sector 3, Uttara, Dhaka',
                'code' => 'BR-' . Str::upper(Str::random(4)),
                'phone' => '01711000001',
                'is_active' => true,
            ],
            [
                'name' => 'Chittagong Branch',
                'address' => '56 Agrabad, Chittagong',
                'code' => 'BR-' . Str::upper(Str::random(4)),
                'phone' => '01812000002',
                'is_active' => true,
            ],
            [
                'name' => 'Sylhet Branch',
                'address' => 'Zindabazar, Sylhet',
                'code' => 'BR-' . Str::upper(Str::random(4)),
                'phone' => '01613000003',
                'is_active' => false,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
