<?php

namespace App\Http\Controllers\Accounting\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Accounting\MoneyTransaction as MoneyTransactionResource;
use App\Http\Resources\Accounting\Wallet as WalletResource;
use App\Models\Accounting\MoneyTransaction;
use App\Models\Accounting\Wallet;
use App\Services\Accounting\CurrentWalletService;
use App\Services\Accounting\TransactionRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
    public function index(Request $request, CurrentWalletService $currentWallet)
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
                'nullable',
                'exists:accounting_wallets,id',
            ],
        ]);

        $filter = $request->input('filter', []);
        $sortColumn = $request->input('sortBy', 'created_at');
        $sortOrder = $request->input('sortDirection', 'desc');
        $pageSize = intval($request->input('pageSize', 50));
        $wallet = $currentWallet->get();
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
        ]);
    }

    public function updateReceipt(Request $request, MoneyTransaction $transaction)
    {
        $this->authorize('update', $transaction);

        $transaction->deleteReceiptPictures(); // TODO no need to clear pictures for multi picture support
        $transaction->addReceiptPicture($request->file('img'));
        $transaction->save();

        return response([
            'message' => __('accounting.receipt_picture_added'),
            'receipt_pictures' => collect($transaction->receipt_pictures)->map(fn ($p) => Storage::url($p)),
        ]);
    }
}
