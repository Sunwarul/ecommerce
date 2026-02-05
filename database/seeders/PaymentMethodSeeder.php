<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'name' => 'Cash in Hand',
                'photo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Bkash',
                'photo' => 'payment/bkash.png',
                'is_active' => true,
            ],
            [
                'name' => 'Nagad',
                'photo' => 'payment/nagad.png',
                'is_active' => true,
            ],
            [
                'name' => 'Rocket',
                'photo' => 'payment/rocket.png',
                'is_active' => true,
            ],
            [
                'name' => 'Bank Transfer',
                'photo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Credit Card',
                'photo' => 'payment/credit-card.png',
                'is_active' => true,
            ],
            [
                'name' => 'Bank Cheque',
                'photo' => null,
                'is_active' => true,
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::create($method);
        }
    }
}
