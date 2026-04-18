<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\ChartOfAccount;
use App\Models\User;

class ChartOfAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminId = User::role('admin')->first()->id;

        $categories = [
            // Income
            ['code' => 'INC-001', 'name' => 'Salary', 'type' => 'income', 'description' => 'Monthly executive salary'],
            ['code' => 'INC-002', 'name' => 'Dividends', 'type' => 'income', 'description' => 'Company dividends'],
            ['code' => 'INC-003', 'name' => 'Reimbursements', 'type' => 'income', 'description' => 'General reimbursements'],

            // Expense
            ['code' => 'EXP-001', 'name' => 'Travel & Transport', 'type' => 'expense', 'description' => 'Flights, taxi, public transport', 'budget_limit' => 5000],
            ['code' => 'EXP-002', 'name' => 'Meals & Entertainment', 'type' => 'expense', 'description' => 'Business dinners and client entertainment', 'budget_limit' => 2000],
            ['code' => 'EXP-003', 'name' => 'Fuel', 'type' => 'expense', 'description' => 'Vehicle fuel and maintenance', 'budget_limit' => 1000],
            ['code' => 'EXP-004', 'name' => 'Accommodation', 'type' => 'expense', 'description' => 'Hotel stays', 'budget_limit' => 4000],
            ['code' => 'EXP-005', 'name' => 'Office Supplies', 'type' => 'expense', 'description' => 'Stationery and small office equipment', 'budget_limit' => 500],
            ['code' => 'EXP-006', 'name' => 'Utilities', 'type' => 'expense', 'description' => 'Electricity, water, etc.', 'budget_limit' => 1500],
            ['code' => 'EXP-007', 'name' => 'Communication', 'type' => 'expense', 'description' => 'Mobile and internet bills', 'budget_limit' => 1000],
            ['code' => 'EXP-008', 'name' => 'Medical', 'type' => 'expense', 'description' => 'Staff health and medical costs', 'budget_limit' => 2000],
        ];

        foreach ($categories as $category) {
            ChartOfAccount::create(array_merge($category, [
                'created_by' => $adminId,
                'is_active' => true
            ]));
        }
    }
}

