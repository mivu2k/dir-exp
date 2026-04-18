<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'department' => 'Management',
            'is_active' => true,
        ]);

        $admin->assignRole('admin');

        // Create a Director and Accountant for testing
        $director = User::create([
            'name' => 'Director User',
            'email' => 'director@example.com',
            'password' => Hash::make('password'),
            'department' => 'Sales',
            'is_active' => true,
        ]);
        $director->assignRole('director');

        $accountant = User::create([
            'name' => 'Accountant User',
            'email' => 'accountant@example.com',
            'password' => Hash::make('password'),
            'department' => 'Finance',
            'is_active' => true,
        ]);
        $accountant->assignRole('accountant');
    }
}

