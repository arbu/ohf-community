<?php

namespace Modules\Accounting\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Modules\Accounting\Entities\MoneyTransaction;
use Modules\Accounting\Transformers\MoneyTransactionCollection;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;

use Carbon\Carbon;

class MoneyTransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('list', MoneyTransaction::class);

        $request->validate([
            'page' => [
                'nullable',
                'integer',
                'min:1'
            ],
            'size' => [
                'nullable',
                'integer',
                'min:0'
            ],
            'sortBy' => [
                'nullable',
                Rule::in([
                    'receipt_no',
                    'date',
                    'category',
                    'project',
                    'description',
                    'beneficiary',
                    'registered'
                ])
            ],
            'sortDesc' => [
                'nullable',
                'boolean'
            ]
        ]);

        // $request->validate([
        //     'date_start' => [
        //         'nullable',
        //         'date',
        //         'before_or_equal:' . Carbon::today(),
        //     ],
        //     'date_end' => [
        //         'nullable',
        //         'date',
        //         'before_or_equal:' . Carbon::today(),
        //     ],
        //     'type' => [
        //         'nullable',
        //         Rule::in(['income', 'spending']),
        //     ],
        //     'month' => 'nullable|regex:/[0-1]?[1-9]/',
        //     'year' => 'nullable|integer|min:2017|max:' . Carbon::today()->year,
        //     'sortColumn' => 'nullable|in:date,created_at,category,project,beneficiary,receipt_no',
        //     'sortOrder' => 'nullable|in:asc,desc',
        // ]);

        // if ($request->query('reset_filter') != null) {
        //     session(['accounting.filter' => []]);
        // }
        // $filter = session('accounting.filter', []);
        // foreach (Config::get('accounting.filter_columns') as $col) {
        //     if (!empty($request->filter[$col])) {
        //         $filter[$col] = $request->filter[$col];
        //     } else if (isset($request->filter)) {
        //         unset($filter[$col]);
        //     }
        // }
        // if (!empty($request->filter['date_start'])) {
        //     $filter['date_start'] = $request->filter['date_start'];
        // } else if (isset($request->filter)) {
        //     unset($filter['date_start']);
        // }
        // if (!empty($request->filter['date_end'])) {
        //     $filter['date_end'] = $request->filter['date_end'];
        // } else if (isset($request->filter)) {
        //     unset($filter['date_end']);
        // }
        // session(['accounting.filter' => $filter]);

        // $query = self::createIndexQuery($filter, $sortColumn, $sortOrder);

        $query = MoneyTransaction::query();

        // Ordering
        if ($request->filled('sortBy')) {
            $map = [
                'receipt_no' => 'receipt_no',
                'date' => 'date',
                'category' => 'category',
                'project' => 'project',
                'description' => 'description',
                'beneficiary' => 'beneficiary',
                'registered' => 'created_at',
            ];
            if ($request->filled('sortDesc') && $request->sortDesc) {
                $query->orderByDesc($map[$request->sortBy]);
            } else {
                $query->orderBy($map[$request->sortBy]);
            }
        }

        // Limiting / pagination
        $data = $request->filled('size') && $request->size > 0 ? $query->paginate($request->size) : $query->get();

        // Return results
        return new MoneyTransactionCollection($data);
    }

    private static function createIndexQuery($filter, $sortColumn, $sortOrder) {
        $query = MoneyTransaction::orderBy($sortColumn, $sortOrder)
            ->orderBy('created_at', 'DESC');
        self::applyFilterToQuery($filter, $query);
        return $query;
    }

    private static function applyFilterToQuery($filter, &$query, $skipDates = false) {
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

}
