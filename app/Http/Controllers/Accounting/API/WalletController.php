<?php

namespace App\Http\Controllers\Accounting\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Accounting\StoreWallet;
use App\Models\Accounting\Wallet;
use App\Role;
use App\Http\Resources\Accounting\Wallet as WalletResource;

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
        $wallets = Wallet::orderBy('name')
            ->with(['transactions', 'roles'])
            ->get();

        return WalletResource::collection($wallets);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreWallet $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWallet $request)
    {
        $wallet = new Wallet();
        $wallet->fill($request->all());
        $wallet->is_default = isset($request->is_default);
        $wallet->save();

        if ($request->user()->can('viewAny', Role::class)) {
            $wallet->roles()->sync($request->input('roles', []));
        }

        return response()->json([
            'message' => __('accounting.wallet_added'),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Accounting\Wallet  $wallet
     * @return \Illuminate\Http\Response
     */
    public function show(Wallet $wallet)
    {
        return new WalletResource($wallet);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreWallet $request
     * @param  \App\Models\Accounting\Wallet  $wallet
     * @return \Illuminate\Http\Response
     */
    public function update(StoreWallet $request, Wallet $wallet)
    {
        $wallet->fill($request->all());
        $wallet->is_default = isset($request->is_default);
        $wallet->save();

        if ($request->user()->can('viewAny', Role::class)) {
            $wallet->roles()->sync($request->input('roles', []));
        }

        return response()->json([
            'message' => __('accounting.wallet_updated'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Accounting\Wallet  $wallet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Wallet $wallet)
    {
        $wallet->delete();

        return response()->json([
            'message' => __('accounting.wallet_deleted'),
        ]);
    }
}
