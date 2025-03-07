<?php

namespace Modules\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Product\Models\Cart>
 */
class CartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $shopping_ids = \Modules\Product\Models\Shopping::pluck('id')->toArray();
        $product_ids = \Modules\Product\Models\Product::pluck('id')->toArray();

        return [
            'shopping_id' => $this->faker->randomElement($shopping_ids),
            'product_id' => $this->faker->randomElement($product_ids),
            'quantity' => $this->faker->randomNumber(),
        ];
    }
}
