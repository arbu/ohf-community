<?php

namespace App\Navigation\ContextButtons\Accounting;

use App\Models\Accounting\MoneyTransaction;
use App\Navigation\ContextButtons\ContextButtons;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TransactionIndexContextButtons implements ContextButtons
{
    public function getItems(View $view): array
    {
        $wallet = $view->getData()['wallet'];
        return [
            'action' => [
                'url' => route('accounting.wallets.transactions.create', $wallet),
                'caption' => __('app.add'),
                'icon' => 'plus-circle',
                'icon_floating' => 'plus',
                'authorized' => request()->user()->can('create', MoneyTransaction::class),
            ],
            'summary' => [
                'url' => route('accounting.wallets.transactions.summary', $wallet),
                'caption' => __('accounting.summary'),
                'icon' => 'calculator',
                'authorized' => Gate::allows('view-accounting-summary'),
            ],
            'export' => [
                'url' => route('accounting.wallets.transactions.export', $wallet),
                'caption' => __('app.export'),
                'icon' => 'download',
                'authorized' => request()->user()->can('viewAny', MoneyTransaction::class),
            ],
        ];
    }

}
