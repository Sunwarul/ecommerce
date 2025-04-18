<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    protected $model = Brand::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->bothify('Brand - ##??'),
            'description' => $this->faker->paragraph(),
            'photo' => $this->faker->imageUrl(),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
