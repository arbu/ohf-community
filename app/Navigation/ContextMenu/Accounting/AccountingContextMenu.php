<?php

namespace App\Navigation\ContextMenu\Accounting;

use App\Navigation\ContextMenu\ContextMenu;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AccountingContextMenu implements ContextMenu
{
    public function getItems(View $view): array
    {
        $wallet = $view->getData()['wallet'];
        return [
            'wallets' => [
                'url' => route('accounting.wallets.index'),
                'caption' => __('accounting.wallets'),
                'icon' => 'wallet',
                'authorized' => Gate::allows('configure-accounting'),
            ],
            'book' => [
                'url' => route('accounting.webling.index', $wallet),
                'caption' => __('accounting.book_to_webling'),
                'icon' => 'cloud-upload-alt',
                'authorized' => request()->user()->can('book-accounting-transactions-externally'),
            ],
        ];
    }
}
