<?php

namespace App\Http\Controllers\Shop;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CouponHandout;

class ShopController extends Controller
{
    public function index() {
        return view('shop.index');
    }

    public function searchCode(string $code) {
        // code_redeemed
        $handout = CouponHandout::where('code', $code)->orderBy('date', 'asc')->first();

        return view('shop.searchCode', [
            'handout' => $handout,
        ]);
    }
}