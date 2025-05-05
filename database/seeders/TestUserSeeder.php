<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create a renter user
        User::create([
            'name' => 'Test Renter',
            'email' => 'renter@example.com',
            'password' => Hash::make('password'),
            'role' => 'renter',
            'is_approved' => true,
        ]);

        // Create a tenant user
        User::create([
            'name' => 'Test Tenant',
            'email' => 'tenant@example.com',
            'password' => Hash::make('password'),
            'role' => 'tenant',
            'is_approved' => true,
        ]);
    }
} 