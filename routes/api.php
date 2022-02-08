<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/login', 'AuthController@login');
Route::post('/register', 'AuthController@register');

Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::post('/loan/apply', 'LoanController@apply');
    Route::post('/loans', 'LoanController@loans');
    Route::get('/loan/{loan}', 'LoanController@loan');
    Route::get('/loan/{loan_id}/{payment_id}', 'LoanController@getPayment');

    Route::post('/loan/{loan_id}/{payment_id}/pay', 'LoanController@pay');
});

Route::group(['middleware' => ['auth:sanctum', 'admin'], 'namespace' => 'Admin'], function(){
    Route::post('/admin/loans', 'Loans@loans');
    Route::post('/admin/loan/update', 'Loans@update_loan');
});
