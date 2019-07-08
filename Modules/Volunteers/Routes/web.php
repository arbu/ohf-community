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

Route::prefix('volunteers')->group(function() {
    Route::view('/', 'volunteers::index')->where('any', '.*');
    Route::view('/{any}', 'volunteers::index')->where('any', '.*');
});
