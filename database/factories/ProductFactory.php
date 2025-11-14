<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $categoryIds = \App\Models\Category::pluck('id')->toArray();
        $tagIds = \App\Models\Tag::pluck('id')->toArray();
        $taxIds = \App\Models\Tax::pluck('id')->toArray();
        $brandIds = \App\Models\Brand::pluck('id')->toArray();
        $userIds = \App\Models\User::pluck('id')->toArray();

        return [
            'category_id' => $this->faker->randomElement($categoryIds),
            'tax_id' => $this->faker->randomElement($taxIds),
            'brand_id' => $this->faker->randomElement($brandIds),
            'tag_id' => $this->faker->randomElement($tagIds),
            'added_by' => $this->faker->randomElement($userIds),
            'product_name' => $this->faker->word(),
            'price' => $this->faker->randomFloat(2, 100, 5000),
            'discount_price' => $this->faker->randomFloat(2, 10, 500),
            'title' => $this->faker->text(),
            'code' => $this->faker->text(),
            'slug' => $this->faker->word(),
            'dimantion' => $this->faker->word(),
            'weight' => $this->faker->word(),
            'sku' => $this->faker->word(),
            'meterials' => $this->faker->word(),
            'description' => $this->faker->paragraph(),
            'other_info' => $this->faker->paragraph(),
            'pro_photo' => $this->faker->imageUrl(),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
