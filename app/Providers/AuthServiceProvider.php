<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceBuyer;
use App\Policies\OrderPolicy;
use App\Policies\ServiceBuyerPolicy;
use App\Policies\ServicePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        ServiceBuyer::class => ServiceBuyerPolicy::class,
        Service::class => ServicePolicy::class,
        Order::class => OrderPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}
