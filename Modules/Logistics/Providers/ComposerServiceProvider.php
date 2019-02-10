<?php

namespace Modules\Logistics\Providers;

use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('layouts.include.side-nav', 'Modules\Logistics\Http\ViewComposers\NavigationComposer');
        view()->composer(['layouts.app', 'layouts.include.site-header'], 'Modules\Logistics\Http\ViewComposers\ContextMenuComposer');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
