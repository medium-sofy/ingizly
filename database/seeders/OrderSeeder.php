<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceBuyer;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all active services
        $services = Service::where('status', 'active')->get();
        
        // Get all service buyers
        $buyers = ServiceBuyer::all();

        // Create orders
        foreach ($services as $service) {
            // Create 0-3 orders for each service
            $orderCount = rand(0, 3);
            
            for ($i = 0; $i < $orderCount; $i++) {
                // Randomly select a buyer
                $buyer = $buyers->random();
                
                // Create an order with random status
                $status = fake()->randomElement(['pending', 'accepted', 'in_progress', 'completed', 'cancelled']);
                
                Order::factory()
                    ->create([
                        'service_id' => $service->id,
                        'buyer_id' => $buyer->user_id,
                        'status' => $status,
                    ]);
            }
        }
    }
}