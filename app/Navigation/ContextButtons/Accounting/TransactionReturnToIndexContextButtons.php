<?php

namespace App\Navigation\ContextButtons\Accounting;

use App\Models\Accounting\MoneyTransaction;
use App\Navigation\ContextButtons\ContextButtons;
use Illuminate\View\View;

class TransactionReturnToIndexContextButtons implements ContextButtons
{
    public function getItems(View $view): array
    {
        $wallet = $view->getData()['wallet'];
        return [
            'back' => [
                'url' => route('accounting.wallets.transactions.index', $wallet),
                'caption' => __('app.cancel'),
                'icon' => 'times-circle',
                'authorized' => request()->user()->can('viewAny', MoneyTransaction::class),
            ],
        ];
    }

}
