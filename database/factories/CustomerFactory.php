<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Customer;

class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'customer_type' => fake()->randomElement(["retailer", "wholesaler"]),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'whatsapp_number' => fake()->word(),
            'tax_number' => fake()->word(),
            'currency_id' => fake()->word(),
            'status' => true,
            'address' => fake()->text(),
            'opening_balance' => 0,
            'opening_balance_date' => fake()->date(),
            'opening_balance_type' => fake()->randomElement(["to_pay", "to_receive"]),
            'credit_limit' => fake()->word(),
            'has_credit_limit' => fake()->boolean(),
            'photo' => fake()->word(),
            'file' => fake()->word(),
            'created_by' => fake()->word(),
        ];
    }
}
