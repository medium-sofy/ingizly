<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceBuyer;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'buyer_id' => ServiceBuyer::factory(),
            'status' => fake()->randomElement(['pending', 'accepted', 'in_progress', 'completed', 'cancelled']),
            'total_amount' => fake()->randomFloat(2, 20, 1000),
            'scheduled_date' => fake()->dateTimeBetween('now', '+2 months'),
            'scheduled_time' => fake()->time(),
            'location' => fake()->address(),
            'special_instructions' => fake()->paragraph(),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }
}