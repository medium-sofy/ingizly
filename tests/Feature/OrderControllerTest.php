<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceBuyer;
use App\Models\ServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class,RefreshDatabase::class);

beforeEach(function () {
    // Setup authenticated user
    $this->buyer = User::factory()->create(['role' => 'service_buyer']);
    $this->buyerProfile = ServiceBuyer::factory()->create(['user_id' => $this->buyer->id]);
    
    // Create a service to order
    $providerUser = User::factory()->create(['role' => 'service_provider']);
    $provider = ServiceProvider::factory()->create(['user_id' => $providerUser->id]);
    $this->service = Service::factory()->create([
        'provider_id' => $providerUser->id,
        'price' => 100.00,
        'status' => 'active'
    ]);
});

it('allows authenticated buyers to create an order', function () {
    // Act
    $response = $this->actingAs($this->buyer)
        ->post(route('service.book', $this->service->id), [
            'scheduled_date' => now()->addDays(2)->format('Y-m-d'),
            'scheduled_time' => '12:00:00',
            'special_instructions' => 'Please arrive on time'
        ]);
    
    // Assert
    $response->assertRedirect();
    
    $this->assertDatabaseHas('orders', [
        'service_id' => $this->service->id,
        'buyer_id' => $this->buyer->id,
        'status' => 'pending',
        'total_amount' => 100.00,
        'special_instructions' => 'Please arrive on time'
    ]);
});

it('prevents non-buyers from creating orders', function () {
    // Arrange - Create admin user
    $admin = User::factory()->create(['role' => 'admin']);
    
    // Act
    $response = $this->actingAs($admin)
        ->post(route('service.book', $this->service->id), [
            'scheduled_date' => now()->addDays(2)->format('Y-m-d'),
            'scheduled_time' => '12:00:00',
        ]);
    
    // Assert
    $response->assertStatus(302);
    
    $this->assertDatabaseMissing('orders', [
        'service_id' => $this->service->id,
        'buyer_id' => $admin->id,
    ]);
});

it('allows buyers to cancel their pending orders', function () {
    // Arrange - Create an order
    $order = Order::factory()->create([
        'service_id' => $this->service->id,
        'buyer_id' => $this->buyer->id,
        'status' => 'pending'
    ]);
    
    // Act
    $response = $this->actingAs($this->buyer)
        ->post(route('orders.cancel', $order->id));
    
    // Assert
    $response->assertRedirect();
    
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'cancelled'
    ]);
});

it('prevents buyers from cancelling accepted orders', function () {
    // Arrange - Create an accepted order
    $order = Order::factory()->create([
        'service_id' => $this->service->id,
        'buyer_id' => $this->buyer->id,
        'status' => 'accepted'
    ]);
    
    // Act
    $response = $this->actingAs($this->buyer)
        ->post(route('orders.cancel', $order->id));
    
    // Assert
    $response->assertStatus(302); // Unauthorized
    
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'accepted' // Status should not change
    ]);
});

it('allows buyers to approve completed services', function () {
    // Arrange - Create an order pending approval
    $order = Order::factory()->create([
        'service_id' => $this->service->id,
        'buyer_id' => $this->buyer->id,
        'status' => 'pending_approval'
    ]);
    
    // Act
    $response = $this->actingAs($this->buyer)
        ->post(route('buyer.orders.approve', $order->id));
    
    // Assert
    $response->assertRedirect();
    
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'completed'
    ]);
});

it('shows only buyers orders on their order page', function () {
    // Arrange - Create orders for our buyer and another buyer
    $order1 = Order::factory()->create([
        'buyer_id' => $this->buyer->id,
    ]);
    
    $otherBuyer = User::factory()->create(['role' => 'service_buyer']);
    $otherBuyerProfile = ServiceBuyer::factory()->create(['user_id' => $otherBuyer->id]);
    
    $order2 = Order::factory()->create([
        'buyer_id' => $otherBuyer->id,
    ]);
    
    // Act
    $response = $this->actingAs($this->buyer)
        ->get(route('buyer.orders.index'));
    
    // Assert
    $response->assertSuccessful();
    $response->assertViewHas('orders', function ($orders) use ($order1, $order2) {
        return $orders->contains($order1) && !$orders->contains($order2);
    });
});