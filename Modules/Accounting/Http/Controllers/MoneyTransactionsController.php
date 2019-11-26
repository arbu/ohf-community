<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Export\ExportableActions;

use Modules\Accounting\Http\Requests\StoreTransaction;
use Modules\Accounting\Entities\MoneyTransaction;
use Modules\Accounting\Exports\MoneyTransactionsExport;
use Modules\Accounting\Exports\WeblingMoneyTransactionsExport;
use Modules\Accounting\Exports\MoneyTransactionsMonthsExport;
use Modules\Accounting\Support\Webling\Entities\Entrygroup;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;

use Carbon\Carbon;

use Setting;

class MoneyTransactionsController extends Controller
{
    use ExportableActions;

    const CATEGORIES_SETTING_KEY = 'accounting.transactions.categories';
    const PROJECTS_SETTING_KEY = 'accounting.transactions.projects';

    public function __construct()
    {
        Carbon::setUtf8(true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('list', MoneyTransaction::class);

        return view('accounting::transactions.index', [
            'beneficiaries' => self::getBeneficiaries(),
            'categories' => self::getCategories(true),
            'fixed_categories' => Setting::has(self::CATEGORIES_SETTING_KEY),
            'projects' => self::getProjects(true),
            'fixed_projects' => Setting::has(self::PROJECTS_SETTING_KEY),
        ]);
    }

    // private static function createIndexQuery($filter, $sortColumn, $sortOrder) {
    //     $query = MoneyTransaction::orderBy($sortColumn, $sortOrder)
    //         ->orderBy('created_at', 'DESC');
    //     self::applyFilterToQuery($filter, $query);
    //     return $query;
    // }

    public static function applyFilterToQuery($filter, &$query, $skipDates = false) {
        foreach (Config::get('accounting.filter_columns') as $col) {
            if (!empty($filter[$col])) {
                if ($col == 'today') {
                    $query->whereDate('created_at', Carbon::today());
                } else if ($col == 'no_receipt') {
                    $query->where(function($query){
                        $query->whereNull('receipt_no');
                        $query->orWhereNull('receipt_pictures');
                        $query->orWhere('receipt_pictures', '[]');
                    });
                } else if ($col == 'beneficiary' || $col == 'description') {
                    $query->where($col, 'like', '%' . $filter[$col] . '%');
                } else {
                    $query->where($col, $filter[$col]);
                }
            }
        }
        if (!$skipDates) {
            if (!empty($filter['date_start'])) {
                $query->whereDate('date', '>=', $filter['date_start']);
            }
            if (!empty($filter['date_end'])) {
                $query->whereDate('date', '<=', $filter['date_end']);
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', MoneyTransaction::class);

        return view('accounting::transactions.create', [
            'beneficiaries' => self::getBeneficiaries(),
            'categories' => self::getCategories(),
            'fixed_categories' => Setting::has(self::CATEGORIES_SETTING_KEY),
            'projects' => self::getProjects(),
            'fixed_projects' => Setting::has(self::PROJECTS_SETTING_KEY),
            'newReceiptNo' => MoneyTransaction::getNextFreeReceiptNo(),
        ]);
    }

    private static function getBeneficiaries() {
        return MoneyTransaction::select('beneficiary')
            ->groupBy('beneficiary')
            ->orderBy('beneficiary')
            ->get()
            ->pluck('beneficiary')
            ->unique()
            ->toArray();
    }

    private static function getCategories($onlyExisting = false) {
        if (!$onlyExisting && Setting::has(self::CATEGORIES_SETTING_KEY)) {
            return collect(Setting::get(self::CATEGORIES_SETTING_KEY))
                ->mapWithKeys(function($e){
                    return [ $e => $e ];
                })
                ->sort()
                ->toArray();
        }
        return MoneyTransaction::select('category')
            ->groupBy('category')
            ->orderBy('category')
            ->get()
            ->pluck('category', 'category')
            ->unique()
            ->toArray();
    }

    private static function getProjects($onlyExisting = false) {
        if (!$onlyExisting && Setting::has(self::PROJECTS_SETTING_KEY)) {
            return collect(Setting::get(self::PROJECTS_SETTING_KEY))
                ->mapWithKeys(function($e){
                    return [ $e => $e ];
                })
                ->sort()
                ->toArray();
        }
        return MoneyTransaction::select('project')
            ->where('project', '!=', null)
            ->groupBy('project')
            ->orderBy('project')
            ->get()
            ->pluck('project', 'project')
            ->unique()
            ->toArray();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Modules\Accounting\Http\Requests\StoreTransaction  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTransaction $request)
    {
        $this->authorize('create', MoneyTransaction::class);

        $transaction = new MoneyTransaction();
        $transaction->fill($request->all());
        $transaction->save();

        return redirect()
            ->route($request->submit == 'save_and_continue' ? 'accounting.transactions.create' : 'accounting.transactions.index')
            ->with('info', __('accounting::accounting.transactions_registered'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Modules\Accounting\Entities\MoneyTransaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(MoneyTransaction $transaction)
    {
        $this->authorize('view', $transaction);

        // $sortColumn = session('accounting.sortColumn', 'created_at');
        // $sortOrder = session('accounting.sortOrder', 'desc');
        // $filter = session('accounting.filter', []);
        // $query = self::createIndexQuery($filter, $sortColumn, $sortOrder);
        // // TODO: can this be optimized, e.g. with a cursor??
        // $res = $query->select('id')->get()->pluck('id')->toArray();
        // $prev_id = null;
        // $next_id = null;
        // $cnt = count($res);
        // for ($i = 0; $i < $cnt; $i++) {
        //     $prev_id = $i > 0 ? $res[$i - 1] : null;
        //     $next_id = $i < $cnt - 1 ? $res[$i + 1] : null;
        //     if ($res[$i] == $transaction->id) {
        //         break;
        //     }
        // }

        return view('accounting::transactions.show', [
            'transaction' => $transaction,
            // 'prev_id' => $prev_id,
            // 'next_id' => $next_id,
        ]);
    }

    public function snippet(MoneyTransaction $transaction)
    {
        $this->authorize('view', $transaction);

        return view('accounting::transactions.snippet', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Modules\Accounting\Entities\MoneyTransaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(MoneyTransaction $transaction)
    {
        $this->authorize('update', $transaction);

        return view('accounting::transactions.edit', [
            'transaction' => $transaction,
            'beneficiaries' => self::getBeneficiaries(),
            'categories' => self::getCategories(),
            'fixed_categories' => Setting::has(self::CATEGORIES_SETTING_KEY),
            'projects' => self::getProjects(),
            'fixed_projects' => Setting::has(self::PROJECTS_SETTING_KEY),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Modules\Accounting\Entities\MoneyTransaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(StoreTransaction $request, MoneyTransaction $transaction)
    {
        $this->authorize('update', $transaction);

        $transaction->fill($request->all());
        $transaction->save();

        return redirect()
            ->route('accounting.transactions.index')
            ->with('info', __('accounting::accounting.transactions_updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Modules\Accounting\Entities\MoneyTransaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(MoneyTransaction $transaction)
    {
        $this->authorize('delete', $transaction);

        $transaction->delete();

        return redirect()
            ->route('accounting.transactions.index')
            ->with('info', __('accounting::accounting.transactions_deleted'));
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function summary(Request $request)
    {
        $this->authorize('view-accounting-summary');

        $currentMonth = Carbon::now()->startOfMonth();

        $validatedData = $request->validate([
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2000|max:' . Carbon::today()->year,
        ]);

        if ($request->filled('year') && $request->filled('month')) {
            $year = $request->year;
            $month = $request->month;
        } else if ($request->filled('year')) {
            $year = $request->year;
            $month = null;
        } else if ($request->has('year')) {
            $year = null;
            $month = null;
        } else if ($request->session()->has('accounting.summary_range.year') && $request->session()->has('accounting.summary_range.month')) {
            $year = $request->session()->get('accounting.summary_range.year');
            $month = $request->session()->get('accounting.summary_range.month');
        } else if ($request->session()->has('accounting.summary_range.year')) {
            $year = $request->session()->get('accounting.summary_range.year');
            $month = null;
        } else if ($request->session()->exists('accounting.summary_range.year')) {
            $year = null;
            $month = null;
        } else {
            $year = $currentMonth->year;
            $month = $currentMonth->month;
        }
        session([
            'accounting.summary_range.year' => $year,
            'accounting.summary_range.month' => $month,
        ]);

        if ($year != null && $month != null) {
            $dateFrom = (new Carbon($year.'-'.$month.'-01'))->startOfMonth();
            $dateTo = (clone $dateFrom)->endOfMonth();
            $heading = $dateFrom->formatLocalized('%B %Y');
            $currentRange = $dateFrom->format('Y-m');
        } else if ($year != null) {
            $dateFrom = (new Carbon($year.'-01-01'))->startOfYear();
            $dateTo = (clone $dateFrom)->endOfYear();
            $heading = $year;
            $currentRange = $year;
        } else {
            $dateFrom = null;
            $dateTo = null;
            $heading = __('app.all_time');
            $currentRange = null;
        }

        // TODO: Probably define on more general location
        setlocale(LC_TIME, \App::getLocale());

        $revenueByCategory = MoneyTransaction::revenueByField('category', $dateFrom, $dateTo);
        $revenueByProject = MoneyTransaction::revenueByField('project', $dateFrom, $dateTo);
        $wallet = MoneyTransaction::currentWallet($dateTo);

        $spending = MoneyTransaction::totalSpending($dateFrom, $dateTo);
        $income = MoneyTransaction::totalIncome($dateFrom, $dateTo);

        $months = MoneyTransaction::selectRaw('MONTH(date) as month')
            ->selectRaw('YEAR(date) as year')
            ->groupBy(DB::raw('MONTH(date)'))
            ->groupBy(DB::raw('YEAR(date)'))
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->mapWithKeys(function($e){
                $date = new Carbon($e->year.'-'.$e->month.'-01');
                return [ $date->format('Y-m') => $date->formatLocalized('%B %Y') ];
            })
            ->prepend($currentMonth->formatLocalized('%B %Y'), $currentMonth->format('Y-m'))
            ->toArray();

        $years = MoneyTransaction::selectRaw('YEAR(date) as year')
            ->groupBy(DB::raw('YEAR(date)'))
            ->orderBy('year', 'desc')
            ->get()
            ->mapWithKeys(function($e){
                return [ $e->year => $e->year ];
            })
            ->prepend($currentMonth->format('Y'), $currentMonth->format('Y'))
            ->toArray();

        return view('accounting::transactions.summary', [
            'heading' => $heading,
            'currentRange' => $currentRange,
            'months' => $months,
            'years' => $years,
            'revenueByCategory' => $revenueByCategory,
            'revenueByProject' => $revenueByProject,
            'wallet' => $wallet,
            'spending' => $spending,
            'income' => $income,
            'filterDateStart' => optional($dateFrom)->toDateString(),
            'filterDateEnd' => optional($dateTo)->toDateString(),
        ]);
    }

    function exportAuthorize()
    {
        $this->authorize('list', MoneyTransaction::class);
    }

    function exportView(): string
    {
        return 'accounting::transactions.export';
    }

    function exportViewArgs(): array
    {
        $filter = session('accounting.filter', []);
        return [
            'columnsSelection' => [
                'all' => __('app.all'),
                'webling' => __('accounting::accounting.selection_for_webling'),
            ],
            'columns' => 'all',
            'groupings' => [
                'none' => __('app.none'),
                'monthly' => __('app.monthly'),
            ],
            'grouping' => 'none',
            'selections' => !empty($filter) ? [
                'all' => __('app.all_records'),
                'filtered' => __('app.selected_records_according_to_current_filter')
            ] : null,
            'selection' => 'all',
        ];
    }

    function exportValidateArgs(): array
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

    function exportFilename(Request $request): string
    {
        return Config::get('app.name') . ' ' . __('accounting::accounting.accounting') . ' (' . Carbon::now()->toDateString() . ')';
    }

    function exportExportable(Request $request)
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

    public function undoBooking(MoneyTransaction $transaction)
    {
        $this->authorize('undoBooking', $transaction);

        if ($transaction->external_id != null && Entrygroup::find($transaction->external_id) != null) {
            return redirect()
                ->route('accounting.transactions.show', $transaction)
                ->with('error', __('accounting::accounting.transaction_not_updated_external_record_still_exists'));
        }

        $transaction->booked = false;
        $transaction->external_id = null;
        $transaction->save();

        return redirect()
            ->route('accounting.transactions.show', $transaction)
            ->with('info', __('accounting::accounting.transactions_updated'));
    }
}
