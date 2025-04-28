<?php

namespace App\Policies;

use App\Models\ServiceBuyer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ServiceBuyerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ServiceBuyer $serviceBuyer): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ServiceBuyer $serviceBuyer): bool
    {
        return $user->id === $serviceBuyer->user_id || $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ServiceBuyer $serviceBuyer): bool
    {
        return $user->id === $serviceBuyer->user_id || $user->role === 'admin';
    }
}
