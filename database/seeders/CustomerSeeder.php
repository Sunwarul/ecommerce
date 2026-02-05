<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use Carbon\Carbon;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'customer_type' => 'retailer',
                'name' => 'John Smith',
                'email' => 'john@example.com',
                'phone' => '01711000001',
                'whatsapp_number' => '01711000001',
                'tax_number' => 'TX-1001',
                'currency_id' => '1', // USD
                'status' => true,
                'address' => '123 Main Street, Dhaka',
                'opening_balance' => '250.00',
                'opening_balance_date' => Carbon::now()->subDays(10),
                'opening_balance_type' => 'to_receive',
                'credit_limit' => null,
                'has_credit_limit' => false,
                'photo' => null,
                'file' => null,
                'created_by' => '1',
            ],
            [
                'customer_type' => 'wholesaler',
                'name' => 'GreenMart Distribution',
                'email' => 'sales@greenmart.com',
                'phone' => '01712000002',
                'whatsapp_number' => '01712000002',
                'tax_number' => 'TX-2002',
                'currency_id' => '2', // EUR
                'status' => true,
                'address' => '45 Warehouse Road, Chattogram',
                'opening_balance' => '0.00',
                'opening_balance_date' => Carbon::now()->subDays(5),
                'opening_balance_type' => 'to_pay',
                'credit_limit' => '10000.00',
                'has_credit_limit' => true,
                'photo' => null,
                'file' => null,
                'created_by' => '1',
            ],
            [
                'customer_type' => 'retailer',
                'name' => 'Mizan Electronics',
                'email' => 'info@mizanstore.com',
                'phone' => '01713000003',
                'whatsapp_number' => '01713000003',
                'tax_number' => null,
                'currency_id' => '1', // USD
                'status' => true,
                'address' => '56 Central Plaza, Rajshahi',
                'opening_balance' => '500.00',
                'opening_balance_date' => Carbon::now()->subDays(3),
                'opening_balance_type' => 'to_receive',
                'credit_limit' => null,
                'has_credit_limit' => false,
                'photo' => null,
                'file' => null,
                'created_by' => '1',
            ],
        ];

        foreach ($customers as $data) {
            Customer::create($data);
        }
    }
}
