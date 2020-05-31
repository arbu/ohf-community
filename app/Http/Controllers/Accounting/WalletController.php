<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Accounting\MoneyTransaction;
use App\Models\Accounting\Wallet;
use App\Services\Accounting\CurrentWalletService;

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
     * @param CurrentWalletService $currentWallet
     * @return void
     */
    public function change(CurrentWalletService $currentWallet)
    {
        $this->authorize('viewAny', MoneyTransaction::class);

        return view('accounting.wallets.change', [
            'wallets' => Wallet::orderBy('name')
                ->get()
                ->filter(fn ($wallet) => request()->user()->can('view', $wallet)),
            'active' => $currentWallet->get(),
        ]);
    }

    /**
     * Change the default wallet in the user session
     *
     * @param Wallet $wallet
     * @param CurrentWalletService $currentWallet
     * @return void
     */
    public function doChange(Wallet $wallet, CurrentWalletService $currentWallet)
    {
        $this->authorize('viewAny', MoneyTransaction::class);
        $this->authorize('view', $wallet);

        $change = optional($currentWallet->get())->id != $wallet->id;

        $currentWallet->set($wallet);

        $ret = redirect()
            ->route('accounting.transactions.index');
        if ($change) {
            return $ret->with('info', __('accounting.wallet_changed'));
        }
        return $ret;
    }
}
