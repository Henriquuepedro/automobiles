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

use Illuminate\Support\Facades\Route;

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
    Route::get('/automoveis/edit/{codAuto}', 'Automovel\AutomovelController@edit')->name('admin.automoveis.edit');

    Route::get('/config/destaque', 'Config\ConfigController@index')->name('config.destaque');

    Route::post('/automoveis/cadastro/save', 'Automovel\AutomovelController@store')->name('admin.automoveis.cadastro.save');
    Route::post('/automoveis/cadastro/update', 'Automovel\AutomovelController@update')->name('admin.automoveis.cadastro.update');



    // Consulta AJAX
    Route::group(['prefix' => '/ajax', 'as' => 'ajax.'], function () {
        Route::group(['prefix' => '/opcional', 'as' => 'optional.'], function () {

            Route::get('/buscar/{tipo_auto}', [App\Http\Controllers\Admin\OpcionalController::class, 'getOptionals'])->name('getOptionals');
            Route::get('/buscar/{tipo_auto}/{auto_id}', [App\Http\Controllers\Admin\OpcionalController::class, 'getOptionalsByAuto'])->name('getOptionalsByAuto');

        });

        Route::group(['prefix' => '/complementar', 'as' => 'optional.'], function () {

            Route::get('/buscar/{tipo_auto}', [App\Http\Controllers\Admin\ComplementarController::class, 'getComplemenetares'])->name('getComplemenetares');
            Route::get('/buscar/{tipo_auto}/{auto_id}', [App\Http\Controllers\Admin\ComplementarController::class, 'getComplemenetaresByAuto'])->name('getComplemenetaresByAuto');

        });
    });


});
