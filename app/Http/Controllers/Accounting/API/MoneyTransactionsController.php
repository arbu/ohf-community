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
            'date_start' => [
                'nullable',
                'date',
                'before_or_equal:' . Carbon::today(),
            ],
            'date_end' => [
                'nullable',
                'date',
                'before_or_equal:' . Carbon::today(),
            ],
            'type' => [
                'nullable',
                Rule::in(['income', 'spending']),
            ],
            'month' => 'nullable|regex:/[0-1]?[1-9]/',
            'year' => 'nullable|integer|min:2017|max:' . Carbon::today()->year,
            'sortColumn' => 'nullable|in:date,created_at,category,secondary_category,project,location,cost_center,beneficiary,receipt_no',
            'sortOrder' => 'nullable|in:asc,desc',
            'wallet_id' => [
                'nullable',
                'exists:accounting_wallets,id',
            ],
        ]);

        if ($request->has('wallet_id')) {
            $wallet = Wallet::find($request->input('wallet_id'));
            $this->authorize('view', $wallet);
            $currentWallet->set($wallet);
        }

        $wallet = $currentWallet->get();
        if ($wallet === null) {
            return redirect()->route('accounting.wallets.change');
        }

        $sortColumns = [
            'date' => __('app.date'),
            'category' => __('app.category'),
            'secondary_category' => __('app.secondary_category'),
            'project' => __('app.project'),
            'location' => __('app.location'),
            'cost_center' => __('accounting.cost_center'),
            'beneficiary' => __('accounting.beneficiary'),
            'receipt_no' => __('accounting.receipt'),
            'created_at' => __('app.registered'),
        ];
        $sortColumn = session('accounting.sortColumn', 'created_at');
        $sortOrder = session('accounting.sortOrder', 'desc');
        if (isset($request->sortColumn)) {
            $sortColumn = $request->sortColumn;
            session(['accounting.sortColumn' => $sortColumn]);
        }
        if (isset($request->sortOrder)) {
            $sortOrder = $request->sortOrder;
            session(['accounting.sortOrder' => $sortOrder]);
        }

        if ($request->query('reset_filter') != null) {
            session(['accounting.filter' => []]);
        }
        $filter = session('accounting.filter', []);
        foreach (config('accounting.filter_columns') as $col) {
            if (! empty($request->filter[$col])) {
                $filter[$col] = $request->filter[$col];
            } elseif (isset($request->filter)) {
                unset($filter[$col]);
            }
        }
        if (! empty($request->filter['date_start'])) {
            $filter['date_start'] = $request->filter['date_start'];
        } elseif (isset($request->filter)) {
            unset($filter['date_start']);
        }
        if (! empty($request->filter['date_end'])) {
            $filter['date_end'] = $request->filter['date_end'];
        } elseif (isset($request->filter)) {
            unset($filter['date_end']);
        }
        session(['accounting.filter' => $filter]);

        $query = $this->repository->createIndexQuery($filter, $sortColumn, $sortOrder);

        // Get results
        $transactions = $query->paginate(250);

        // Single receipt no. query
        if ($transactions->count() == 1 && ! empty($filter['receipt_no'])) {
            session(['accounting.filter' => []]);
            return redirect()->route('accounting.transactions.show', $transactions->first());
        }

        return response()->json([
            'transactions' => [
                'data' => MoneyTransactionResource::collection($transactions)->resolve(),
            ],
            'filter' => $filter,
            'sortColumns' => $sortColumns,
            'sortColumn' => $sortColumn,
            'sortOrder' => $sortOrder,
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
            'wallet' => (new WalletResource($wallet))->resolve(),
            'has_multiple_wallets' => Wallet::count() > 1,
            'sum_income' => $transactions->where('type', 'income')->sum('amount'),
            'sum_spending' => $transactions->where('type', 'spending')->sum('amount'),
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
