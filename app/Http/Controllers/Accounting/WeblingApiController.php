<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Accounting\MoneyTransaction;
use App\Services\Accounting\CurrentWalletService;
use App\Support\Accounting\Webling\Entities\Entrygroup;
use App\Support\Accounting\Webling\Entities\Period;
use App\Support\Accounting\Webling\Exceptions\ConnectionException;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WeblingApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $this->authorize('book-accounting-transactions-externally');

        return view('accounting.webling.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function prepare(Request $request)
    {
        $this->authorize('book-accounting-transactions-externally');

        $this->validateRequest($request);

        return view('accounting.webling.prepare', [
            'period_id' => $request->period,
            'from' => $request->from,
            'to' => $request->to,
        ]);
    }

    private function validateRequest($request)
    {
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
    }

    public function store(Request $request)
    {
        $this->authorize('book-accounting-transactions-externally');

        $this->validateRequest($request);

        $period = Period::find($request->period);

        $preparedTransactions = collect($request->input('action', []))
            ->filter(fn ($v, $id) => $v == 'book'
                && ! empty($request->get('posting_text')[$id])
                && ! empty($request->get('debit_side')[$id])
                && ! empty($request->get('credit_side')[$id])
            )
            ->keys()
            ->map(fn ($id) => self::mapTransactionById($id, $request, $period))
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
                    return redirect()->back()
                        ->withInput()
                        ->with('error', $e->getMessage());
                }
            }

        return redirect()
            ->route('accounting.webling.index')
            ->with('info', __('accounting.num_transactions_booked', ['num' => count($bookedTransactions)]));
    }

    private static function mapTransactionById(int $id, Request $request, Period $period)
    {
        $transaction = MoneyTransaction::find($id);
        if ($transaction != null) {
            return [
                'transaction' => $transaction,
                'request' => [
                    'properties' => [
                        'date' => $transaction->date,
                        'title' => $request->get('posting_text')[$id],
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
                                        $request->get('credit_side')[$id],
                                    ],
                                    'debit' => [
                                        $request->get('debit_side')[$id],
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

    public function sync(Request $request, CurrentWalletService $currentWallet)
    {
        $this->authorize('book-accounting-transactions-externally');

        $request->validate([
            'period' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $period = Period::find($value);
                    if ($period == null) {
                        return $fail('Period does not exist.');
                    }
                },
            ],
        ]);

        $synced = 0;
        try {
            $period = Period::find($request->period);
            $entryGroups = $period->entryGroups();
            $entryGroups = collect($entryGroups)
                // ->slice(0, 3)
                ->map(function ($entryGroup) {
                    $entryGroup->entries = $entryGroup->entries();
                    return $entryGroup;
                });

            foreach ($entryGroups as $entryGroup) {
                foreach ($entryGroup->entries as $entry) {
                    $transaction = MoneyTransaction::query()
                        ->whereDate('date', $entryGroup->date)
                        ->forWallet($currentWallet->get())
                        ->notBooked()
                        ->where('receipt_no', $entry->receipt)
                        ->first();
                    if ($transaction != null) {
                        $transaction->booked = true;
                        $transaction->external_id = $entryGroup->id;
                        $transaction->save();
                        $synced++;
                    }
                }
            }

        } catch (ConnectionException $e) {
            session()->now('error', $e->getMessage());
            // $transactions = [];
        }

        return redirect()
            ->route('accounting.webling.index')
            ->with('info', __('accounting.num_transactions_synced', ['num' => $synced]));
    }
}
