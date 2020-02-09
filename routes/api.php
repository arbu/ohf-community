<?php

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

//
// Fundraising
//

Route::middleware(['language', 'auth'])
    ->prefix('fundraising')
    ->name('api.fundraising.')
    ->namespace('Fundraising\API')
    ->group(function () {
        Route::apiResource('donors', 'DonorController');
    });

// RaiseNow Webhook
Route::middleware(['auth.basic', 'can:accept-fundraising-webhooks'])
    ->prefix('fundraising/donations')
    ->name('fundraising.')
    ->namespace('Fundraising\API')
    ->group(function () {
        Route::name('donations.raiseNowWebHookListener')->post('raiseNowWebHookListener', 'DonationController@raiseNowWebHookListener');
    });

//
// Accounting
//

Route::middleware(['language', 'auth'])
    ->prefix('accounting')
    ->name('accounting.')
    ->namespace('Accounting\API')
    ->group(function () {
        Route::post('transactions/{transaction}/receipt', 'MoneyTransactionsController@updateReceipt')
            ->name('transactions.updateReceipt');
    });

//
// Collaboration
//

Route::middleware('auth')
    ->namespace('Collaboration\API')
    ->prefix('calendar')
    ->name('calendar.')
    ->group(function () {
        Route::apiResource('events', 'CalendarEventController');
        Route::patch('events/{event}/date', 'CalendarEventController@updateDate')
            ->name('events.updateDate');
        Route::apiResource('resources', 'CalendarResourceController');
    });

Route::middleware('auth')
    ->namespace('Collaboration\API')
    ->group(function () {
        Route::apiResource('tasks', 'TasksController');
        Route::patch('tasks/{task}/done', 'TasksController@done')
            ->name('tasks.done');
    });

//
// People
//

Route::middleware(['auth', 'language'])
    ->namespace('People\API')
    ->name('api.people.')
    ->prefix('people')
    ->group(function () {

        // Get list of people
        Route::get('', 'PeopleController@index')
            ->name('index')
            ->middleware('can:list,App\Models\People\Person');

        // Filter persons
        Route::get('filterPersons', 'PeopleController@filterPersons')
            ->name('filterPersons')
            ->middleware('can:list,App\Models\People\Person');

        // Set gender
        Route::patch('{person}/gender', 'PeopleController@setGender')
            ->name('setGender')
            ->middleware('can:update,person');

        // Set date of birth
        Route::patch('{person}/date_of_birth', 'PeopleController@setDateOfBirth')
            ->name('setDateOfBirth')
            ->middleware('can:update,person');

        // Set nationality
        Route::patch('{person}/nationality', 'PeopleController@setNationality')
            ->name('setNationality')
            ->middleware('can:update,person');

        // Update remarks
        Route::patch('{person}/remarks', 'PeopleController@updateRemarks')
            ->name('updateRemarks')
            ->middleware('can:update,person');

        // Register code card
        Route::patch('{person}/card', 'PeopleController@registerCard')
            ->name('registerCard')
            ->middleware('can:update,person');

        // Reporting
        Route::prefix('reporting')
            ->name('reporting.')
            ->middleware(['can:view-people-reports'])
            ->group(function(){
                Route::get('nationalities', 'ReportingController@nationalities')
                    ->name('nationalities');
                Route::get('genderDistribution', 'ReportingController@genderDistribution')
                    ->name('genderDistribution');
                Route::get('ageDistribution', 'ReportingController@ageDistribution')
                    ->name('ageDistribution');
                Route::get('registrationsPerDay', 'ReportingController@registrationsPerDay')
                    ->name('registrationsPerDay');
            });
    });
