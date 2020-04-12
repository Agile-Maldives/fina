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

Route::post('register', 'UserApiController@register');
Route::get('users', 'UserApiController@index');
Route::post('users/store', 'UserApiController@store');
Route::post('users/update/{id}', 'UserApiController@update');
Route::delete('users/{id}', 'UserApiController@destroy');

Route::group([
    'namespace' => 'Auth',
    'middleware' => 'api',
    'prefix' => 'password'
], function () {
    Route::post('create', 'PasswordResetController@create');
    Route::get('find/{token}', 'PasswordResetController@find');
    Route::post('reset', 'PasswordResetController@reset');
});


Route::get('aggreements/{file}', 'LoanApiController@downloadAggreement');
Route::get('id_cards/{file}', 'AccountApiController@downloadIdCard');

Route::get('accounts', 'AccountApiController@index');
Route::post('accounts/store', 'AccountApiController@store');
Route::post('accounts/update/{id}', 'AccountApiController@update');
Route::get('accounts/{id}', 'AccountApiController@show');
Route::delete('accounts/{id}', 'AccountApiController@destroy');


Route::get('general-settings', 'GeneralSettingsApiController@show');
Route::post('general-settings/update', 'GeneralSettingsApiController@update');

Route::get('loans', 'LoanApiController@index');
Route::get('loans-by-accountid/{accountId}', 'LoanApiController@getLoansByAccountId');

Route::post('loans/store', 'LoanApiController@store');
Route::post('loans/update/{id}', 'LoanApiController@update');
Route::get('loans/{id}', 'LoanApiController@show');
Route::delete('loans/{id}', 'LoanApiController@destroy');

Route::get('loans-issued-report', 'LoanApiController@loansIssuedReport');
Route::get('loans-issued-report-pdf', 'LoanApiController@loansIssuedReportPdf');
Route::get('loans-issued-report-csv', 'LoanApiController@loansIssuedReportCsv');



Route::get('dues-per-loan-for-account-report/{accountId}', 'LoanApiController@duesPerLoanForAccount');
Route::get('dues-per-loan-for-account-report-pdf/{accountId}', 'LoanApiController@duesPerLoanForAccountPdf');
Route::get('dues-per-loan-for-account-report-csv/{accountId}', 'LoanApiController@duesPerLoanForAccountCsv');

Route::get('dues-per-loan-for-all-accounts-report', 'LoanApiController@duesPerLoanForAllAccounts');
Route::get('dues-per-loan-for-all-accounts-report-pdf', 'LoanApiController@duesPerLoanForAllAccountsPdf');
Route::get('dues-per-loan-for-all-accounts-report-csv', 'LoanApiController@duesPerLoanForAllAccountsCsv');



Route::get('test/{begin}/{end}/{interval}', 'LoanApiController@getDatePeriodDatesWithInteval');


Route::get('loan-intervals-by-loanid/{loanId}', 'LoanIntervalApiController@getLoanIntervalsByLoanId');
Route::post('pay-installment/{loanIntervalId}', 'LoanIntervalApiController@payInstallment');


Route::get('account-statement/{accountId}', 'LoanIntervalApiController@accountStatement');
Route::get('account-statement-pdf/{accountId}', 'LoanIntervalApiController@accountStatementPdf');
Route::get('account-statement-csv/{accountId}', 'LoanIntervalApiController@accountStatementCsv');

Route::get('all-accounts-statement', 'LoanIntervalApiController@allAccountsStatement');
Route::get('all-accounts-statement-pdf', 'LoanIntervalApiController@allAccountsStatementPdf');
Route::get('all-accounts-statement-csv', 'LoanIntervalApiController@allAccountsStatementCsv');

Route::get('users', 'UserManagementController@getUsers');

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

//Route::post('login', 'ApiAuthController@login');
//Route::group(['middleware' => 'auth:api'], function(){
//	Route::post('getUser', 'ApiAuthController@getUser');
//});

