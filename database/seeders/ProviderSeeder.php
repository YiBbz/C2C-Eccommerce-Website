<?php

namespace Database\Seeders;

use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        $providerUser = User::where('email', 'provider@servicehub.com')->first();

        if ($providerUser) {
            Provider::create([
                'user_id' => $providerUser->id,
                'business_name' => 'John\'s Professional Services',
                'description' => 'Professional service provider offering various home and personal services.',
                'phone' => '+1234567890',
                'address' => '123 Service Street, Service City, SC 12345',
                'business_license' => 'LIC123456',
                'tax_id' => 'TAX123456',
                'is_verified' => true
            ]);
        }
    }
} 