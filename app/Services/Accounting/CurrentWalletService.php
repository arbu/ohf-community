<?php

namespace App\Services\Accounting;

use App\Models\Accounting\Wallet;

class CurrentWalletService
{
    private const SESSION_KEY = 'accounting.wallet.current';

    private ?Wallet $wallet = null;

    public function get(): Wallet
    {
        if ($this->wallet !== null) {
            return $this->wallet;
        }
        $key = session(self::SESSION_KEY);
        if ($key === null) {
            $wallet = $this->getOrCreateDefaultWallet();
            $this->set($wallet);
        } else {
            $wallet = Wallet::find($key);
            if ($wallet === null) {
                $wallet = $this->getOrCreateDefaultWallet();
            }
        }
        $this->wallet = $wallet;
        return $wallet;
    }

    private function getOrCreateDefaultWallet(): Wallet
    {
        $wallet = Wallet::where('is_default', true)->first();
        if ($wallet === null) {
            $wallet = Wallet::create([
                'name' => 'Default Wallet',
                'is_default' => true,
            ]);
        }
        return $wallet;
    }

    public function set(Wallet $wallet)
    {
        $this->wallet = $wallet;
        session([self::SESSION_KEY => $wallet->getKey()]);
    }
}
