<?php

use Illuminate\Support\Facades\Route;

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
        Route::get('/', 'HomeController@index')
            ->name('home');

        // Reporting
        Route::view('reporting', 'reporting.index')
            ->name('reporting.index')
            ->middleware('can:view-reports');
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
    Route::get('userPrivacyPolicy', 'PrivacyPolicy@userPolicy')
        ->name('userPrivacyPolicy');

    // Settings
    Route::get('settings', 'Settings\SettingsController@edit')
        ->name('settings.edit');
    Route::put('settings', 'Settings\SettingsController@update')
        ->name('settings.update');
});

//
// User management
//
Route::middleware(['auth', 'language'])
    ->namespace('UserManagement')
    ->group(function () {

        // User management
        Route::prefix('admin')
            ->group(function () {
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
// Badges
//
Route::middleware(['language', 'auth'])
    ->namespace('Badges')
    ->name('badges.')
    ->prefix('badges')
    ->group(function () {
        Route::middleware(['can:create-badges'])
            ->group(function () {
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
        // SPA
        Route::get('', 'FundraisingController@index')
            ->name('index');
        Route::get('/{any}', 'FundraisingController@index')
            ->where('any', '.*');
    });

//
// Accounting
//

Route::middleware(['language', 'auth'])
    ->namespace('Accounting')
    ->prefix('accounting')
    ->name('accounting.')
    ->group(function () {

        // Transactions
        Route::get('transactions/export', 'MoneyTransactionsController@export')
            ->name('transactions.export');
        Route::post('transactions/doExport', 'MoneyTransactionsController@doExport')
            ->name('transactions.doExport');
        Route::get('transactions/summary', 'SummaryController@summary')
            ->name('transactions.summary');
        Route::get('transactions/summary/all', 'GlobalSummaryController@summary')
            ->name('transactions.globalSummary');
        Route::get('transactions/{transaction}/snippet', 'MoneyTransactionsController@snippet')
            ->name('transactions.snippet');
        Route::put('transactions/{transaction}/undoBooking', 'MoneyTransactionsController@undoBooking')
            ->name('transactions.undoBooking');
        Route::get('transactions/{transaction}/print', 'MoneyTransactionsController@print')
            ->name('transactions.print');

        Route::get('transactions/import', 'TransactionsImportController@import')
            ->name('transactions.import')
            ->middleware('can:import,App\Models\Accounting\MoneyTransaction');
        Route::post('transactions/import', 'TransactionsImportController@doImport')
            ->name('transactions.doImport')
            ->middleware('can:import,App\Models\Accounting\MoneyTransaction');
        Route::get('transactions/print', 'MoneyTransactionsController@printMultiple')
            ->name('transactions.printMultiple');
        Route::post('transactions/print', 'MoneyTransactionsController@doPrintMultiple')
            ->name('transactions.doPrintMultiple');

        Route::resource('transactions', 'MoneyTransactionsController');

        Route::get('wallets/change', 'WalletController@change')
            ->name('wallets.change');
        Route::get('wallets/change/{wallet}', 'WalletController@doChange')
            ->name('wallets.doChange');
        Route::resource('wallets', 'WalletController');

        // Webling
        Route::get('webling', 'WeblingApiController@index')
            ->name('webling.index');
        Route::get('webling/prepare', 'WeblingApiController@prepare')
            ->name('webling.prepare');
        Route::post('webling', 'WeblingApiController@store')
            ->name('webling.store');
        Route::get('webling/sync', 'WeblingApiController@sync')
            ->name('webling.sync');
    });

//
// Collaboration
//

Route::middleware(['language'])
    ->namespace('Collaboration')
    ->prefix('kb')
    ->name('kb.')
    ->group(function () {
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
            ->middleware('can:viewAny,App\Models\People\Person');
        Route::post('/people/bulkSearch', 'PeopleController@doBulkSearch')
            ->name('people.doBulkSearch')
            ->middleware('can:viewAny,App\Models\People\Person');
        Route::get('/people/export', 'PeopleController@export')
            ->name('people.export')
            ->middleware('can:export,App\Models\People\Person');
        Route::get('/people/import', 'PeopleController@import')
            ->name('people.import')
            ->middleware('can:create,App\Models\People\Person');
        Route::post('/people/doImport', 'PeopleController@doImport')
            ->name('people.doImport')
            ->middleware('can:create,App\Models\People\Person');
        Route::get('/people/duplicates', 'PeopleController@duplicates')
            ->name('people.duplicates');
        Route::post('/people/duplicates', 'PeopleController@applyDuplicates')
            ->name('people.applyDuplicates');
        Route::resource('/people', 'PeopleController');

        // Reporting
        Route::namespace('Reporting')
            ->prefix('reporting')
            ->middleware(['can:view-people-reports'])
            ->group(function () {

                // Monthly summary report
                Route::view('monthly-summary', 'people.reporting.monthly-summary')
                    ->name('reporting.monthly-summary');

                // People report
                Route::view('people', 'people.reporting.people')
                    ->name('reporting.people');
            });
    });

//
// Bank
//

Route::middleware(['auth', 'language'])
    ->namespace('Bank')
    ->group(function () {

        Route::prefix('bank')
            ->name('bank.')
            ->group(function () {

                // Withdrawals
                Route::middleware('can:do-bank-withdrawals')
                    ->group(function () {

                        Route::redirect('', 'bank/withdrawal')->name('index');

                        Route::view('withdrawal', 'bank.withdrawal.search')
                            ->name('withdrawal.search');

                        Route::view('withdrawal/transactions', 'bank.withdrawal.transactions')
                            ->name('withdrawal.transactions')
                            ->middleware('can:viewAny,App\Models\People\Person');

                        Route::get('codeCard', 'CodeCardController@create')
                            ->name('prepareCodeCard');

                        Route::post('codeCard', 'CodeCardController@download')
                            ->name('createCodeCard');
                    });

                // People
                Route::resource('people', 'PeopleController')
                    ->except(['index', 'store', 'update']);

                // Maintenance
                Route::middleware('can:cleanup,App\Models\People\Person')
                    ->group(function () {
                        Route::get('maintenance', 'MaintenanceController@maintenance')
                            ->name('maintenance');
                        Route::post('maintenance', 'MaintenanceController@updateMaintenance')
                            ->name('updateMaintenance');
                    });

                // Export
                Route::middleware('can:export,App\Models\People\Person')
                    ->group(function () {
                        Route::get('export', 'ImportExportController@export')
                            ->name('export');
                        Route::post('doExport', 'ImportExportController@doExport')
                            ->name('doExport');
                    });
            });

        // Coupons
        Route::middleware('can:configure-bank')
            ->prefix('bank')
            ->group(function () {
                Route::resource('coupons', 'CouponTypesController');
            });

        // Reporting
        Route::middleware('can:view-bank-reports')
            ->namespace('Reporting')
            ->name('reporting.bank.')
            ->prefix('reporting/bank')
            ->group(function () {
                Route::view('withdrawals', 'bank.reporting.withdrawals')
                    ->name('withdrawals');
                Route::view('visitors', 'bank.reporting.visitors')
                    ->name('visitors');
            });
    });

//
// Community volunteers
//

Route::middleware(['auth', 'language'])
    ->namespace('CommunityVolunteers')
    ->group(function () {
        Route::redirect('helpers', 'cmtyvol');
        Route::name('cmtyvol.')
            ->prefix('cmtyvol')
            ->group(function () {

                // Overview
                Route::view('overview', 'cmtyvol.overview')
                    ->name('overview')
                    ->middleware('can:viewAny,App\Models\CommunityVolunteers\CommunityVolunteer');

                // Report view
                Route::view('report', 'cmtyvol.report')
                    ->name('report')
                    ->middleware('can:viewAny,App\Models\CommunityVolunteers\CommunityVolunteer');

                // Export view
                Route::get('export', 'ExportController@export')
                    ->name('export')
                    ->middleware('can:export,App\Models\CommunityVolunteers\CommunityVolunteer');

                // Export download
                Route::post('doExport', 'ExportController@doExport')
                    ->name('doExport')
                    ->middleware('can:export,App\Models\CommunityVolunteers\CommunityVolunteer');

                // Import view
                Route::get('import', 'ImportController@import')
                    ->name('import')
                    ->middleware('can:import,App\Models\CommunityVolunteers\CommunityVolunteer');

                // Import upload
                Route::post('doImport', 'ImportController@doImport')
                    ->name('doImport')
                    ->middleware('can:import,App\Models\CommunityVolunteers\CommunityVolunteer');

                // Download vCard
                Route::get('{cmtyvol}/vcard', 'ExportController@vcard')
                    ->name('vcard');

                // Responsibilities resource
                Route::resource('responsibilities', 'ResponsibilitiesController')
                    ->except('show');

                // Maintenance
                Route::get('maintenance', 'MaintenanceController@maintenance')
                    ->name('maintenance')
                    ->middleware('can:maintenance,App\Models\CommunityVolunteers\CommunityVolunteer');
                Route::post('maintenance', 'MaintenanceController@doMaintenance')
                    ->name('doMaintenance')
                    ->middleware('can:maintenance,App\Models\CommunityVolunteers\CommunityVolunteer');
            });

        // Community volunteers resource
        Route::resource('cmtyvol', 'ListController');
    });

//
// Library
//

Route::middleware(['auth', 'language', 'can:operate-library'])
    ->namespace('Library')
    ->prefix('library')
    ->name('library.')
    ->group(function () {
        // SPA
        Route::get('', 'LibraryController@index')
            ->name('index');
        Route::get('/{any}', 'LibraryController@index')
            ->where('any', '.*');
    });

//
// Shop
//

Route::middleware(['auth', 'language'])
    ->namespace('Shop')
    ->prefix('shop')
    ->name('shop.')
    ->group(function () {
        Route::middleware(['can:validate-shop-coupons'])
            ->group(function () {
                Route::view('/', 'shop.index')->name('index');
                Route::view('manageCards', 'shop.manageCards')->name('manageCards');
            });
    });

Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});
