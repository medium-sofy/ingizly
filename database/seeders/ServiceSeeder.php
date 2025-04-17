<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceImage;
use App\Models\ServiceProvider;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all service providers
        $providers = ServiceProvider::all();

        // Get all categories
        $categories = Category::whereNotNull('parent_category_id')->get();

        if ($categories->isEmpty()) {
            $this->command->error('No subcategories found. Make sure CategorySeeder has been run.');
            return;
        }

        foreach ($providers as $provider) {
            // Create 2-5 services for each provider
            for ($i = 0; $i < rand(2, 5); $i++) {
                // Randomly select a category
                $category = $categories->random();

                $service = Service::create([
                    'provider_id' => $provider->user_id,
                    'category_id' => $category->id,
                    'title' => fake()->sentence(3),
                    'description' => fake()->paragraph(),
                    'price' => fake()->randomFloat(2, 10, 500),
                    'view_count' => fake()->numberBetween(0, 1000),
                    'status' => 'active',
                    'service_type' => fake()->randomElement(['on_site', 'remote', 'bussiness_based']),
                    'location' => fake()->address(),
                ]);

                // Create a primary image
                ServiceImage::create([
                    'service_id' => $service->id,
                    'image_url' => fake()->imageUrl(640, 480, 'services', true),
                    'is_primary' => true,
                ]);

                // Create 2-4 additional images
                for ($j = 0; $j < rand(2, 4); $j++) {
                    ServiceImage::create([
                        'service_id' => $service->id,
                        'image_url' => fake()->imageUrl(640, 480, 'services', true),
                        'is_primary' => false,
                    ]);
                }
            }
        }
    }
}
