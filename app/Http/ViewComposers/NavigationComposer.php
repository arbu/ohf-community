<?php

namespace App\Http\ViewComposers;

use App\Support\Facades\NavigationItems;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\View\View;

class NavigationComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if (Auth::check()) {
            $view->with('nav', collect(NavigationItems::items())
                ->filter(fn ($n) => $n->isAuthorized())
                ->map(fn ($item) => [
                    'active' => Request::is($item->getActive()),
                    'url' => $item->getRoute(),
                    'icon' => $item->getIcon(),
                    'caption' => $item->getCaption(),
                    'badge' => $item->getBadge(),
                ])
                ->toArray()
            );
        }
    }
}
