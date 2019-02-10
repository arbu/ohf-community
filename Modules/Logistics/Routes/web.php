<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('logistics')->group(function() {
    Route::get('/', 'LogisticsController@index');
});

Route::group(['middleware' => 'language'], function () {
    Route::middleware(['auth'])->prefix('logistics')->name('logistics.')->group(function () {
        
        // Suppliers
        Route::resource('suppliers', 'SupplierController');

        // Products
        Route::resource('products', 'ProductController')->only(['index', 'show']);

    });
});