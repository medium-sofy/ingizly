<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceBuyer;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        $order = Order::factory()->completed()->create();
        
        return [
            'service_id' => $order->service_id,
            'buyer_id' => $order->buyer_id,
            'order_id' => $order->id,
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->paragraph(),
        ];
    }
}