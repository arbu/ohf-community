<?php

namespace App\Http\Controllers\Accounting\API;

use App\Http\Controllers\Controller;
use App\Models\Accounting\MoneyTransaction;
use App\Models\Accounting\SignedMoneyTransaction;
use App\Models\Accounting\Wallet;
use App\Services\Accounting\CurrentWalletService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SummaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function summary(Request $request, CurrentWalletService $currentWallet)
    {
        $this->authorize('view-accounting-summary');

        setlocale(LC_TIME, \App::getLocale());

        $currentMonth = Carbon::now()->startOfMonth();

        $request->validate([
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2000|max:' . Carbon::today()->year,
        ]);

        $wallet = $currentWallet->get();
        if ($wallet === null) {
            return redirect()->route('accounting.wallets.change');
        }

        if ($request->filled('year') && $request->filled('month')) {
            $year = $request->year;
            $month = $request->month;
        } elseif ($request->filled('year')) {
            $year = $request->year;
            $month = null;
        } elseif ($request->has('year')) {
            $year = null;
            $month = null;
        } else {
            $year = $currentMonth->year;
            $month = $currentMonth->month;
        }

        $currentYearMonth = null;
        $currentYear = null;
        if ($year != null && $month != null) {
            $dateFrom = (new Carbon($year.'-'.$month.'-01'))->startOfMonth();
            $dateTo = (clone $dateFrom)->endOfMonth();
            $currentYearMonth = $dateFrom->format('Y-m');
        } elseif ($year != null) {
            $dateFrom = (new Carbon($year.'-01-01'))->startOfYear();
            $dateTo = (clone $dateFrom)->endOfYear();
            $currentYear = $year;
        } else {
            $dateFrom = null;
            $dateTo = null;
        }

        $revenueByCategory = self::revenueByField('category', $wallet, $dateFrom, $dateTo);
        $revenueByProject = self::revenueByField('project', $wallet, $dateFrom, $dateTo);

        $spending = self::totalByType('spending', $wallet, $dateFrom, $dateTo);
        $income = self::totalByType('income', $wallet, $dateFrom, $dateTo);

        $months = MoneyTransaction::query()
            ->selectRaw('MONTH(date) as month')
            ->selectRaw('YEAR(date) as year')
            ->forWallet($wallet)
            ->groupByRaw('MONTH(date)')
            ->groupByRaw('YEAR(date)')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(fn ($e) => (new Carbon($e->year.'-'.$e->month.'-01'))->format('Y-m'))
            ->prepend($currentMonth->format('Y-m'))
            ->unique()
            ->values()
            ->toArray();

        $years = MoneyTransaction::query()
            ->selectRaw('YEAR(date) as year')
            ->forWallet($wallet)
            ->groupByRaw('YEAR(date)')
            ->orderBy('year', 'desc')
            ->get()
            ->pluck('year')
            ->prepend($currentMonth->year)
            ->unique()
            ->values()
            ->toArray();

        return response()->json([
            'current_year_month' => $currentYearMonth,
            'current_year' => $currentYear,
            'months' => $months,
            'years' => $years,
            'revenue_by_category' => $revenueByCategory,
            'revenue_by_project' => $revenueByProject,
            'wallet_amount' => $wallet->calculatedSum($dateTo),
            'spending' => $spending,
            'income' => $income,
            'wallet' => $wallet,
            'has_multiple_wallets' => Wallet::count() > 1,
            'can_view_transactions' => $request->user()->can('viewAny', MoneyTransaction::class),
        ]);
    }

    private static function revenueByField(string $field, Wallet $wallet, ?Carbon $dateFrom = null, ?Carbon $dateTo = null): Collection
    {
        return SignedMoneyTransaction::query()
            ->select($field)
            ->selectRaw('SUM(amount) as sum')
            ->forWallet($wallet)
            ->forDateRange($dateFrom, $dateTo)
            ->groupBy($field)
            ->orderBy($field)
            ->get()
            ->map(fn ($e) => [
                'name' => $e->$field,
                'amount' => $e->sum,
            ]);
    }

    private static function totalByType(string $type, Wallet $wallet, ?Carbon $dateFrom = null, ?Carbon $dateTo = null): ?float
    {
        $result = MoneyTransaction::query()
            ->selectRaw('SUM(amount) as sum')
            ->forWallet($wallet)
            ->forDateRange($dateFrom, $dateTo)
            ->where('type', $type)
            ->first();

        return optional($result)->sum;
    }

}
