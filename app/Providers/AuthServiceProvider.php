<?php

namespace App\Providers;

use App\Models\WorkOrder;
use App\Policies\WorkOrderPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        WorkOrder::class => WorkOrderPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
