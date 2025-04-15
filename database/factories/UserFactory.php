<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\ServiceBuyer;
use App\Models\ServiceProvider;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'service_buyer', // Default role
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ])->afterCreating(function (User $user) {
            // Create the corresponding admin record
            Admin::create(['user_id' => $user->id]);
        });
    }

    /**
     * Indicate that the user is a service buyer (default role).
     */
    public function serviceBuyer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'service_buyer',
        ])->afterCreating(function (User $user) {
            // Create the corresponding service_buyer record
            ServiceBuyer::create([
                'user_id' => $user->id,
                'phone_number' => fake()->numerify('###########'),
                'location' => fake()->city(),
            ]);
        });
    }

    /**
     * Indicate that the user is a service provider.
     */
    public function serviceProvider(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'service_provider',
        ])->afterCreating(function (User $user) {
            // Create the corresponding service_provider record
            ServiceProvider::create([
                'user_id' => $user->id,
                'phone_number' => fake()->numerify('###########'),
                'bio' => fake()->paragraph(),
                'location' => fake()->city(),
                'business_name' => fake()->company(),
                'business_address' => fake()->address(),
                'avg_rating' => fake()->randomFloat(1, 3, 5),
                'provider_type' => fake()->randomElement(['handyman', 'bussiness_owner']),
            ]);
        });
    }
}
