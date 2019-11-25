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
Route::group(['middleware' => 'language'], function () {
    Route::group(['middleware' => ['auth']], function () {
        Route::prefix('accounting')
            ->name('api.accounting.')
            ->namespace('API')
            ->group(function(){

                Route::get('transactions', 'MoneyTransactionsController@index')
                    ->name('transactions.index');

                Route::get('transactions/{transaction}/receipts', 'ReceiptsController@listReceipts')
                    ->name('transactions.listReceipts');
                Route::post('transactions/{transaction}/receipts', 'ReceiptsController@updateReceipts')
                    ->name('transactions.updateReceipts');
                Route::post('transactions/{transaction}/deleteReceipt', 'ReceiptsController@deleteReceipt')
                    ->name('transactions.deleteReceipt');

            });
    });
});