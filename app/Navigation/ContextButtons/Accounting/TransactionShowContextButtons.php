<?php

namespace App\Navigation\ContextButtons\Accounting;

use App\Models\Accounting\MoneyTransaction;
use App\Navigation\ContextButtons\ContextButtons;
use Illuminate\View\View;

class TransactionShowContextButtons implements ContextButtons
{
    public function getItems(View $view): array
    {
        $wallet = $view->getData()['wallet'];
        $transaction = $view->getData()['transaction'];
        return [
            'action' => [
                'url' => route('accounting.transactions.edit', $transaction),
                'caption' => __('app.edit'),
                'icon' => 'edit',
                'icon_floating' => 'pencil-alt',
                'authorized' => request()->user()->can('update', $transaction),
            ],
            // 'delete' => [
            //     'url' => route('accounting.transactions.destroy', $transaction),
            //     'caption' => __('app.delete'),
            //     'icon' => 'trash',
            //     'authorized' => Auth::user()->can('delete', $transaction),
            //     'confirmation' => __('accounting.confirm_delete_transaction'),
            // ],
            'back' => [
                'url' => route('accounting.wallets.transactions.index', $wallet),
                'caption' => __('app.close'),
                'icon' => 'times-circle',
                'authorized' => request()->user()->can('viewAny', MoneyTransaction::class),
            ],
        ];
    }

}
