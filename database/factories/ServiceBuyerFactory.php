<?php

namespace Database\Factories;

use App\Models\ServiceBuyer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceBuyerFactory extends Factory
{
    protected $model = ServiceBuyer::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'service_buyer']),
            'phone_number' => fake()->numerify('###########'),
            'location' => fake()->city(),
        ];
    }
}
