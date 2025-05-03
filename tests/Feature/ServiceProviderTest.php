<?php

namespace Tests\Feature;

use App\Models\ServiceProvider;
use App\Models\User;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class); // Or just in this file

    it('can be created from a user', function () {
        // Arrange
        $user = User::factory()->create([
            'role' => 'service_provider'
        ]);
        
        // Act
        $provider = ServiceProvider::create([
            'user_id' => $user->id,
            'business_name' => 'Test Business',
            'location' => 'Test Location',
            'phone_number' => '1234567890',
            'bio' => 'Test bio',
            'is_verified' => false
        ]);
        
        // Assert
        expect($provider)->toBeInstanceOf(ServiceProvider::class)
            ->and($provider->user_id)->toBe($user->id)
            ->and($provider->business_name)->toBe('Test Business');
    });
    
    it('belongs to a user', function () {
        // Arrange
        $user = User::factory()->create(['role' => 'service_provider']);
        $provider = ServiceProvider::factory()->create(['user_id' => $user->id]);
        
        // Act & Assert
        expect($provider->user)->toBeInstanceOf(User::class)
            ->and($provider->user->id)->toBe($user->id);
    });
    
    it('can have many services', function () {
        // Arrange
        $provider = ServiceProvider::factory()->create();
        
        // Create 3 services for this provider
        $services = Service::factory()->count(3)->create([
            'provider_id' => $provider->user_id
        ]);
        
        // Act & Assert
        expect($provider->services)->toHaveCount(3)
            ->and($provider->services->first())->toBeInstanceOf(Service::class);
    });
