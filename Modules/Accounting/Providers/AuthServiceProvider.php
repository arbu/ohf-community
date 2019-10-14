<?php

namespace Modules\Accounting\Providers;

use App\Providers\BaseAuthServiceProvider;

class AuthServiceProvider extends BaseAuthServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \Modules\Accounting\Entities\MoneyTransaction::class => \Modules\Accounting\Policies\MoneyTransactionPolicy::class,
    ];

    protected $permissions = [
        'accounting.transactions.view' => [
            'label' => 'accounting::permissions.view_transactions',
            'sensitive' => true,
        ],
        'accounting.transactions.create' => [
            'label' => 'accounting::permissions.create_transactions',
            'sensitive' => true,
        ],
        'accounting.transactions.update' => [
            'label' => 'accounting::permissions.update_transactions',
            'sensitive' => true,
        ],
        'accounting.transactions.delete' => [
            'label' => 'accounting::permissions.delete_transactions',
            'sensitive' => true,
        ],
        'accounting.transactions.book_externally' => [
            'label' => 'accounting::permissions.book_externally',
            'sensitive' => true,
        ],
        'accounting.summary.view' => [
            'label' => 'accounting::permissions.view_summary',
            'sensitive' => false,
        ],
        'accounting.configure' => [
            'label' => 'accounting::permissions.configure',
            'sensitive' => false,
        ],        
    ];

    protected $permission_gate_mappings = [
        'view-accounting-summary' => 'accounting.summary.view',
        'book-accounting-transactions-externally'=> 'accounting.transactions.book_externally',
        'configure-accounting' => 'accounting.configure',
    ];

}
