<?php

namespace App\Navigation\ContextButtons;

use App\Helper;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class HelperIndexContextButtons implements ContextButtons {

    public function getItems(View $view): array
    {
        return [
            'action' => [
                'url' => route('people.helpers.createFrom'),
                'caption' => __('app.register'),
                'icon' => 'plus-circle',
                'icon_floating' => 'plus',
                'authorized' => Auth::user()->can('create', Helper::class)
            ],
            'report' => [
                'url' => route('people.helpers.report'),
                'caption' => __('app.report'),
                'icon' => 'bar-chart',
                'authorized' => Auth::user()->can('list', Helper::class)
            ],                    
            'badges' => [
                'url' => route('badges.index', ['source' => 'helpers']),
                'caption' => __('people.badges'),
                'icon' => 'id-card',
                'authorized' => Auth::user()->can('list', Helper::class) && Gate::allows('create-badges')
            ], 
        ];
    }

}
