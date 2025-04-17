<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all completed orders
        $completedOrders = Order::where('status', 'completed')->get();
        
        // Create reviews for 80% of completed orders
        foreach ($completedOrders as $order) {
            // 80% chance to create a review
            if (rand(1, 100) <= 80) {
                Review::factory()->create([
                    'service_id' => $order->service_id,
                    'buyer_id' => $order->buyer_id,
                    'order_id' => $order->id,
                ]);
            }
        }
    }
}