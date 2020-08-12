<?php

namespace App\Navigation\ContextMenu\Accounting;

use App\Models\Accounting\MoneyTransaction;
use App\Navigation\ContextMenu\ContextMenu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AccountingContextMenu implements ContextMenu
{
    public function getItems(): array
    {
        return [
            'wallets' => [
                'url' => route('accounting.wallets.index'),
                'caption' => __('accounting.wallets'),
                'icon' => 'wallet',
                'authorized' => Gate::allows('configure-accounting'),
            ],
            'book' => [
                'url' => route('accounting.webling.index'),
                'caption' => __('accounting.book_to_webling'),
                'icon' => 'cloud-upload-alt',
                'authorized' => Auth::user()->can('book-accounting-transactions-externally'),
            ],
            'import' => [
                'url' => route('accounting.transactions.import'),
                'caption' => __('app.import'),
                'icon' => 'upload',
                'authorized' => Auth::user()->can('import', MoneyTransaction::class),
            ],
        ];
    }
}
