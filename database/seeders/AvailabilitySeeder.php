<?php

namespace Database\Seeders;

use App\Models\Availability;
use App\Models\ServiceProvider;
use Illuminate\Database\Seeder;

class AvailabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all service providers
        $providers = ServiceProvider::all();
        
        $daysOfWeek = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        
        foreach ($providers as $provider) {
            // Create availability for each day of the week
            foreach ($daysOfWeek as $day) {
                // 80% chance to be available on a given day
                $isAvailable = rand(1, 100) <= 80;
                
                // Always provide time values, even when not available
                $startTime = fake()->time('H:i:s', '12:00:00');
                $endTime = fake()->time('H:i:s', '18:00:00');
                
                Availability::factory()->create([
                    'provider_id' => $provider->user_id,
                    'day_of_week' => $day,
                    'is_available' => $isAvailable,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                ]);
            }
        }
    }
}