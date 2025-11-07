<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = [
            [
                'name' => 'Central Warehouse',
                'address' => '123 Industrial Area, Tongi, Gazipur',
                'phone' => '01715000001',
                'status' => true,
            ],
            [
                'name' => 'Dhaka City Warehouse',
                'address' => 'House 45, Road 10, Mirpur, Dhaka',
                'phone' => '01716000002',
                'status' => true,
            ],
            [
                'name' => 'Chittagong Warehouse',
                'address' => 'Port Road, Agrabad, Chittagong',
                'phone' => '01817000003',
                'status' => true,
            ],
            [
                'name' => 'Sylhet Warehouse',
                'address' => 'Zindabazar, Sylhet',
                'phone' => '01618000004',
                'status' => false,
            ],
            [
                'name' => 'Rajshahi Warehouse',
                'address' => 'Shaheb Bazar, Rajshahi',
                'phone' => '01519000005',
                'status' => true,
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::create($warehouse);
        }
    }
}
