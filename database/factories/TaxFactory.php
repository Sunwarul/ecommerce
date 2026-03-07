<?php

namespace Database\Factories;

use App\Models\Tax;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaxFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tax::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'rate_type' => fake()->randomElement(['percent', 'flat']),
            'rate_value' => fake()->randomFloat(4, 0, 999999.9999),
            'deleted_at' => fake()->word(),
        ];
    }
}
