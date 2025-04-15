<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\User;
use App\Models\Violation;
use Illuminate\Database\Seeder;

class ViolationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all services
        $services = Service::all();
        
        // Get all users who are service buyers
        $users = User::where('role', 'service_buyer')->get();
        
        // Create violations for 5% of services
        $violationCount = ceil($services->count() * 0.05);
        
        for ($i = 0; $i < $violationCount; $i++) {
            // Randomly select a service and a user
            $service = $services->random();
            $user = $users->random();
            
            Violation::factory()->create([
                'service_id' => $service->id,
                'user_id' => $user->id,
            ]);
        }
    }
}