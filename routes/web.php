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

use App\Http\Controllers\Admin\Config\AboutStore;
use App\Http\Controllers\Admin\Config\BannerController;
use App\Http\Controllers\Admin\FipeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Grupo publico
Route::get('/inicio', [App\Http\Controllers\User\HomeController::class, 'home'])->name('user.home');
Route::get('/', [App\Http\Controllers\User\HomeController::class, 'home'])->name('user.home');

Route::get('/automoveis', [App\Http\Controllers\User\AutoController::class, 'list'])->name('user.auto.list');
Route::get('/automovel/{auto}', [App\Http\Controllers\User\AutoController::class, 'previewAuto'])->name('user.auto.preview');

Route::get('/pagina/{page}', [App\Http\Controllers\User\PageDynamicController::class, 'viewPage'])->name('user.pageDynamic.view');

Route::get('/contato', [App\Http\Controllers\User\ContactController::class, 'index'])->name('user.contact.index');

Route::get('/sobre-loja', [\App\Http\Controllers\User\AboutStore::class, 'index'])->name('user.about.index');

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
        Route::get('/listagem/relacionados/{auto}/{registers}', [App\Http\Controllers\User\AutoController::class, 'getAutosRelated'])->name('getAutosRelated');
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

    Route::group(['prefix' => '/contato', 'as' => 'contact.'], function () {
        Route::post('/enviar-mensagem', [App\Http\Controllers\User\ContactController::class, 'sendMessage'])->name('sendMessage');
    });
});

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Auth::routes();
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
});

// Grupo admin
Route::group(['middleware' => ['auth'], 'namespace' => 'Admin', 'prefix' => 'admin', 'as' => 'admin.'], function (){
    Route::get('/home', 'AdminController@index')->name('home');
    Route::get('/', 'AdminController@index')->name('home');

    Route::get('/automoveis', 'Automovel\AutomovelController@index')->name('automoveis.listagem');
    Route::get('/automoveis/cadastro', 'Automovel\AutomovelController@cadastro')->name('automoveis.cadastro');
    Route::get('/automoveis/edit/{codAuto}', 'Automovel\AutomovelController@edit')->name('automoveis.edit');

    Route::post('/automoveis/cadastro/save', 'Automovel\AutomovelController@store')->name('automoveis.cadastro.save');
    Route::post('/automoveis/cadastro/update', 'Automovel\AutomovelController@update')->name('automoveis.cadastro.update');

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

    Route::get('/empresa', [App\Http\Controllers\Admin\CompanyController::class, 'manageCompany'])->name('company');
    Route::post('/empresa/atualizar', [App\Http\Controllers\Admin\CompanyController::class, 'update'])->name('company.update');

    //Route::post('/loja/atualizar', [App\Http\Controllers\Admin\StoreController::class, 'update'])->name('store.update');

    Route::get('/depoimento', [App\Http\Controllers\Admin\TestimonyController::class, 'index'])->name('testimony.index');
    Route::get('/depoimento/cadastro', [App\Http\Controllers\Admin\TestimonyController::class, 'new'])->name('testimony.new');
    Route::get('/depoimento/atualizar/{id}', [App\Http\Controllers\Admin\TestimonyController::class, 'edit'])->name('testimony.edit');
    Route::post('/depoimento/atualizar', [App\Http\Controllers\Admin\TestimonyController::class, 'update'])->name('testimony.update');
    Route::post('/depoimento/cadastrar', [App\Http\Controllers\Admin\TestimonyController::class, 'insert'])->name('testimony.insert');

    Route::get('/formulario-contato', [App\Http\Controllers\Admin\ContactController::class, 'index'])->name('contactForm.index');
    Route::get('/formulario-contato/{id}', [App\Http\Controllers\Admin\ContactController::class, 'view'])->name('contactForm.view');

    Route::get('/sobre-loja', [App\Http\Controllers\Admin\Config\AboutStore::class, 'index'])->name('config.about.index');

    Route::get('/bloqueado', [App\Http\Controllers\Admin\StoreController::class, 'lockScreen'])->name('lockscreen');

    // ADMIN MASTER
    Route::group(['prefix' => '/master', 'as' => 'master.'], function () {

        Route::group(['prefix' => '/empresa', 'as' => 'company.'], function () {
            Route::get('/', [App\Http\Controllers\Master\CompanyController::class, 'index'])->name('index');
            Route::get('/novo', [App\Http\Controllers\Master\CompanyController::class, 'new'])->name('new');
            Route::get('/{id}', [App\Http\Controllers\Master\CompanyController::class, 'edit'])->name('edit');
            Route::post('/atualizar', [App\Http\Controllers\Master\CompanyController::class, 'update'])->name('update');
            Route::post('/novo', [App\Http\Controllers\Master\CompanyController::class, 'insert'])->name('insert');

            Route::group(['prefix' => '/{company}/loja', 'as' => 'store.'], function () {
                Route::get('/novo', [App\Http\Controllers\Master\StoreController::class, 'new'])->name('new');
                Route::get('/{store}', [App\Http\Controllers\Master\StoreController::class, 'edit'])->name('edit');
                Route::post('/novo', [App\Http\Controllers\Master\StoreController::class, 'insert'])->name('insert');
                Route::post('/atualizar', [App\Http\Controllers\Master\StoreController::class, 'update'])->name('update');
            });

            Route::group(['prefix' => '/{company}/usuario', 'as' => 'user.'], function () {
                Route::get('/novo', [App\Http\Controllers\Master\UserController::class, 'new'])->name('new');
                Route::get('/{user}', [App\Http\Controllers\Master\UserController::class, 'edit'])->name('edit');
                Route::post('/novo', [App\Http\Controllers\Master\UserController::class, 'insert'])->name('insert');
                Route::post('/atualizar', [App\Http\Controllers\Master\UserController::class, 'update'])->name('update');
            });
        });

        Route::group(['prefix' => '/ajax', 'as' => 'ajax.'], function () {
            Route::group(['prefix' => '/empresa', 'as' => 'company.'], function () {
                Route::post('/buscar', [App\Http\Controllers\Master\CompanyController::class, 'fetch'])->name('fetch');
            });
        });
    });

    // Consulta AJAX
    Route::group(['prefix' => '/ajax', 'as' => 'ajax.'], function () {

        Route::group(['prefix' => '/fipe', 'as' => 'fipe.'], function () {
            Route::get('/{auto}/marcas', [FipeController::class, 'getBrand'])->name('getBrand');
            Route::get('/{auto}/marcas/{brand}/modelos', [FipeController::class, 'getModel'])->name('getModel');
            Route::get('/{auto}/marcas/{brand}/modelos/{model}/anos', [FipeController::class, 'getYear'])->name('getYear');
            Route::get('/{auto}/marcas/{brand}/modelos/{model}/anos/{year}', [FipeController::class, 'getAuto'])->name('getAuto');
        });

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
            Route::get('/buscar/store/{store}', [App\Http\Controllers\Admin\EstadoFinanceiroController::class, 'getFinancialsStatus'])->name('getFinancialsStatus');
            Route::get('/buscar/store/{store}/{auto_id}', [App\Http\Controllers\Admin\EstadoFinanceiroController::class, 'getFinancialsStatusByAuto'])->name('getFinancialsStatusByAuto');
            Route::post('/cadastrar', [App\Http\Controllers\Admin\EstadoFinanceiroController::class, 'insert'])->name('insert');
            Route::put('/atualizar', [App\Http\Controllers\Admin\EstadoFinanceiroController::class, 'update'])->name('update');

        });

        Route::group(['prefix' => '/paginaInicial', 'as' => 'homePage.'], function () {

            Route::put('/atualizar', [App\Http\Controllers\Admin\Config\HomePageController::class, 'updateOrder'])->name('updateOrder');
            Route::get('/buscar/{store}', [App\Http\Controllers\Admin\Config\HomePageController::class, 'getConfigHomePageByStore'])->name('getConfigHomePageByStore');

        });

        Route::group(['prefix' => '/ckeditor', 'as' => 'ckeditor.'], function () {

            Route::post('/upload/paginaDinamica', [App\Http\Controllers\Admin\Config\PageDynamicController::class, 'uploadImages'])->name('uploadImages');
            Route::post('/upload/obsAutos', [App\Http\Controllers\Admin\Automovel\AutomovelController::class, 'uploadImagesObsAuto'])->name('uploadImagesObsAuto');

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

        Route::group(['prefix' => '/formulario-contato', 'as' => 'contactForm.'], function () {
            Route::post('/buscar', [App\Http\Controllers\Admin\ContactController::class, 'fetchContactData'])->name('fetch');
            Route::delete('/excluir/{id}', [App\Http\Controllers\Admin\ContactController::class, 'remove'])->name('remove');
        });

        Route::group(['prefix' => '/sobre-loja', 'as' => 'about.'], function () {
            Route::get('/buscar/{store}', [AboutStore::class, 'getAboutStore'])->name('getAboutStore');
            Route::post('/atualizar', [AboutStore::class, 'update'])->name('update');
        });

    });
});
