<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Warehouse>
 */
class WarehouseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'code' => $this->faker->randomNumber(),
            'address' => $this->faker->address(),
            'description' => $this->faker->paragraph(),
            'under_business' => $this->faker->word(),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
