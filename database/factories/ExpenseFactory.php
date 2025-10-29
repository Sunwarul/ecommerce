<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use App\Models\Warehouse;

class ExpenseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Expense::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'date' => fake()->date(),
            'amount' => fake()->randomFloat(0, 0, 9999999999.),
            'details' => fake()->text(),
            'status' => fake()->boolean(),
            'expense_category_id' => ExpenseCategory::factory(),
            'user_id' => User::factory(),
            'warehouse_id' => Warehouse::factory(),
        ];
    }
}
