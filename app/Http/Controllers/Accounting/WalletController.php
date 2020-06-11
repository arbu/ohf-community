<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Accounting\MoneyTransaction;
use App\Models\Accounting\Wallet;

class WalletController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Wallet::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('accounting.wallets.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('accounting.wallets.create', );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Accounting\Wallet  $wallet
     * @return \Illuminate\Http\Response
     */
    public function edit(Wallet $wallet)
    {
        return view('accounting.wallets.edit', [
            'wallet' => $wallet,
        ]);
    }

    /**
     * List wallets so the user can change the default one in his session
     *
     * @return void
     */
    public function change()
    {
        $this->authorize('viewAny', MoneyTransaction::class);

        return view('accounting.wallets.change', [
            'wallets' => Wallet::orderBy('name')
                ->get()
                ->filter(fn ($wallet) => request()->user()->can('view', $wallet)),
        ]);
    }
}
