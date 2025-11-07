<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // --- Step 1: Create expense categories ---
            $categories = [
                'Office Supplies',
                'Utilities',
                'Transportation',
                'Maintenance',
                'Marketing',
                'Travel',
                'Miscellaneous',
            ];

            $categoryIds = [];
            foreach ($categories as $name) {
                $category = ExpenseCategory::create([
                    'name' => $name,
                    'status' => true,
                ]);
                $categoryIds[] = $category->id;
            }

            // --- Step 2: Create example expenses ---
            $expenses = [
                [
                    'title' => 'Printer Ink Purchase',
                    'amount' => 2500.00,
                    'details' => 'Purchased printer ink cartridges for office use.',
                    'expense_category_id' => $categoryIds[0], // Office Supplies
                ],
                [
                    'title' => 'Electric Bill - October',
                    'amount' => 7800.00,
                    'details' => 'Monthly electricity bill for main office.',
                    'expense_category_id' => $categoryIds[1], // Utilities
                ],
                [
                    'title' => 'Staff Transport Cost',
                    'amount' => 12000.00,
                    'details' => 'Monthly staff transport expenses.',
                    'expense_category_id' => $categoryIds[2], // Transportation
                ],
                [
                    'title' => 'AC Servicing',
                    'amount' => 3500.00,
                    'details' => 'Servicing of air conditioners in warehouse.',
                    'expense_category_id' => $categoryIds[3], // Maintenance
                ],
                [
                    'title' => 'Facebook Ad Campaign',
                    'amount' => 15000.00,
                    'details' => 'Online marketing for product launch.',
                    'expense_category_id' => $categoryIds[4], // Marketing
                ],
            ];

            foreach ($expenses as $expense) {
                Expense::create([
                    'title' => $expense['title'],
                    'date' => Carbon::now()->subDays(rand(1, 30))->format('Y-m-d'),
                    'amount' => $expense['amount'],
                    'details' => $expense['details'],
                    'status' => true,
                    'expense_category_id' => $expense['expense_category_id'],
                    'user_id' => 1, // you can adjust dynamically
                    'warehouse_id' => 1, // adjust if needed
                    'attachment' => null,
                ]);
            }
        });
    }
}
