<?php

namespace App\Navigation\ContextButtons\Helpers;

use App\Models\Helpers\Responsibility;
use App\Navigation\ContextButtons\ContextButtons;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ResponsibilitiesEditContextButtons implements ContextButtons
{
    public function getItems(View $view): array
    {
        $responsibility = $view->getData()['responsibility'];
        return [
            'delete' => [
                'url' => route('people.helpers.responsibilities.destroy', $responsibility),
                'caption' => __('app.delete'),
                'icon' => 'trash',
                'authorized' => Auth::user()->can('delete', $responsibility),
                'confirmation' => __('responsibilities.confirm_delete_responsibility'),
            ],
            'back' => [
                'url' => route('people.helpers.responsibilities.index'),
                'caption' => __('app.cancel'),
                'icon' => 'times-circle',
                'authorized' => Auth::user()->can('viewAny', Responsibility::class),
            ],
        ];
    }

}
