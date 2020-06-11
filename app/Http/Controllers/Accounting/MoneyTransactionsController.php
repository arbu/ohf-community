<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Accounting\MoneyTransaction;
use App\Models\Accounting\Wallet;

class MoneyTransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Wallet $wallet)
    {
        $this->authorize('view', $wallet);
        $this->authorize('viewAny', MoneyTransaction::class);

        return view('accounting.transactions.index', [
            'wallet' => $wallet,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Wallet $wallet)
    {
        $this->authorize('view', $wallet);
        $this->authorize('create', MoneyTransaction::class);

        return view('accounting.transactions.create', [
            'wallet' => $wallet,
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
        $this->authorize('view', $transaction->wallet);
        $this->authorize('view', $transaction);

        return view('accounting.transactions.show', [
            'wallet' => $transaction->wallet,
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
        $this->authorize('view', $transaction->wallet);
        $this->authorize('update', $transaction);

        return view('accounting.transactions.edit', [
            'wallet' => $transaction->wallet,
            'transaction' => $transaction,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function summary(Wallet $wallet)
    {
        $this->authorize('view', $wallet);
        $this->authorize('view-accounting-summary');

        return view('accounting.transactions.summary', [
            'wallet' => $wallet,
        ]);
    }

    /**
     * Display a form for setting export options.
     *
     * @return \Illuminate\Http\Response
     */
    public function export(Wallet $wallet)
    {
        $this->authorize('view', $wallet);
        $this->authorize('viewAny', MoneyTransaction::class);

        return view('accounting.transactions.export', [
            'wallet' => $wallet,
        ]);
    }
}
