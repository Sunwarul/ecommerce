<?php

namespace Modules\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Product\Models\Shopping>
 */
class ShoppingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $customer_ids = \Modules\Product\Models\Customer::pluck('id')->toArray();

        return [
            'customer_id' => $this->faker->randomElement($customer_ids),
            'total' => $this->faker->randomFloat(2, 100, 5000),
        ];
    }
}
