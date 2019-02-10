<?php

namespace Modules\Logistics\Http\ViewComposers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

use Modules\Logistics\Entities\Supplier;

class NavigationComposer {

	/**
     * Create the composer.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if (Auth::check()) {
            $nav = $view->getData()['nav'];
            
            array_insert($nav, 6, [[
                'route' => 'logistics.suppliers.index',
                'caption' => __('logistics::logistics.logistics'),
                'icon' => 'exchange',
                'active' => 'logistics*',
                'authorized' => Auth::user()->can('list', Supplier::class)
            ]]);

            $view->with('nav', $nav);
        }
    }
}
