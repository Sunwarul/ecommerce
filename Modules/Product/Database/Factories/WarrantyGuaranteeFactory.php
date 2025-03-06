<?php

namespace Modules\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WarrantyGuarantee>
 */
class WarrantyGuaranteeFactory extends Factory
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
            'type' => $this->faker->word('warranty'),
            'duration' => $this->faker->date(),
            'description' => $this->faker->paragraph(),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
