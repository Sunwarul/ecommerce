<?php

namespace Modules\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Product\Models\ProductStock>
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
        $product_ids = \Modules\Product\Models\Product::pluck('id')->toArray();
        $warehouse_ids = \Modules\Product\Models\Warehouse::pluck('id')->toArray();

        return [
            'product_id' => $this->faker->randomElement($product_ids),
            'warehouse_id' => $this->faker->randomElement($warehouse_ids),
            'quantity' => $this->faker->randomNumber(),
            'alert_quantity' => $this->faker->randomNumber(),
        ];
    }
}
