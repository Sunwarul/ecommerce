<?php

namespace App\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'code' => $this->faker->numerify('SCOMMERCE-#####'),
            'categories' => $this->faker->word(),
            'valid_from' => $this->faker->dateTimeBetween('-7 days', '+2 months'),
            'valid_to' => $this->faker->date('2022-5-20'),
            'discount_type' => $this->faker->randomFloat(2, 50, 3000),
            'type' => $this->faker->word(),
            'amount' => $this->faker->randomFloat(2, 100, 5000),
            'minimum_order' => $this->faker->randomNumber(4),
            'use_limit' => $this->faker->randomNumber(4),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
