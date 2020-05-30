<?php

namespace App\Http\Controllers\Accounting\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Accounting\StoreTransaction;
use App\Http\Resources\Accounting\MoneyTransaction as MoneyTransactionResource;
use App\Http\Resources\Accounting\Wallet as WalletResource;
use App\Models\Accounting\MoneyTransaction;
use App\Models\Accounting\Wallet;
use App\Services\Accounting\CurrentWalletService;
use App\Services\Accounting\TransactionRepository;
use App\Support\Accounting\Webling\Entities\Entrygroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Setting;

class MoneyTransactionsController extends Controller
{
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
    public function index(Request $request)
    {
        $this->authorize('viewAny', MoneyTransaction::class);

        $request->validate([
            'filter.date_start' => [
                'nullable',
                'date',
                'before_or_equal:' . Carbon::today(),
            ],
            'filter.date_end' => [
                'nullable',
                'date',
                'before_or_equal:' . Carbon::today(),
            ],
            'filter.type' => [
                'nullable',
                Rule::in(['income', 'spending']),
            ],
            // TODO
            // 'month' => [
            //     'nullable',
            //     'regex:/[0-1]?[1-9]/',
            // ],
            // 'year' => [
            //     'nullable',
            //     'integer',
            //     'min:2017',
            //     'max:' . Carbon::today()->year,
            // ],
            'sortBy' => [
                'nullable',
                Rule::in([
                    'date',
                    'created_at',
                    'category',
                    'secondary_category',
                    'project',
                    'location',
                    'cost_center',
                    'beneficiary',
                    'receipt_no',
                ]),
            ],
            'sortDirection' => [
                'nullable',
                'in:asc,desc',
            ],
            'wallet_id' => [
                'required',
                'exists:accounting_wallets,id',
            ],
        ]);

        $wallet = Wallet::findOrFail($request->input('wallet_id'));
        $this->authorize('view', $wallet);

        $filter = $request->input('filter', []);
        $sortColumn = $request->input('sortBy', 'created_at');
        $sortOrder = $request->input('sortDirection', 'desc');
        $pageSize = intval($request->input('pageSize', 50));
        $transactions = MoneyTransaction::query()
            ->forWallet($wallet)
            ->forFilter($filter)
            ->orderBy($sortColumn, $sortOrder)
            ->orderBy('created_at', 'desc')
            ->paginate($pageSize);

        return MoneyTransactionResource::collection($transactions)
            ->additional([
                'meta' => [
                    'sum_income' => round($transactions->where('type', 'income')->sum('amount'), 2),
                    'sum_spending' => round($transactions->where('type', 'spending')->sum('amount'), 2),
                ],
            ]);
    }

    public function currentWallet(CurrentWalletService $currentWallet)
    {
        $wallet = $currentWallet->get();
        return (new WalletResource($wallet))
            ->additional([
                'meta' => [
                    'has_multiple_wallets' => Wallet::count() > 1,
                    'use_secondary_categories' => $this->repository->useSecondaryCategories(),
                    'use_locations' => $this->repository->useLocations(),
                    'use_cost_centers' => $this->repository->useCostCenters(),
                ],
            ]);
    }

    public function filterClassifications()
    {
        return response()->json([
            'beneficiaries' => MoneyTransaction::beneficiaries(),
            'categories' => $this->repository->getCategories(true),
            'fixed_categories' => Setting::has('accounting.transactions.categories'),
            'secondary_categories' => $this->repository->useSecondaryCategories() ? $this->repository->getSecondaryCategories(true) : null,
            'fixed_secondary_categories' => Setting::has('accounting.transactions.secondary_categories'),
            'projects' => $this->repository->getProjects(true),
            'fixed_projects' => Setting::has('accounting.transactions.projects'),
            'locations' => $this->repository->useLocations() ? $this->repository->getLocations(true) : null,
            'fixed_locations' => Setting::has('accounting.transactions.locations'),
            'cost_centers' => $this->repository->useCostCenters() ? $this->repository->getCostCenters(true) : null,
            'fixed_cost_centers' => Setting::has('accounting.transactions.cost_centers'),
            'auth_user_name' => Auth::user()->name,
        ]);
    }

    public function updateReceipt(Request $request, MoneyTransaction $transaction)
    {
        $this->authorize('update', $transaction);

        $request->validate([
            'img' => [
                'file',
                'mimes:jpeg,png,gif,webp',
            ],
        ]);

        $transaction->deleteReceiptPictures(); // TODO no need to clear pictures for multi picture support
        $transaction->addReceiptPicture($request->file('img'));
        $transaction->save();

        return response([
            'message' => __('accounting.receipt_picture_added'),
            'receipt_pictures' => collect($transaction->receipt_pictures)->map(fn ($p) => Storage::url($p)),
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

        // TODO
        // if (isset($request->receipt_picture)) {
        //     $transaction->addReceiptPicture($request->file('receipt_picture'));
        // }

        $transaction->save();

        if ($transaction->wallet->id !== optional($currentWallet->get())->id) {
            $currentWallet->set($transaction->wallet);
        }

        return response()->json([
            'message' => __('accounting.transactions_registered'),
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

        return new MoneyTransactionResource($transaction);
    }

    public function undoBooking(MoneyTransaction $transaction)
    {
        $this->authorize('undoBooking', $transaction);

        if ($transaction->external_id != null && Entrygroup::find($transaction->external_id) != null) {
            return response()->json([
                'message' => __('accounting.transaction_not_updated_external_record_still_exists'),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $transaction->booked = false;
        $transaction->external_id = null;
        $transaction->save();

        return (new MoneyTransactionResource($transaction))
            ->additional([
                'message' => __('accounting.transactions_updated'),
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

        // TODO
        // if (isset($request->remove_receipt_picture)) {
        //     $transaction->deleteReceiptPictures();
        // }
        // elseif (isset($request->receipt_picture)) {
        //     $transaction->deleteReceiptPictures(); // TODO no need to clear pictures for multi picture support
        //     $transaction->addReceiptPicture($request->file('receipt_picture'));
        // }

        $transaction->save();

        return response()->json([
            'message' => __('accounting.transactions_updated'),
        ]);
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

        return response()->json([
            'message' => __('accounting.transactions_deleted'),
        ]);
    }
}
