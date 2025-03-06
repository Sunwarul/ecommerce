<?php

namespace Modules\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Product\Models\SubCategory>
 */
class SubCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $categoryIds = \Modules\Product\Models\Category::pluck('id')->toArray();

        return [
            'category_id' => $this->faker->randomElement($categoryIds),
            'name' => $this->faker->bothify('SC - ##??'),
            'description' => $this->faker->paragraph(),
            'photo' => $this->faker->imageUrl(),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
