<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['language', 'auth']], function () {
    Route::group(['middleware' => ['can:do-bank-withdrawals']], function () {
        Route::get('bank/withdrawal/dailyStats', 'API\BankController@dailyStats')
            ->name('api.bank.withdrawal.dailyStats');
        Route::get('bank/withdrawal/search', 'API\BankController@search')
            ->name('api.bank.withdrawal.search');
        Route::post('bank/person/{person}/couponType/{couponType}/handout', 'API\BankController@handoutCoupon')
            ->name('bank.handoutCoupon');
        Route::delete('bank/person/{person}/couponType/{couponType}/handout', 'API\BankController@undoHandoutCoupon')
            ->name('bank.undoHandoutCoupon');
    });
});
