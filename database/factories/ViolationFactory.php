<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\User;
use App\Models\Violation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ViolationFactory extends Factory
{
    protected $model = Violation::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'user_id' => User::factory(),
            'reason' => fake()->paragraph(),
            'status' => fake()->randomElement(['pending', 'investigating', 'resolved', 'dismissed']),
            'admin_note' => fake()->boolean(70) ? fake()->paragraph() : null,
            'resolved_at' => fake()->boolean(50) ? fake()->dateTimeThisYear() : null,
        ];
    }
}
