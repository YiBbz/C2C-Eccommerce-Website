<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Provider;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $provider = Provider::first();
        $homeServicesCategory = Category::where('slug', 'home-services')->first();
        $personalCareCategory = Category::where('slug', 'personal-care')->first();

        if ($provider && $homeServicesCategory && $personalCareCategory) {
            // Home Services
            Service::create([
                'provider_id' => $provider->id,
                'category_id' => $homeServicesCategory->id,
                'title' => 'Home Cleaning Service',
                'description' => 'Professional home cleaning service for all your cleaning needs.',
                'price' => 50.00,
                'duration' => 120,
                'is_available' => true
            ]);

            Service::create([
                'provider_id' => $provider->id,
                'category_id' => $homeServicesCategory->id,
                'title' => 'Plumbing Repair',
                'description' => 'Expert plumbing services for repairs and installations.',
                'price' => 75.00,
                'duration' => 60,
                'is_available' => true
            ]);

            // Personal Care Services
            Service::create([
                'provider_id' => $provider->id,
                'category_id' => $personalCareCategory->id,
                'title' => 'Hair Styling',
                'description' => 'Professional hair styling and cutting services.',
                'price' => 40.00,
                'duration' => 60,
                'is_available' => true
            ]);
        }
    }
} 