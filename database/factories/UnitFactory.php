<?php

namespace Database\Factories;

use App\Models\BaseUnit;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Unit::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'short_code' => fake()->word(),
            'status' => fake()->boolean(),
            'base_unit_id' => BaseUnit::factory(),
        ];
    }
}
