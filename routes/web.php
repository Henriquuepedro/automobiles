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

    Route::post('/automoveis/cadastro/save', 'Automovel\AutomovelController@store')->name('admin.automoveis.cadastro.save');
    Route::post('/automoveis/cadastro/update', 'Automovel\AutomovelController@update')->name('admin.automoveis.cadastro.update');

    Route::get('/config/complementares', 'ComplementarController@list')->name('register.complements.manage');
    Route::get('/config/opcionais', 'OpcionalController@list')->name('register.optionals.manage');
    Route::get('/config/estadosFinanceiro', 'EstadoFinanceiroController@list')->name('register.financialsStatus.manage');

    Route::get('/config/paginaInicial', 'Config\HomePageController@homePage')->name('config.homePage');
    Route::get('/config/paginaDinamica', 'Config\PageDynamicController@list')->name('config.pageDyncamic.listagem');
    Route::get('/config/paginaDinamica/cadastro', 'Config\PageDynamicController@new')->name('config.pageDyncamic.new');
    Route::post('/config/paginaDinamica/insert', 'Config\PageDynamicController@insert')->name('config.pageDyncamic.insert');
    Route::post('/config/paginaDinamica/update', 'Config\PageDynamicController@update')->name('config.pageDyncamic.update');
    Route::get('/config/paginaDinamica/{id}', 'Config\PageDynamicController@edit')->name('config.pageDyncamic.edit');

    Route::get('/empresa', [App\Http\Controllers\Admin\CompanyController::class, 'manageCompany'])->name('admin.company');
    Route::post('/empresa/atualizar', [App\Http\Controllers\Admin\CompanyController::class, 'update'])->name('admin.company.update');

    Route::post('/loja/atualizar', [App\Http\Controllers\Admin\StoreController::class, 'update'])->name('admin.store.update');

    // Consulta AJAX
    Route::group(['prefix' => '/ajax', 'as' => 'ajax.'], function () {
        Route::group(['prefix' => '/opcional', 'as' => 'optional.'], function () {

            Route::get('/buscar_opcional/{id}', [App\Http\Controllers\Admin\OpcionalController::class, 'getOptional'])->name('get');
            Route::get('/buscar/{tipo_auto}', [App\Http\Controllers\Admin\OpcionalController::class, 'getOptionals'])->name('getOptionals');
            Route::get('/buscar/{tipo_auto}/{auto_id}', [App\Http\Controllers\Admin\OpcionalController::class, 'getOptionalsByAuto'])->name('getOptionalsByAuto');
            Route::post('/cadastrar', [App\Http\Controllers\Admin\OpcionalController::class, 'insert'])->name('insert');
            Route::put('/atualizar', [App\Http\Controllers\Admin\OpcionalController::class, 'update'])->name('update');

        });

        Route::group(['prefix' => '/complementar', 'as' => 'complementar.'], function () {

            Route::get('/buscar_complementar/{id}', [App\Http\Controllers\Admin\ComplementarController::class, 'getComplement'])->name('get');
            Route::get('/buscar/{tipo_auto}', [App\Http\Controllers\Admin\ComplementarController::class, 'getComplemenetares'])->name('getComplemenetares');
            Route::get('/buscar/{tipo_auto}/{auto_id}', [App\Http\Controllers\Admin\ComplementarController::class, 'getComplemenetaresByAuto'])->name('getComplemenetaresByAuto');
            Route::post('/cadastrar', [App\Http\Controllers\Admin\ComplementarController::class, 'insert'])->name('insert');
            Route::put('/atualizar', [App\Http\Controllers\Admin\ComplementarController::class, 'update'])->name('update');

        });

        Route::group(['prefix' => '/estadoFinanceiro', 'as' => 'financialStatus.'], function () {

            Route::get('/buscar_estadoFinanceiro/{id}', [App\Http\Controllers\Admin\EstadoFinanceiroController::class, 'getFinancialStatus'])->name('get');
            Route::post('/cadastrar', [App\Http\Controllers\Admin\EstadoFinanceiroController::class, 'insert'])->name('insert');
            Route::put('/atualizar', [App\Http\Controllers\Admin\EstadoFinanceiroController::class, 'update'])->name('update');

        });

        Route::group(['prefix' => '/paginaInicial', 'as' => 'homePage.'], function () {

            Route::put('/atualizar', [App\Http\Controllers\Admin\Config\HomePageController::class, 'updateOrder'])->name('updateOrder');

        });

        Route::group(['prefix' => '/ckeditor', 'as' => 'ckeditor.'], function () {

            Route::post('/upload', [App\Http\Controllers\Admin\Config\PageDynamicController::class, 'uploadImages'])->name('uploadImages');

        });

        Route::group(['prefix' => '/loja', 'as' => 'store.'], function () {

            Route::get('/buscar/{store}', [App\Http\Controllers\Admin\StoreController::class, 'getStore'])->name('admin.store.getStore');
            Route::get('/atualizar', [App\Http\Controllers\Admin\StoreController::class, 'update'])->name('admin.store.update');

        });

    });
});
