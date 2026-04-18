<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ChartOfAccount;

class ProfessionalHeadsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::role('admin')->first();
        $directors = User::role('director')->get();

        $heads = [
            // Income Heads
            ['code' => 'INC-OPS', 'name' => 'Operational Revenue', 'type' => 'income', 'description' => 'Direct revenue from business operations'],
            ['code' => 'INC-PROJ', 'name' => 'Project Funding', 'type' => 'income', 'description' => 'External project-specific funding'],
            ['code' => 'INC-RESR', 'name' => 'Strategic Reserve', 'type' => 'income', 'description' => 'Drawdowns from strategic reserves'],

            // Expense Heads (Professional)
            ['code' => 'EXP-CAPEX', 'name' => 'Capital Expenditure', 'type' => 'expense', 'description' => 'Fixed assets and infrastructure investment', 'budget_limit' => 50000],
            ['code' => 'EXP-OPEX', 'name' => 'Operational Expenses', 'type' => 'expense', 'description' => 'General daily operation costs', 'budget_limit' => 10000],
            ['code' => 'EXP-TRAIN', 'name' => 'Personnel Development', 'type' => 'expense', 'description' => 'Staff training and certifications', 'budget_limit' => 5000],
            ['code' => 'EXP-TRV-INT', 'name' => 'International Travel', 'type' => 'expense', 'description' => 'Business travel outside domestic borders', 'budget_limit' => 15000],
            ['code' => 'EXP-TRV-DOM', 'name' => 'Domestic Operations', 'type' => 'expense', 'description' => 'Local travel and field visits', 'budget_limit' => 3000],
            ['code' => 'EXP-COMP', 'name' => 'Compliance & Legal', 'type' => 'expense', 'description' => 'Legal fees and regulatory compliance', 'budget_limit' => 8000],
            ['code' => 'EXP-DIGI', 'name' => 'Digital Infrastructure', 'type' => 'expense', 'description' => 'SaaS subscriptions and cloud hosting', 'budget_limit' => 4000],
            ['code' => 'EXP-ENT', 'name' => 'Strategic Entertainment', 'type' => 'expense', 'description' => 'Client relationship management dinners', 'budget_limit' => 2000],
        ];

        foreach ($directors as $director) {
            foreach ($heads as $head) {
                // Ensure unique code per director for this seed run
                $headCopy = $head;
                $headCopy['code'] = $head['code'] . '-' . strtoupper(substr($director->name, 0, 3));
                
                ChartOfAccount::create(array_merge($headCopy, [
                    'director_id' => $director->id,
                    'created_by' => $admin->id,
                    'is_active' => true
                ]));
            }
        }
    }
}
