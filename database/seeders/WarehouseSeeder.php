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
                'name' => 'Rangpur Warehouse',
                'address' => 'Port Road, Agrabad, Rangpur',
                'phone' => '01817000003',
                'status' => true,
            ]
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::create($warehouse);
        }
    }
}
