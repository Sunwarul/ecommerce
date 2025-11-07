<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Currency;

class CurrencyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Currency::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'short_code' => fake()->word(),
            'date' => fake()->date(),
            'symbol' => fake()->word(),
            'exchange_rate' => fake()->randomFloat(0, 0, 9999999999.),
            'status' => fake()->boolean(),
        ];
    }
}
