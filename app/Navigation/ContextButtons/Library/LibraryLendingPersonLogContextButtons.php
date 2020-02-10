<?php

namespace App\Navigation\ContextButtons\Library;

use App\Navigation\ContextButtons\ContextButtons;

use App\Models\People\Person;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class LibraryLendingPersonLogContextButtons implements ContextButtons {

    public function getItems(View $view): array
    {
        $person = $view->getData()['person'];
        return [
            'back' => [
                'url' => route('library.lending.person', $person),
                'caption' => __('app.close'),
                'icon' => 'times-circle',
                'authorized' => Auth::user()->can('list', Person::class),
            ]
        ];
    }

}