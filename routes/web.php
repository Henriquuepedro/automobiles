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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', function() {
    return view('home');
})->name('home');

// Grupo admin/
Route::group(['middleware' => ['auth'], 'namespace' => 'Admin', 'prefix' => 'admin'], function (){
    Route::get('/', 'AdminController@index')->name('admin.home');
    Route::get('/home', 'AdminController@index')->name('admin.home');
    Route::get('/automoveis', 'Automovel\AutomovelController@index')->name('admin.automoveis.listagem');
    Route::get('/automoveis/cadastro', 'Automovel\AutomovelController@cadastro')->name('admin.automoveis.cadastro');
    Route::post('/automoveis/cadastro/save', 'Automovel\AutomovelController@store')->name('admin.automoveis.cadastro.save');


    Route::get('/automoveis/edit/{codAuto}', 'Automovel\AutomovelController@edit')->name('admin.automoveis.edit');

    /**
     * BALANCE, Deposit / Withdraw / Transfer
     */
//    Route::get('balance', 'BalanceController@index')->name('admin.balance');
//
//    Route::get('balance/deposit', 'BalanceController@deposit')->name('balance.deposit');
//    Route::post('balance/deposit', 'BalanceController@depositStore')->name('deposit.store');
//
//    Route::get('balance/withdraw', 'BalanceController@withdraw')->name('balance.withdraw');
//    Route::post('balance/withdraw', 'BalanceController@withdrawStore')->name('withdraw.store');
//
//    Route::get('balance/transfer', 'BalanceController@transfer')->name('balance.transfer');
//    Route::post('balance/confirm-transfer', 'BalanceController@confirmTransfer')->name('confirm.transfer');
//    Route::post('balance/transfer', 'BalanceController@transferStore')->name('transfer.store');

    /**
     * HISTORIC
     */
    Route::get('historic', 'BalanceController@historic')->name('admin.historic');

});
