<?php

namespace Modules\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Product\Models\Payments>
 */
class PaymentsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $order_ids = \Modules\Product\Models\Order::pluck('id')->toArray();

        return [
            'order_id' => $this->faker->randomElement($order_ids),
            'amount' => $this->faker->randomFloat(2, 100, 5000),
            'provider' => $this->faker->word(),
            'is_active' => $this->faker->boolean(30),
        ];
    }
}
