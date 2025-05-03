<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@servicehub.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        // Create a provider user
        User::create([
            'name' => 'John Provider',
            'email' => 'provider@servicehub.com',
            'password' => Hash::make('password'),
            'role' => 'provider'
        ]);

        // Create a customer user
        User::create([
            'name' => 'Jane Customer',
            'email' => 'customer@servicehub.com',
            'password' => Hash::make('password'),
            'role' => 'customer'
        ]);
    }
} 