<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use Illuminate\Support\Str;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Haier Bangladesh',
                'address' => 'Dhaka, Bangladesh',
                'phone' => '',
                'status' => true,
            ],
            // [
            //     'name' => 'Techno World Ltd.',
            //     'address' => 'Agrabad, Chittagong',
            //     'phone' => '01821000002',
            //     'status' => true,
            // ],
            // [
            //     'name' => 'Super Appliance Traders',
            //     'address' => 'Zindabazar, Sylhet',
            //     'phone' => '01622000003',
            //     'status' => true,
            // ],
            // [
            //     'name' => 'Digital Zone',
            //     'address' => 'Shaheb Bazar, Rajshahi',
            //     'phone' => '01523000004',
            //     'status' => false,
            // ],
            // [
            //     'name' => 'Smart Solutions BD',
            //     'address' => 'Khulna Sadar, Khulna',
            //     'phone' => '01924000005',
            //     'status' => true,
            // ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create([
                'code' => 'SUP-' . strtoupper(Str::random(5)),
                'name' => $supplier['name'],
                'address' => $supplier['address'],
                'phone' => $supplier['phone'],
                'status' => $supplier['status'],
            ]);
        }
    }
}
