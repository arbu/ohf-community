<?php

namespace Modules\Accounting\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Modules\Accounting\Entities\MoneyTransaction;
use Modules\Accounting\Transformers\MoneyTransactionCollection;

use Illuminate\Http\Request;
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
            ],
            'filter' => [
                'nullable',
                'array'
            ],
            'filter.*' => [
                'filled'
            ],
            'filter.date_start' => [
                'date',
                'before_or_equal:' . Carbon::today(),
            ],
            'filter.date_end' => [
                'date',
                'before_or_equal:' . Carbon::today(),
            ],
            'filter.type' => [
                Rule::in(['income', 'spending']),
            ],
            'filter.month' => [
                'integer',
                'between:1,12'
            ],
            'filter.year' => [
                'integer',
                'max:' . Carbon::today()->year
            ]
        ]);

        $query = MoneyTransaction::query();

        // Filtering
        self::applyFilter($request, $query);

        // Ordering
        self::applyOrdering($request, $query);

        // Limiting / pagination
        $data = $request->filled('size') && $request->size > 0 ? $query->paginate($request->size) : $query->get();

        // Return results
        return new MoneyTransactionCollection($data);
    }

    private static function applyFilter($request, $query)
    {
        if ($request->has('filter')) {
            if (!empty($request->filter['year']) && !empty($request->filter['month'])) {
                $dateFrom = (new Carbon($request->filter['year'].'-'.$request->filter['month'].'-01'))->startOfMonth();
                $dateTo = $dateFrom->clone()->endOfMonth();
                $query->whereDate('date', '>=', $dateFrom);
                $query->whereDate('date', '<=', $dateTo);
            } else if (!empty($request->filter['year'])) {
                $dateFrom = (new Carbon($request->filter['year'].'-01-01'))->startOfMonth();
                $dateTo = $dateFrom->clone()->endOfYear();
                $query->whereDate('date', '>=', $dateFrom);
                $query->whereDate('date', '<=', $dateTo);
            } else {
                if (!empty($request->filter['date'])) {
                    $query->where('date', $request->filter['date']);
                }
                if (!empty($request->filter['date_start'])) {
                    $query->whereDate('date', '>=', $request->filter['date_start']);
                }
                if (!empty($request->filter['date_end'])) {
                    $query->whereDate('date', '<=', $request->filter['date_end']);
                }
            }
            if (isset($request->filter['today'])) {
                $query->whereDate('created_at', Carbon::today());
            }
            if (isset($request->filter['no_receipt'])) {
                $query->where(function($query){
                    $query->whereNull('receipt_no');
                    $query->orWhereNull('receipt_pictures');
                    $query->orWhere('receipt_pictures', '[]');
                });
            }
            foreach (['receipt_no', 'type', 'category', 'project'] as $key) {
                if (isset($request->filter[$key])) {
                    $query->where($key, $request->filter[$key]);
                }
            }
            foreach (['beneficiary', 'description'] as $key) {
                if (isset($request->filter['receipt_no'])) {
                    $query->where($key, 'like', '%' . $request->filter[$key] . '%');
                }
            }
        }
    }

    private static function applyOrdering($request, $query)
    {
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
    }

}
