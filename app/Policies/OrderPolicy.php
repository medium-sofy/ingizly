<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        // Allow service buyers to view their own orders
        if ($user->id === $order->buyer_id) {
            return true;
        }

        // Allow service providers to view orders for their services
        if ($user->serviceProvider && $user->serviceProvider->user_id === $order->service->provider_id) {
            return true;
        }

        // Allow admins to view any order
        return $user->role === 'admin';
    }


    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'service_buyer']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        // allow service_buyer who own the order
        if ($user->serviceBuyer && $user->serviceBuyer->user_id === $order->buyer_id) {
            return true;
        }

        // allow service_provider who own the service
        if ($user->serviceProvider && $user->serviceProvider->user_id === $order->service->provider_id) {
            return true;
        }

        // allow admin
        if ($user->role === 'admin') {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {

        if ($user->serviceBuyer && $user->serviceBuyer->user_id === $order->buyer_id) {
            return true;
        }

        if ($user->serviceProvider && $user->serviceProvider->user_id === $order->service->provider_id) {
            return true;
        }


        if ($user->role === 'admin') {
            return true;
        }

        return false;
    }
}
