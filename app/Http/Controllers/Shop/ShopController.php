<?php

namespace App\Http\Controllers\Shop;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CouponHandout;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\People\ShopCardResource;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    public function __construct() {
        ShopCardResource::withoutWrapping();
    }

    private static function getCouponValidity() {
        return \Setting::get('shop.coupon_valid_days', 1);
    }

    // Search card
    public function index(Request $request) {

        $code = $request->code;
        $handout = null;
        $expired = false;

        if ($code != null) {

            // code_redeemed
            $acceptDate = Carbon::today()->subDays(self::getCouponValidity() - 1);
            $handout = CouponHandout
                ::where(function($q) use ($code) {
                    return $q->where('code', $code)
                        ->orWhere('code', DB::raw("SHA2('". $code ."', 256)"));
                })
                ->whereDate('date', '>=', $acceptDate)
                ->orderBy('date', 'desc')
                ->first();
            if ($handout == null) {
                $handout = CouponHandout
                    ::where(function($q) use ($code) {
                        return $q->where('code', $code)
                            ->orWhere('code', DB::raw("SHA2('". $code ."', 256)"));
                    })
                    ->whereNull('code_redeemed')
                    ->orderBy('date', 'desc')
                    ->first();
                $expired = true;
            }

            Log::notice('Shop: Search code.', [
                'code' => $code,
                'handout' => $handout != null ? $handout->date : null,
            ]);
        }

        $redeemed_cards = CouponHandout
            ::whereDate('code_redeemed', Carbon::today())
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('shop.index', [
            'code' => $code,
            'handout' => $handout,
            'redeemed_cards' => $redeemed_cards,
            'expired' => $expired,
        ]);
    }

    // API: Search card
    public function searchCard(Request $request) {
        $code = $request->code;
        if ($code != null) {
            $handout = null;
            $expired = false;
   
            $acceptDate = Carbon::today()->subDays(self::getCouponValidity() - 1);
            $handout = CouponHandout
                ::where(function($q) use ($code) {
                    return $q->where('code', $code)
                        ->orWhere('code', DB::raw("SHA2('". $code ."', 256)"));
                })
                ->whereDate('date', '>=', $acceptDate)
                ->orderBy('date', 'desc')
                ->first();
            if ($handout == null) {
                $handout = CouponHandout
                    ::where(function($q) use ($code) {
                        return $q->where('code', $code)
                            ->orWhere('code', DB::raw("SHA2('". $code ."', 256)"));
                    })
                    ->whereNull('code_redeemed')
                    ->orderBy('date', 'desc')
                    ->first();
                $expired = true;
            }

            if ($handout != null) {
                return (new ShopCardResource($handout))->additional(['meta' => [
                    'key' => 'value',
                ]]);
            }
        }
        return response()->json([], 404);
    }

    // Redeem card
    public function redeem(Request $request) {

        // code_redeemed
        $acceptDate = Carbon::today()->subDays(self::getCouponValidity() - 1);
        $code = $request->code;
        $handout = CouponHandout
            ::where(function($q) use ($code) {
                return $q->where('code', $code)
                    ->orWhere('code', DB::raw("SHA2('". $code ."', 256)"));
            })
            ->whereDate('date', '>=', $acceptDate)
            ->orderBy('date', 'desc')
            ->first();
        if ($handout == null) {
            $handout = CouponHandout
                ::where(function($q) use ($code) {
                    return $q->where('code', $code)
                        ->orWhere('code', DB::raw("SHA2('". $code ."', 256)"));
                })            
                ->whereNull('code_redeemed')
                ->orderBy('date', 'desc')
                ->first();
        }

        if ($handout != null) {
            $redeemed = $handout->code_redeemed;
            if ($redeemed == null) {
                $handout->code_redeemed = Carbon::now();
                $handout->save();

                Log::notice('Shop: Redeem code.', [
                    'code' => $request->code,
                ]);

                return redirect()->route('shop.index')
                    ->with('success', __('shop.card_redeemed'));
            }
        }

        return redirect()->route('shop.index');
    }

    // API: Redeem card
    public function redeemCard() {
        // TODO
    }
}

