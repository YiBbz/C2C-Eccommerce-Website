<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Home Services',
                'description' => 'Professional services for your home maintenance and improvement needs.',
                'slug' => 'home-services'
            ],
            [
                'name' => 'Personal Care',
                'description' => 'Services focused on personal well-being and grooming.',
                'slug' => 'personal-care'
            ],
            [
                'name' => 'Professional Services',
                'description' => 'Business and professional services for individuals and companies.',
                'slug' => 'professional-services'
            ],
            [
                'name' => 'Education',
                'description' => 'Tutoring and educational services for all ages.',
                'slug' => 'education'
            ],
            [
                'name' => 'Health & Wellness',
                'description' => 'Services focused on physical and mental health.',
                'slug' => 'health-wellness'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
} 