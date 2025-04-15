<?php

namespace Database\Factories;

use App\Models\Availability;
use App\Models\ServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Availability>
 */
class AvailabilityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Availability::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider_id' => ServiceProvider::factory(),
            'day_of_week' => fake()->randomElement(['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']),
            'start_time' => fake()->time('H:i:s', '12:00:00'),
            'end_time' => fake()->time('H:i:s', '18:00:00'),
            'is_available' => true,
        ];
    }
}