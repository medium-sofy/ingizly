<?php

namespace Database\Factories;

use App\Models\ServiceProvider;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceProviderFactory extends Factory
{
    protected $model = ServiceProvider::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'service_provider']),
            'phone_number' => fake()->numerify('###########'),
            'bio' => fake()->paragraph(),
            'location' => fake()->city(),
            'business_name' => fake()->company(),
            'business_address' => fake()->address(),
            'avg_rating' => fake()->randomFloat(1, 3, 5),
            'provider_type' => fake()->randomElement(['handyman', 'bussiness_owner']),
            'is_verified' => fake()->boolean(20),
        ];
    }
}
