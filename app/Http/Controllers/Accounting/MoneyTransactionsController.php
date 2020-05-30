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
            'beneficiaries' => MoneyTransaction::beneficiaries(),
            'categories' => $this->repository->getCategories(),
            'fixed_categories' => Setting::has('accounting.transactions.categories'),
            'secondary_categories' => $this->repository->useSecondaryCategories() ? $this->repository->getSecondaryCategories() : null,
            'fixed_secondary_categories' => Setting::has('accounting.transactions.secondary_categories'),
            'projects' => $this->repository->getProjects(),
            'fixed_projects' => Setting::has('accounting.transactions.projects'),
            'locations' => $this->repository->useLocations() ? $this->repository->getLocations() : null,
            'fixed_locations' => Setting::has('accounting.transactions.locations'),
            'cost_centers' => $this->repository->useCostCenters() ? $this->repository->getCostCenters() : null,
            'fixed_cost_centers' => Setting::has('accounting.transactions.cost_centers'),
            'wallet' => $wallet,
            'wallets' => $wallets,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Accounting\StoreTransaction  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTransaction $request, CurrentWalletService $currentWallet)
    {
        $this->authorize('create', MoneyTransaction::class);

        $transaction = new MoneyTransaction();
        $transaction->date = $request->date;
        $transaction->receipt_no = $request->receipt_no;
        $transaction->type = $request->type;
        $transaction->amount = $request->amount;
        $transaction->beneficiary = $request->beneficiary;
        $transaction->category = $request->category;
        if ($this->repository->useSecondaryCategories()) {
            $transaction->secondary_category = $request->secondary_category;
        }
        $transaction->project = $request->project;
        if ($this->repository->useLocations()) {
            $transaction->location = $request->location;
        }
        if ($this->repository->useCostCenters()) {
            $transaction->cost_center = $request->cost_center;
        }
        $transaction->description = $request->description;
        $transaction->remarks = $request->remarks;
        $transaction->wallet_owner = $request->wallet_owner;

        $transaction->wallet()->associate($request->input('wallet_id'));
        $this->authorize('view', $transaction->wallet);

        if (isset($request->receipt_picture)) {
            $transaction->addReceiptPicture($request->file('receipt_picture'));
        }

        $transaction->save();

        if ($transaction->wallet->id !== optional($currentWallet->get())->id) {
            $currentWallet->set($transaction->wallet);
        }

        return redirect()
            ->route($request->submit == 'save_and_continue' ? 'accounting.transactions.create' : 'accounting.transactions.index')
            ->with('info', __('accounting.transactions_registered'));
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
            'beneficiaries' => MoneyTransaction::beneficiaries(),
            'categories' => $this->repository->getCategories(),
            'fixed_categories' => Setting::has('accounting.transactions.categories'),
            'secondary_categories' => $this->repository->useSecondaryCategories() ? $this->repository->getSecondaryCategories() : null,
            'fixed_secondary_categories' => Setting::has('accounting.transactions.secondary_categories'),
            'projects' => $this->repository->getProjects(),
            'fixed_projects' => Setting::has('accounting.transactions.projects'),
            'locations' => $this->repository->useLocations() ? $this->repository->getLocations() : null,
            'fixed_locations' => Setting::has('accounting.transactions.locations'),
            'cost_centers' => $this->repository->useCostCenters() ? $this->repository->getCostCenters() : null,
            'fixed_cost_centers' => Setting::has('accounting.transactions.cost_centers'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Accounting\MoneyTransaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(StoreTransaction $request, MoneyTransaction $transaction)
    {
        $this->authorize('update', $transaction);

        $transaction->date = $request->date;
        $transaction->receipt_no = $request->receipt_no;
        $transaction->type = $request->type;
        $transaction->amount = $request->amount;
        $transaction->beneficiary = $request->beneficiary;
        $transaction->category = $request->category;
        if ($this->repository->useSecondaryCategories()) {
            $transaction->secondary_category = $request->secondary_category;
        }
        $transaction->project = $request->project;
        if ($this->repository->useLocations()) {
            $transaction->location = $request->location;
        }
        if ($this->repository->useCostCenters()) {
            $transaction->cost_center = $request->cost_center;
        }
        $transaction->description = $request->description;
        $transaction->remarks = $request->remarks;
        $transaction->wallet_owner = $request->wallet_owner;

        if (isset($request->remove_receipt_picture)) {
            $transaction->deleteReceiptPictures();
        }
        elseif (isset($request->receipt_picture)) {
            $transaction->deleteReceiptPictures(); // TODO no need to clear pictures for multi picture support
            $transaction->addReceiptPicture($request->file('receipt_picture'));
        }

        $transaction->save();

        return redirect()
            ->route('accounting.transactions.index')
            ->with('info', __('accounting.transactions_updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Accounting\MoneyTransaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(MoneyTransaction $transaction)
    {
        $this->authorize('delete', $transaction);

        $transaction->delete();

        return redirect()
            ->route('accounting.transactions.index')
            ->with('info', __('accounting.transactions_deleted'));
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
