<?php

namespace Database\Factories;

use App\Models\ProductAttribute;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductAttributeValueFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'attribute_id' => ProductAttribute::factory(),
            'value' => fake()->word(),
            'display_value' => fake()->word(),
            'color_code' => fake()->word(),
            'deleted_at' => fake()->word(),
        ];
    }
}
