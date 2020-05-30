<?php

namespace App\Http\Controllers\Accounting;

use App\Exports\Accounting\MoneyTransactionsExport;
use App\Exports\Accounting\MoneyTransactionsMonthsExport;
use App\Exports\Accounting\WeblingMoneyTransactionsExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Export\ExportableActions;
use App\Http\Requests\Accounting\StoreTransaction;
use App\Models\Accounting\MoneyTransaction;
use App\Models\Accounting\Wallet;
use App\Services\Accounting\CurrentWalletService;
use App\Services\Accounting\TransactionRepository;
use App\Support\Accounting\Webling\Entities\Entrygroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Setting;

class MoneyTransactionsController extends Controller
{
    use ExportableActions;

    private TransactionRepository $repository;

    public function __construct(TransactionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, CurrentWalletService $currentWallet)
    {
        $this->authorize('viewAny', MoneyTransaction::class);

        if ($request->has('wallet_id')) {
            $wallet = Wallet::find($request->input('wallet_id'));
            $this->authorize('view', $wallet);
            $currentWallet->set($wallet);
        }
        if ($currentWallet->get() === null) {
            return redirect()->route('accounting.wallets.change');
        }

        return view('accounting.transactions.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(CurrentWalletService $currentWallet)
    {
        $this->authorize('create', MoneyTransaction::class);

        $wallets = Wallet::orderBy('name')
            ->get()
            ->filter(fn ($wallet) => request()->user()->can('view', $wallet))
            ->map(fn ($wallet) => [
                'id' => $wallet->id,
                'name' => $wallet->name,
                'new_receipt_no' => $this->repository->getNextFreeReceiptNo($wallet),
            ]);
        if ($wallets->isEmpty()) {
            return redirect()->route('accounting.wallets.change');
        }
        $wallet = $currentWallet->get();

        return view('accounting.transactions.create', [
            'wallet' => $wallet,
            'wallets' => $wallets,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Accounting\MoneyTransaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(MoneyTransaction $transaction)
    {
        $this->authorize('view', $transaction);

        return view('accounting.transactions.show', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Accounting\MoneyTransaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(MoneyTransaction $transaction)
    {
        $this->authorize('update', $transaction);

        return view('accounting.transactions.edit', [
            'transaction' => $transaction,
        ]);
    }

    protected function exportAuthorize()
    {
        $this->authorize('viewAny', MoneyTransaction::class);
    }

    protected function exportView(): string
    {
        return 'accounting.transactions.export';
    }

    protected function exportViewArgs(): array
    {
        $filter = session('accounting.filter', []);
        return [
            'columnsSelection' => [
                'all' => __('app.all'),
                'webling' => __('accounting.selection_for_webling'),
            ],
            'columns' => 'all',
            'groupings' => [
                'none' => __('app.none'),
                'monthly' => __('app.monthly'),
            ],
            'grouping' => 'none',
            'selections' => ! empty($filter) ? [
                'all' => __('app.all_records'),
                'filtered' => __('app.selected_records_according_to_current_filter'),
            ] : null,
            'selection' => 'all',
        ];
    }

    protected function exportValidateArgs(): array
    {
        return [
            'columns' => [
                'required',
                Rule::in(['all', 'webling']),
            ],
            'grouping' => [
                'required',
                Rule::in(['none', 'monthly']),
            ],
            'selection' => [
                'nullable',
                Rule::in(['all', 'filtered']),
            ],
        ];
    }

    protected function exportFilename(Request $request): string
    {
        $wallet = resolve(CurrentWalletService::class)->get();
        return config('app.name') . ' ' . __('accounting.accounting') . ' [' . $wallet->name . '] (' . Carbon::now()->toDateString() . ')';
    }

    protected function exportExportable(Request $request)
    {
        $filter = $request->selection == 'filtered' ? session('accounting.filter', []) : [];
        if ($request->grouping == 'monthly') {
            return new MoneyTransactionsMonthsExport($filter);
        }
        if ($request->columns == 'webling') {
            return new WeblingMoneyTransactionsExport($filter);
        }
        return new MoneyTransactionsExport($filter);
    }
}
