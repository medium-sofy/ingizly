<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create parent categories
        $parentCategories = [
            'Home Services' => 'fa-home',
            'Professional Services' => 'fa-briefcase',
            'Personal Services' => 'fa-user',
            'Technical Services' => 'fa-laptop',
            'Educational Services' => 'fa-graduation-cap',
        ];

        foreach ($parentCategories as $name => $icon) {
            $parent = Category::create([
                'name' => $name,
                'icon' => $icon,
                'parent_category_id' => null,
            ]);

            // Create subcategories for each parent category
            $this->createSubcategories($parent);
        }
    }

    /**
     * Create subcategories for a parent category
     */
    private function createSubcategories(Category $parent): void
    {
        $subcategories = [];

        switch ($parent->name) {
            case 'Home Services':
                $subcategories = [
                    'Cleaning' => 'fa-broom',
                    'Plumbing' => 'fa-wrench',
                    'Electrical' => 'fa-bolt',
                    'Gardening' => 'fa-leaf',
                    'Painting' => 'fa-paint-roller',
                    'Furniture Assembly' => 'fa-couch',
                ];
                break;
            case 'Professional Services':
                $subcategories = [
                    'Legal Consultation' => 'fa-gavel',
                    'Financial Advice' => 'fa-money-bill',
                    'Business Consulting' => 'fa-chart-line',
                    'Marketing' => 'fa-ad',
                    'Translation' => 'fa-language',
                ];
                break;
            case 'Personal Services':
                $subcategories = [
                    'Beauty & Wellness' => 'fa-spa',
                    'Fitness Training' => 'fa-dumbbell',
                    'Event Planning' => 'fa-calendar-check',
                    'Photography' => 'fa-camera',
                    'Pet Care' => 'fa-paw',
                ];
                break;
            case 'Technical Services':
                $subcategories = [
                    'Computer Repair' => 'fa-desktop',
                    'Mobile Phone Repair' => 'fa-mobile-alt',
                    'Web Development' => 'fa-code',
                    'Graphic Design' => 'fa-palette',
                    'IT Support' => 'fa-headset',
                ];
                break;
            case 'Educational Services':
                $subcategories = [
                    'Tutoring' => 'fa-book',
                    'Language Lessons' => 'fa-comments',
                    'Music Lessons' => 'fa-music',
                    'Art Classes' => 'fa-paint-brush',
                    'Cooking Classes' => 'fa-utensils',
                ];
                break;
        }

        foreach ($subcategories as $name => $icon) {
            Category::create([
                'name' => $name,
                'icon' => $icon,
                'parent_category_id' => $parent->id,
            ]);
        }
    }
}