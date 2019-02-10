<?php

namespace Modules\Logistics\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \Modules\Logistics\Entities\Product::class => \Modules\Logistics\Policies\ProductPolicy::class,
        \Modules\Logistics\Entities\Supplier::class => \Modules\Logistics\Policies\SupplierPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
