<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            UserSeeder::class,
            ServiceSeeder::class,
            OrderSeeder::class,
            ReviewSeeder::class,
            AvailabilitySeeder::class,
            NotificationSeeder::class,
            ViolationSeeder::class,
        ]);

        // Create a test user for development
        User::factory()->serviceBuyer()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
