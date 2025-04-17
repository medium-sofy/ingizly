<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceImageFactory extends Factory
{
    protected $model = ServiceImage::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'image_url' => fake()->imageUrl(640, 480, 'services', true),
            'is_primary' => false,
        ];
    }

    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }
}