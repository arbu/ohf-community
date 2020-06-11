<?php

namespace App\Http\Controllers\Accounting\API;

use App\Exceptions\ConfigurationException;
use App\Http\Controllers\Controller;
use App\Models\Accounting\MoneyTransaction;
use App\Models\Accounting\Wallet;
use App\Support\Accounting\Webling\Entities\Entrygroup;
use App\Support\Accounting\Webling\Entities\Period;
use App\Support\Accounting\Webling\Exceptions\ConnectionException;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class WeblingApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Wallet $wallet)
    {
        $this->authorize('view', $wallet);
        $this->authorize('book-accounting-transactions-externally');

        setlocale(LC_TIME, \App::getLocale());

        try {
            $periods = Period::all()
                ->where('state', 'open')
                ->map(fn ($period) => [
                        'id' => $period->id,
                        'title' => $period->title,
                        'from' => $period->from,
                        'to' => $period->to,
                        'months' => self::getMonthsForPeriod($wallet, $period->from, $period->to),
                ])
                ->values();
        } catch (ConnectionException|ConfigurationException $e) {
            session()->now('error', $e->getMessage());
            $periods = collect();
        }
        return response()->json([
            'periods' => $periods,
        ]);
    }

    private static function getMonthsForPeriod(Wallet $wallet, $from, $to): Collection
    {
        $monthsWithTransactions = MoneyTransaction::query()
            ->forWallet($wallet)
            ->forDateRange($from, $to)
            ->notBooked()
            ->selectRaw('MONTH(date) as month')
            ->selectRaw('YEAR(date) as year')
            ->groupByRaw('MONTH(date)')
            ->groupByRaw('YEAR(date)')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        return $monthsWithTransactions->map(function ($e) use ($wallet) {
                $date = Carbon::createFromDate($e->year, $e->month, 1);
                return (object) [
                    'transactions' => MoneyTransaction::query()
                        ->forWallet($wallet)
                        ->forDateRange($date, $date->clone()->endOfMonth())
                        ->notBooked()
                        ->count(),
                    'date' => $date,
                ];
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function prepare(Wallet $wallet, Request $request)
    {
        $this->authorize('view', $wallet);
        $this->authorize('book-accounting-transactions-externally');

        $request->validate([
            'period' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $period = Period::find($value);
                    if ($period === null) {
                        return $fail('Period does not exist.');
                    }
                    if ($period->state != 'open') {
                        return $fail('Period \'' . $period->title . '\' is not open.');
                    }
                },
            ],
            'from' => [
                'required',
                'date',
            ],
            'to' => [
                'required',
                'date',
            ],
        ]);

        $period = Period::find($request->period);

        $transactions = MoneyTransaction::query()
            ->forWallet($wallet)
            ->forDateRange($request->from, $request->to)
            ->forDateRange($period->from, $period->to)
            ->notBooked()
            ->orderBy('date', 'asc')
            ->get();
        $hasTransactions = ! $transactions->isEmpty();
        if ($hasTransactions) {
            $accountGroups = $period->accountGroups();
        }

        return response()->json([
            'period' => $period,
            'transactions' => $transactions,
            'assetsSelect' => $hasTransactions ? $this->getAccountSelectArray($accountGroups, 'assets') : [],
            'incomeSelect' => $hasTransactions ? $this->getAccountSelectArray($accountGroups, 'income') : [],
            'expenseSelect' => $hasTransactions ? $this->getAccountSelectArray($accountGroups, 'expense') : [],
        ]);
    }

    private function getAccountSelectArray($accountGroups, $type)
    {
        return $accountGroups->where('type', $type)
            ->mapWithKeys(fn ($accountGroup) => [
                $accountGroup->title => $accountGroup->accounts()
                    ->mapWithKeys(fn ($account) => [ $account->id => $account->title ])
                    ->toArray(),
            ]);
    }

    public function store(Wallet $wallet, Request $request)
    {
        $this->authorize('view', $wallet);
        $this->authorize('book-accounting-transactions-externally');

        $request->validate([
            'period' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $period = Period::find($value);
                    if ($period === null) {
                        return $fail('Period does not exist.');
                    }
                    if ($period->state != 'open') {
                        return $fail('Period \'' . $period->title . '\' is not open.');
                    }
                },
            ],
        ]);

        $period = Period::find($request->period);

        $preparedTransactions = collect($request->json()->all())
            ->filter(fn ($data) => $data['action'] == 'book'
                && $data['id'] != null
                && ! empty($data['posting_text'])
                && ! empty($data['debit_side'])
                && ! empty($data['credit_side'])
            )
            ->map(fn ($data) => self::mapTransaction($data, $period))
            ->filter();

            $bookedTransactions = [];
            foreach ($preparedTransactions as $e) {
                try {
                    $entrygroup = Entrygroup::createRaw($e['request']);
                    $transaction = $e['transaction'];
                    $transaction->booked = true;
                    $transaction->external_id = $entrygroup->id;
                    $transaction->save();
                    $bookedTransactions[] = $transaction->id;
                } catch (Exception $e) {
                    Log::error($e);
                    return response()->json([
                        'message', $e->getMessage(),
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }

        return response()->json([
            'message' => __('accounting.num_transactions_booked', ['num' => count($bookedTransactions)]),
        ]);
    }

    private static function mapTransaction($data, Period $period)
    {
        $transaction = MoneyTransaction::find($data['id']);
        if ($transaction != null) {
            return [
                'transaction' => $transaction,
                'request' => [
                    'properties' => [
                        'date' => $transaction->date,
                        'title' => $data['posting_text'],
                    ],
                    'children' => [
                        'entry' => [
                            [
                                'properties' => [
                                    'amount' => $transaction->amount,
                                    'receipt' => $transaction->receipt_no,
                                ],
                                'links' => [
                                    'credit' => [
                                        $data['credit_side'],
                                    ],
                                    'debit' => [
                                        $data['debit_side'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'parents' => [
                        $period->id,
                    ],
                ],
            ];
        }
        return null;
    }
}
