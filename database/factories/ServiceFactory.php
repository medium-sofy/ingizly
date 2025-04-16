<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'provider_id' => ServiceProvider::factory(),
            'category_id' => Category::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->text(200), 
            'price' => fake()->randomFloat(2, 10, 500),
            'view_count' => fake()->numberBetween(0, 1000),
            'status' => fake()->randomElement(['pending', 'active', 'inactive']),
            'service_type' => fake()->randomElement(['on_site', 'remote', 'bussiness_based']),
            'location' => fake()->address(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }
}