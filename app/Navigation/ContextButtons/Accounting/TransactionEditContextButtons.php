<?php

namespace App\Navigation\ContextButtons\Accounting;

use App\Models\Accounting\MoneyTransaction;
use App\Navigation\ContextButtons\ContextButtons;
use Illuminate\View\View;

class TransactionEditContextButtons implements ContextButtons
{
    public function getItems(View $view): array
    {
        $transaction = $view->getData()['transaction'];
        return [
            // TODO
            // 'delete' => [
            //     'url' => route('accounting.transactions.destroy', $transaction),
            //     'caption' => __('app.delete'),
            //     'icon' => 'trash',
            //     'authorized' => Auth::user()->can('delete', $transaction),
            //     'confirmation' => __('accounting.confirm_delete_transaction'),
            // ],
            'back' => [
                'url' => route('accounting.transactions.show', $transaction),
                'caption' => __('app.cancel'),
                'icon' => 'times-circle',
                'authorized' => request()->user()->can('viewAny', MoneyTransaction::class),
            ],
        ];
    }

}
