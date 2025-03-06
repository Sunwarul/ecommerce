<?php

namespace Modules\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $userIds = \App\Models\User::pluck('id')->toArray();
        $payment_ids = \App\Models\Payments::pluck('id')->toArray();

        return [
            'user_id' => $this->faker->randomElement($userIds),
            'payment_id' => $this->faker->randomElement($payment_ids),
            'total' => $this->faker->randomFloat(2, 100, 5000),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
