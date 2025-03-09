<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductStock>
 */
class ProductStockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $product_ids = \App\Models\pluck('id')->toArray();
        $warehouse_ids = \App\Models\Warehouse::pluck('id')->toArray();

        return [
            'product_id' => $this->faker->randomElement($product_ids),
            'warehouse_id' => $this->faker->randomElement($warehouse_ids),
            'quantity' => $this->faker->randomNumber(),
            'alert_quantity' => $this->faker->randomNumber(),
        ];
    }
}
