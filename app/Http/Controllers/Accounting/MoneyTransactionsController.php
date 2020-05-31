<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Accounting\MoneyTransaction;
use App\Models\Accounting\Wallet;
use App\Services\Accounting\CurrentWalletService;
use App\Services\Accounting\TransactionRepository;
use Illuminate\Http\Request;

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

        if ($request->has('wallet_id')) {
            $wallet = Wallet::find($request->input('wallet_id'));
            $this->authorize('view', $wallet);
            $currentWallet->set($wallet);
        }
        if ($currentWallet->get() === null) {
            return redirect()->route('accounting.wallets.change');
        }

        return view('accounting.transactions.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(CurrentWalletService $currentWallet)
    {
        $this->authorize('create', MoneyTransaction::class);

        $wallets = Wallet::orderBy('name')
            ->get()
            ->filter(fn ($wallet) => request()->user()->can('view', $wallet))
            ->map(fn ($wallet) => [
                'id' => $wallet->id,
                'name' => $wallet->name,
                'new_receipt_no' => $this->repository->getNextFreeReceiptNo($wallet),
            ]);
        if ($wallets->isEmpty()) {
            return redirect()->route('accounting.wallets.change');
        }
        $wallet = $currentWallet->get();

        return view('accounting.transactions.create', [
            'wallet' => $wallet,
            'wallets' => $wallets,
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
        $this->authorize('view', $transaction);

        return view('accounting.transactions.show', [
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
        $this->authorize('update', $transaction);

        return view('accounting.transactions.edit', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Display a form for setting export options.
     *
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
        $this->authorize('viewAny', MoneyTransaction::class);

        return view('accounting.transactions.export');
    }
}
