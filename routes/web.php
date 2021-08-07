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

use App\Http\Controllers\Admin\Config\BannerController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Grupo publico
Route::get('/inicio', [App\Http\Controllers\User\HomeController::class, 'home'])->name('user.home');
Route::get('/', [App\Http\Controllers\User\HomeController::class, 'home'])->name('user.home');

Route::get('/automoveis', [App\Http\Controllers\User\AutoController::class, 'list'])->name('user.auto.list');

// Consulta AJAX
Route::group(['prefix' => '/ajax', 'as' => 'ajax.'], function () {
    Route::group(['prefix' => '/config', 'as' => 'config.'], function () {
        Route::get('/ordem-pagina-inicial', [App\Http\Controllers\User\HomeController::class, 'getOrderHomePage'])->name('getOrderHomePage');
    });

    Route::group(['prefix' => '/banner', 'as' => 'banner.'], function () {
        Route::get('/inicio', [App\Http\Controllers\User\BannerController::class, 'getBannersHome'])->name('getBannersHome');
    });

    Route::group(['prefix' => '/automoveis', 'as' => 'autos.'], function () {
        Route::post('/listagem/page/{page}', [App\Http\Controllers\User\AutoController::class, 'getAutos'])->name('getAutos');
        Route::get('/buscar/{id}', [App\Http\Controllers\User\AutoController::class, 'getDataAutoPreview'])->name('getDataAutoPreview');
        Route::get('/listagem/destaque', [App\Http\Controllers\User\AutoController::class, 'getAutosFeatured'])->name('getAutosFeatured');
        Route::get('/listagem/recente', [App\Http\Controllers\User\AutoController::class, 'getAutosRecent'])->name('getAutosRecent');
    });

    Route::group(['prefix' => '/loja', 'as' => 'store.'], function () {
        Route::get('/dados', [App\Http\Controllers\User\StoreController::class, 'getStore'])->name('getStore');
    });

    Route::group(['prefix' => '/depoimento', 'as' => 'testimony.'], function () {
        Route::get('/primario', [App\Http\Controllers\User\TestimonyController::class, 'getTestimonyPrimary'])->name('getTestimonyPrimary');
    });

    Route::group(['prefix' => '/filtro', 'as' => 'filter.'], function () {
        Route::get('/buscar', [App\Http\Controllers\User\AutoController::class, 'getFilterAutos'])->name('getFilterAutos');
    });

    Route::group(['prefix' => '/opcionais', 'as' => 'optionals.'], function () {
        Route::get('/buscar', [App\Http\Controllers\User\AutoController::class, 'getOptionalsAutos'])->name('getOptionalsAutos');
    });
});

Auth::routes();

// Grupo admin
Route::group(['middleware' => ['auth'], 'namespace' => 'Admin', 'prefix' => 'admin'], function (){
    Route::get('/home', 'AdminController@index')->name('admin.home');
    Route::get('/', 'AdminController@index')->name('admin.home');

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

    Route::get('/config/banner', [BannerController::class, 'index'])->name('config.banner.index');

    Route::get('/empresa', [App\Http\Controllers\Admin\CompanyController::class, 'manageCompany'])->name('admin.company');
    Route::post('/empresa/atualizar', [App\Http\Controllers\Admin\CompanyController::class, 'update'])->name('admin.company.update');

    //Route::post('/loja/atualizar', [App\Http\Controllers\Admin\StoreController::class, 'update'])->name('admin.store.update');

    Route::get('/depoimento', [App\Http\Controllers\Admin\TestimonyController::class, 'index'])->name('admin.testimony.index');
    Route::get('/depoimento/cadastro', [App\Http\Controllers\Admin\TestimonyController::class, 'new'])->name('admin.testimony.new');
    Route::get('/depoimento/atualizar/{id}', [App\Http\Controllers\Admin\TestimonyController::class, 'edit'])->name('admin.testimony.edit');
    Route::post('/depoimento/atualizar', [App\Http\Controllers\Admin\TestimonyController::class, 'update'])->name('admin.testimony.update');
    Route::post('/depoimento/cadastrar', [App\Http\Controllers\Admin\TestimonyController::class, 'insert'])->name('admin.testimony.insert');

    // Consulta AJAX
    Route::group(['prefix' => '/ajax', 'as' => 'ajax.'], function () {
        Route::group(['prefix' => '/opcional', 'as' => 'optional.'], function () {

            Route::get('/buscar_opcional/{id}', [App\Http\Controllers\Admin\OpcionalController::class, 'getOptional'])->name('get');
            Route::get('/buscar/{tipo_auto}/store/{store}', [App\Http\Controllers\Admin\OpcionalController::class, 'getOptionals'])->name('getOptionals');
            Route::get('/buscar/{tipo_auto}/store/{store}/{auto_id}', [App\Http\Controllers\Admin\OpcionalController::class, 'getOptionalsByAuto'])->name('getOptionalsByAuto');
            Route::post('/cadastrar', [App\Http\Controllers\Admin\OpcionalController::class, 'insert'])->name('insert');
            Route::put('/atualizar', [App\Http\Controllers\Admin\OpcionalController::class, 'update'])->name('update');

        });

        Route::group(['prefix' => '/complementar', 'as' => 'complementar.'], function () {

            Route::get('/buscar_complementar/{id}', [App\Http\Controllers\Admin\ComplementarController::class, 'getComplement'])->name('get');
            Route::get('/buscar/{tipo_auto}/store/{store}', [App\Http\Controllers\Admin\ComplementarController::class, 'getComplemenetares'])->name('getComplemenetares');
            Route::get('/buscar/{tipo_auto}/store/{store}/{auto_id}', [App\Http\Controllers\Admin\ComplementarController::class, 'getComplemenetaresByAuto'])->name('getComplemenetaresByAuto');
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
            Route::get('/buscar/{store}', [App\Http\Controllers\Admin\Config\HomePageController::class, 'getConfigHomePageByStore'])->name('getConfigHomePageByStore');

        });

        Route::group(['prefix' => '/ckeditor', 'as' => 'ckeditor.'], function () {

            Route::post('/upload', [App\Http\Controllers\Admin\Config\PageDynamicController::class, 'uploadImages'])->name('uploadImages');

        });

        Route::group(['prefix' => '/loja', 'as' => 'store.'], function () {

            Route::get('/buscar/{store}', [App\Http\Controllers\Admin\StoreController::class, 'getStore'])->name('getStore');
            Route::post('/atualizar', [App\Http\Controllers\Admin\StoreController::class, 'update'])->name('update');

        });

        Route::group(['prefix' => '/usuario', 'as' => 'user.'], function () {

            Route::get('/buscar/todos', [App\Http\Controllers\Admin\UserController::class, 'getUsers'])->name('getUsers');
            Route::get('/buscar/{user}', [App\Http\Controllers\Admin\UserController::class, 'getUser'])->name('getUser');
            Route::post('/cadastrar', [App\Http\Controllers\Admin\UserController::class, 'insert'])->name('insert');
            Route::post('/atualizar', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('update');
            Route::post('/inativar', [App\Http\Controllers\Admin\UserController::class, 'inactive'])->name('inactive');

        });

        Route::group(['prefix' => '/banner', 'as' => 'banner.'], function () {

            Route::post('/rearrangeOrderBanners', [BannerController::class, 'rearrangeOrder'])->name('rearrangeOrder');
            Route::get('/buscar/{store}', [BannerController::class, 'getBannersStore'])->name('getBannersStore');
            Route::post('/cadastro', [BannerController::class, 'insert'])->name('insert');
            Route::post('/excluir', [BannerController::class, 'remove'])->name('remove');

        });

        Route::group(['prefix' => '/depoimento', 'as' => 'testimony.'], function () {
            Route::post('/buscar', [App\Http\Controllers\Admin\TestimonyController::class, 'fetchTestimonyData'])->name('fetch');
            Route::delete('/excluir/{id}', [App\Http\Controllers\Admin\TestimonyController::class, 'remove'])->name('remove');
        });

    });
});
