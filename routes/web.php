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

Route::middleware('language')->group(function () {

    Route::middleware('auth')->group(function () {

        // Home (Dashboard)
        Route::get('/', 'HomeController@index')->name('home');

        // Reporting
        Route::view('reporting', 'reporting.index')->name('reporting.index')->middleware('can:view-reports');

    });

    // Authentication
    Auth::routes();

    $socialite_drivers = config('auth.socialite.drivers');
    Route::get('login/{driver}/redirect', 'Auth\LoginController@redirectToProvider')
        ->name('login.provider')
        ->where('driver', implode('|', $socialite_drivers));
    Route::get('login/{driver}/callback', 'Auth\LoginController@handleProviderCallback')
        ->name('login.callback')
        ->where('driver', implode('|', $socialite_drivers));

    // Privacy policy
    Route::get('userPrivacyPolicy', 'PrivacyPolicy@userPolicy')->name('userPrivacyPolicy');

});

//
// User management
//
Route::middleware(['auth', 'language'])
    ->namespace('UserManagement')
    ->group(function () {

        // User management
        Route::prefix('admin')->group(function(){
            // Users
            Route::put('users/{user}/disable2FA', 'UserController@disable2FA')
                ->name('users.disable2FA');
            Route::put('users/{user}/disableOAuth', 'UserController@disableOAuth')
                ->name('users.disableOAuth');
            Route::resource('users', 'UserController');

            // Roles
            Route::get('roles/{role}/members', 'RoleController@manageMembers')
                ->name('roles.manageMembers');
            Route::put('roles/{role}/members', 'RoleController@updateMembers')
                ->name('roles.updateMembers');
            Route::resource('roles', 'RoleController');

            // Reporting
            Route::group(['middleware' => ['can:view-usermgmt-reports']], function () {
                Route::get('reporting/users/permissions', 'UserController@permissions')
                    ->name('users.permissions');
                Route::get('reporting/users/sensitiveData', 'UserController@sensitiveDataReport')
                    ->name('reporting.privacy');
                Route::get('reporting/roles/permissions', 'RoleController@permissions')
                    ->name('roles.permissions');
            });
        });

        // User profile
        Route::get('/userprofile', 'UserProfileController@index')
            ->name('userprofile');
        Route::post('/userprofile', 'UserProfileController@update')
            ->name('userprofile.update');
        Route::post('/userprofile/updatePassword', 'UserProfileController@updatePassword')
            ->name('userprofile.updatePassword');
        Route::delete('/userprofile', 'UserProfileController@delete')
            ->name('userprofile.delete');
        Route::get('/userprofile/2FA', 'UserProfileController@view2FA')
            ->name('userprofile.view2FA');
        Route::post('/userprofile/2FA', 'UserProfileController@store2FA')
            ->name('userprofile.store2FA');
        Route::delete('/userprofile/2FA', 'UserProfileController@disable2FA')
            ->name('userprofile.disable2FA');
    });

//
// Changelog
//

Route::middleware(['language', 'auth', 'can:view-changelogs'])
    ->namespace('Changelog')
    ->group(function () {
        Route::get('changelog', 'ChangelogController@index')
            ->name('changelog');
    });

//
// Logviewer
//
Route::middleware(['language', 'auth', 'can:view-logs'])
    ->namespace('Logviewer')
    ->group(function () {
        Route::get('logviewer', 'LogViewerController@index')
            ->name('logviewer.index');
    });

//
// Badges
//
Route::middleware(['language', 'auth'])
    ->namespace('Badges')
    ->name('badges.')
    ->prefix('badges')
    ->group(function () {
        Route::middleware(['can:create-badges'])
            ->group(function(){
                Route::get('/', 'BadgeMakerController@index')
                    ->name('index');
                Route::post('/selection', 'BadgeMakerController@selection')
                    ->name('selection');
                Route::post('/make', 'BadgeMakerController@make')
                    ->name('make');
            });
    });

//
// Fundraising
//

Route::middleware(['language', 'auth'])
    ->namespace('Fundraising')
    ->prefix('fundraising')
    ->name('fundraising.')
    ->group(function () {
        // Donors
        Route::name('donors.export')
            ->get('donors/export', 'DonorController@export');
        Route::name('donors.vcard')
            ->get('donors/{donor}/vcard', 'DonorController@vcard');
        Route::resource('donors', 'DonorController');

        // Donations
        Route::name('donations.index')
            ->get('donations', 'DonationController@index');
        Route::name('donations.import')
            ->get('donations/import', 'DonationController@import');
        Route::name('donations.export')
            ->get('donations/export', 'DonationController@export');
        Route::name('donations.doImport')
            ->post('donations/import', 'DonationController@doImport');
        Route::prefix('donors/{donor}')
            ->group(function () {
                Route::name('donations.exportDonor')
                    ->get('export', 'DonationController@exportDonor');
                Route::resource('donations', 'DonationController')
                    ->except('show', 'index');
            });
    });

//
// Accounting
//

Route::middleware(['language', 'auth'])
    ->namespace('Accounting')
    ->prefix('accounting')
    ->name('accounting.')
    ->group(function() {

        // Transactions
        Route::get('transactions/export', 'MoneyTransactionsController@export')
            ->name('transactions.export');
        Route::post('transactions/doExport', 'MoneyTransactionsController@doExport')
            ->name('transactions.doExport');
        Route::get('transactions/summary', 'MoneyTransactionsController@summary')
            ->name('transactions.summary');
        Route::get('transactions/{transaction}/snippet', 'MoneyTransactionsController@snippet')
            ->name('transactions.snippet');
        Route::put('transactions/{transaction}/undoBooking', 'MoneyTransactionsController@undoBooking')
            ->name('transactions.undoBooking');
        Route::resource('transactions', 'MoneyTransactionsController');

        // Webling
        Route::get('webling', 'WeblingApiController@index')
            ->name('webling.index');
        Route::get('webling/prepare', 'WeblingApiController@prepare')
            ->name('webling.prepare');
        Route::post('webling', 'WeblingApiController@store')
            ->name('webling.store');
        Route::get('webling/sync', 'WeblingApiController@sync')
            ->name('webling.sync');

        // Settings
        Route::get('settings', 'AccountingSettingsController@edit')
            ->name('settings.edit')
            ->middleware(['can:configure-accounting']);
        Route::put('settings', 'AccountingSettingsController@update')
            ->name('settings.update')
            ->middleware(['can:configure-accounting']);
    });

//
// Collaboration
//

Route::middleware(['language', 'auth'])
    ->namespace('Collaboration')
    ->group(function () {

        Route::view('calendar', 'collaboration.calendar')
            ->name('calendar')
            ->middleware('can:view-calendar');

        Route::view('tasks', 'collaboration.tasklist')
            ->name('tasks')
            ->middleware('can:list,App\Models\Collaboration\Task');

    });

Route::middleware(['language'])
    ->namespace('Collaboration')
    ->prefix('kb')
    ->name('kb.')
    ->group(function(){
        Route::group(['middleware' => ['auth']], function () {
            Route::get('', 'SearchController@index')
                ->name('index');
            Route::get('latest_changes', 'SearchController@latestChanges')
                ->name('latestChanges');

            Route::get('tags', 'TagController@tags')
                ->name('tags');
            Route::get('tags/{tag}/pdf', 'TagController@pdf')
                ->name('tags.pdf');

            Route::get('articles/{article}/pdf', 'ArticleController@pdf')
                ->name('articles.pdf');
            Route::resource('articles', 'ArticleController')
                ->except('show');
        });
        Route::get('tags/{tag}', 'TagController@tag')
            ->name('tag');
        Route::resource('articles', 'ArticleController')
            ->only('show');
    });

//
// People
//

Route::middleware(['auth', 'language'])
    ->namespace('People')
    ->group(function () {

        // People
        Route::get('/people/bulkSearch', 'PeopleController@bulkSearch')
            ->name('people.bulkSearch')
            ->middleware('can:list,App\Models\People\Person');
        Route::post('/people/bulkSearch', 'PeopleController@doBulkSearch')
            ->name('people.doBulkSearch')
            ->middleware('can:list,App\Models\People\Person');
        Route::get('/people/export', 'PeopleController@export')
            ->name('people.export')
            ->middleware('can:export,App\Models\People\Person');
        Route::get('/people/import', 'PeopleController@import')
            ->name('people.import')
            ->middleware('can:create,App\Models\People\Person');
        Route::post('/people/doImport', 'PeopleController@doImport')
            ->name('people.doImport')
            ->middleware('can:create,App\Models\People\Person');
        Route::get('/people/{person}/qrcode', 'PeopleController@qrCode')
            ->name('people.qrCode')
            ->middleware('can:view,person');
        Route::get('/people/{person}/relations', 'PeopleController@relations')
            ->name('people.relations');
        Route::post('/people/{person}/relations', 'PeopleController@addRelation')
            ->name('people.addRelation');
        Route::delete('/people/{person}/children/{child}', 'PeopleController@removeChild')
            ->name('people.removeChild');
        Route::delete('/people/{person}/partner', 'PeopleController@removePartner')
            ->name('people.removePartner');
        Route::delete('/people/{person}/mother', 'PeopleController@removeMother')
            ->name('people.removeMother');
        Route::delete('/people/{person}/father', 'PeopleController@removeFather')
            ->name('people.removeFather');
        Route::get('/people/duplicates', 'PeopleController@duplicates')
            ->name('people.duplicates');
        Route::post('/people/duplicates', 'PeopleController@applyDuplicates')
            ->name('people.applyDuplicates');
        Route::post('/people/bulkAction', 'PeopleController@bulkAction')
            ->name('people.bulkAction')
            ->middleware('can:manage-people');
        Route::resource('/people', 'PeopleController');

        // Reporting
        Route::namespace('Reporting')
            ->prefix('reporting')
            ->middleware(['can:view-people-reports'])
            ->group(function(){

                // Monthly summary report
                Route::get('monthly-summary', 'MonthlySummaryReportingController@index')
                    ->name('reporting.monthly-summary');

                // People report
                Route::get('people', 'PeopleReportingController@index')
                    ->name('reporting.people');
            });
    });
