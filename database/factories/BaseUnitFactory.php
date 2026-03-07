<?php

namespace Database\Factories;

use App\Models\BaseUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

class BaseUnitFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BaseUnit::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'status' => fake()->boolean(),
        ];
    }
}
