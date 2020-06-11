<?php

namespace App\Navigation\Drawer\Accounting;

use App\Models\Accounting\MoneyTransaction;
use App\Navigation\Drawer\BaseNavigationItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AccountingNavigationItem extends BaseNavigationItem
{
    public function getRoute(): string
    {
        return /*! request()->user()->can('viewAny', MoneyTransaction::class) && Gate::allows('view-accounting-summary')
            ? route('accounting.wallets.transactions.summary') : */
            route('accounting.wallets.index');
    }

    protected $caption = 'accounting.accounting';

    protected $icon = 'money-bill-alt';

    protected $active = 'accounting/*';

    public function isAuthorized(): bool
    {
        return request()->user()->can('viewAny', MoneyTransaction::class) || Gate::allows('view-accounting-summary');
    }
}
