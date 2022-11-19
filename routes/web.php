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

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\Automobile\AutomobileController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\Config\AboutStore;
use App\Http\Controllers\Admin\Config\BannerController;
use App\Http\Controllers\Admin\Config\HomePageController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\FipeController;
use App\Http\Controllers\Admin\Config\PageDynamicController;
use App\Http\Controllers\Admin\ComplementaryController;
use App\Http\Controllers\Admin\OptionalController;
use App\Http\Controllers\Admin\FinancialStateController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\Rent\CharacteristicController;
use App\Http\Controllers\Admin\Rent\WalletController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TestimonyController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\UserController;

use App\Http\Controllers\Master\CompanyController as MasterCompanyController;
use App\Http\Controllers\Master\StoreController as MasterStoreController;
use App\Http\Controllers\Master\UserController as MasterUserController;

use App\Http\Controllers\User\StoreController as UserStoreController;
use App\Http\Controllers\User\TestimonyController as UserTestimonyController;
use App\Http\Controllers\User\AutoController as UserAutoController;
use App\Http\Controllers\User\HomeController as UserHomeController;
use App\Http\Controllers\User\BannerController as UserBannerController;
use App\Http\Controllers\User\ContactController as UserContactController;
use App\Http\Controllers\User\PageDynamicController as UserPageDynamicController;
use App\Http\Controllers\User\AboutStore as UserAboutStore;
use App\Http\Controllers\User\Rent\AutoController as UserRentAutoController;

use App\Http\Controllers\Admin\Rent\AutoController as RentAutoController;
use App\Http\Controllers\Admin\Rent\GroupController;
use App\Http\Controllers\Admin\Rent\PlaceController;
use App\Http\Controllers\Admin\Rent\SettingController;

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Grupo publico
Route::group(['as' => 'user.'], function () {
    Route::get('/inicio', [UserHomeController::class, 'home'])->name('home');
    Route::get('/', [UserHomeController::class, 'home'])->name('home');

    Route::get('/automoveis', [UserAutoController::class, 'index'])->name('auto.index');
    Route::get('/automovel/{auto}', [UserAutoController::class, 'previewAuto'])->name('auto.preview');

    Route::get('/pagina/{page}', [UserPageDynamicController::class, 'viewPage'])->name('pageDynamic.view');

    Route::get('/contato', [UserContactController::class, 'index'])->name('contact.index');

    Route::get('/sobre-loja', [UserAboutStore::class, 'index'])->name('about.index');

    Route::group(['middleware' => ['can:client-view-rent'], 'prefix' => '/aluguel', 'as' => 'rent.'], function () {
        Route::get('/', [UserRentAutoController::class, 'index'])->name('index');
    });
});

// Consulta AJAX
Route::group(['prefix' => '/ajax', 'as' => 'ajax.'], function () {
    Route::group(['prefix' => '/config', 'as' => 'config.'], function () {
        Route::get('/ordem-pagina-inicial', [UserHomeController::class, 'getOrderHomePage'])->name('getOrderHomePage');
    });

    Route::group(['prefix' => '/banner', 'as' => 'banner.'], function () {
        Route::get('/inicio', [UserBannerController::class, 'getBannersHome'])->name('getBannersHome');
    });

    Route::group(['prefix' => '/automoveis', 'as' => 'autos.'], function () {
        Route::post('/listagem/page/{page}', [UserAutoController::class, 'getAutos'])->name('getAutos');
        Route::get('/buscar/{id}', [UserAutoController::class, 'getDataAutoPreview'])->name('getDataAutoPreview');
        Route::get('/listagem/destaque', [UserAutoController::class, 'getAutosFeatured'])->name('getAutosFeatured');
        Route::get('/listagem/recente', [UserAutoController::class, 'getAutosRecent'])->name('getAutosRecent');
        Route::get('/listagem/relacionados/{auto}/{registers}', [UserAutoController::class, 'getAutosRelated'])->name('getAutosRelated');
    });

    Route::group(['prefix' => '/loja', 'as' => 'store.'], function () {
        Route::get('/dados', [UserStoreController::class, 'getStore'])->name('getStore');
    });

    Route::group(['prefix' => '/depoimento', 'as' => 'testimony.'], function () {
        Route::get('/primario', [UserTestimonyController::class, 'getTestimonyPrimary'])->name('getTestimonyPrimary');
    });

    Route::group(['prefix' => '/filtro', 'as' => 'filter.'], function () {
        Route::get('/buscar', [UserAutoController::class, 'getFilterAutos'])->name('getFilterAutos');
        Route::post('/modelos-anos', [UserAutoController::class, 'getFilterByBrands'])->name('getFilterByBrands');
    });

    Route::group(['prefix' => '/opcionais', 'as' => 'optionals.'], function () {
        Route::get('/buscar', [UserAutoController::class, 'getOptionalsAutos'])->name('getOptionalsAutos');
    });

    Route::group(['prefix' => '/contato', 'as' => 'contact.'], function () {
        Route::post('/enviar-mensagem', [UserContactController::class, 'sendMessage'])->name('sendMessage');
    });

    Route::group(['middleware' => ['can:client-view-rent'], 'prefix' => '/aluguel', 'as' => 'rent.'], function () {
        Route::get('/caracteristica/buscar', [UserRentAutoController::class, 'getOptionalsAutos'])->name('characteristic.getOptionalsAutos');

        Route::group(['prefix' => '/filtro', 'as' => 'filter.'], function () {
            Route::get('/buscar', [UserRentAutoController::class, 'getFilterAutos'])->name('getFilterAutos');
            Route::post('/modelos-anos', [UserRentAutoController::class, 'getFilterByBrands'])->name('getFilterByBrands');
        });
    });
});

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Auth::routes();
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/register', function() { // rota register sem permissão
        abort(404);
    });
});

// Grupo admin
Route::group(['middleware' => ['auth'], 'namespace' => 'Admin', 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/home', [AdminController::class, 'index'])->name('home');
    Route::get('/', [AdminController::class, 'index'])->name('home');

    Route::group(['prefix' => '/automoveis', 'as' => 'automobiles.'], function () {
        Route::get('/', [AutomobileController::class, 'index'])->name('index');
        Route::get('/cadastro', [AutomobileController::class, 'cadastro'])->name('cadastro');
        Route::get('/edit/{codAuto}', [AutomobileController::class, 'edit'])->name('edit');
        Route::post('/automoveis/cadastro/save', [AutomobileController::class, 'store'])->name('cadastro.save');
        Route::post('/automoveis/cadastro/update', [AutomobileController::class, 'update'])->name('cadastro.update');
    });

    Route::group(['prefix' => '/config', 'as' => 'register.'], function () {
        Route::get('/complementares', [ComplementaryController::class, 'list'])->name('complements.manage');
        Route::get('/opcionais', [OptionalController::class, 'list'])->name('optionals.manage');
        Route::get('/estadosFinanceiro', [FinancialStateController::class, 'list'])->name('financialsStatus.manage');
    });

    Route::group(['prefix' => '/config', 'as' => 'config.'], function () {
        Route::get('/paginaInicial', [HomePageController::class, 'homePage'])->name('homePage');

        Route::get('/paginaDinamica', [PageDynamicController::class, 'list'])->name('pageDynamic.index');
        Route::get('/paginaDinamica/cadastro', [PageDynamicController::class, 'new'])->name('pageDynamic.new');
        Route::post('/paginaDinamica/insert', [PageDynamicController::class, 'insert'])->name('pageDynamic.insert');
        Route::post('/paginaDinamica/update', [PageDynamicController::class, 'update'])->name('pageDynamic.update');
        Route::get('/paginaDinamica/{id}', [PageDynamicController::class, 'edit'])->name('pageDynamic.edit');

        Route::get('/banner', [BannerController::class, 'index'])->name('banner.index');
    });

    Route::group(['middleware' => ['can:view-admin'], 'prefix' => '/empresa', 'as' => 'company.'], function () {
        Route::get('/', [CompanyController::class, 'manageCompany'])->name('index');
        Route::post('/atualizar', [CompanyController::class, 'update'])->name('update');
    });

    Route::group(['prefix' => '/depoimento', 'as' => 'testimony.'], function () {
        Route::get('/', [TestimonyController::class, 'index'])->name('index');
        Route::get('/cadastro', [TestimonyController::class, 'new'])->name('new');
        Route::get('/atualizar/{id}', [TestimonyController::class, 'edit'])->name('edit');
        Route::post('/atualizar', [TestimonyController::class, 'update'])->name('update');
        Route::post('/cadastrar', [TestimonyController::class, 'insert'])->name('insert');
    });

    Route::group(['prefix' => '/formulario-contato', 'as' => 'contactForm.'], function () {
        Route::get('/', [ContactController::class, 'index'])->name('index');
        Route::get('/{id}', [ContactController::class, 'view'])->name('view');
    });

    Route::get('/sobre-loja', [AboutStore::class, 'index'])->name('config.about.index');

    Route::get('/bloqueado', [StoreController::class, 'lockScreen'])->name('lockscreen');

    Route::group(['middleware' => ['can:manage-report'], 'prefix' => '/relatorio', 'as' => 'report.'], function () {
        Route::get('/variacao-fipe', [ReportController::class, 'fipeVariation'])->name('fipeVariation');
    });

    Route::group(['prefix' => '/cores-automoveis', 'as' => 'colorAuto.'], function () {
        Route::get('/', [ColorController::class, 'index'])->name('index');
    });

    Route::group(['prefix' => '/planos', 'as' => 'plan.'], function () {
        Route::get('/', [PlanController::class, 'index'])->name('index');
        Route::get('/confirmar/{type}/{id}', [PlanController::class, 'confirm'])->name('confirm');
        Route::post('/confirmar/{type}/{id}', [PlanController::class, 'checkout'])->name('checkout');
    });

    Route::group(['middleware' => ['can:manage-rent'], 'prefix' => '/aluguel', 'as' => 'rent.'], function () {
        Route::group(['prefix' => '/grupo', 'as' => 'group.'], function () {
            Route::get('/', [GroupController::class, 'index'])->name('index');
            Route::get('/atualizar/{id}', [GroupController::class, 'edit'])->name('edit');
            Route::post('/atualizar', [GroupController::class, 'update'])->name('update');
            Route::get('/novo', [GroupController::class, 'new'])->name('new');
            Route::post('/novo', [GroupController::class, 'insert'])->name('insert');
        });
        Route::group(['prefix' => '/configuracao', 'as' => 'setting.'], function () {
            Route::get('/', [SettingController::class, 'index'])->name('index');
        });
        Route::group(['prefix' => '/local', 'as' => 'place.'], function () {
            Route::get('/', [PlaceController::class, 'index'])->name('index');
            Route::get('/atualizar/{id}', [PlaceController::class, 'edit'])->name('edit');
            Route::post('/atualizar', [PlaceController::class, 'update'])->name('update');
            Route::get('/novo', [PlaceController::class, 'new'])->name('new');
            Route::post('/novo', [PlaceController::class, 'insert'])->name('insert');
        });
        Route::group(['prefix' => '/automovel', 'as' => 'automobile.'], function () {
            Route::get('/', [RentAutoController::class, 'index'])->name('index');
            Route::get('/atualizar/{id}', [RentAutoController::class, 'edit'])->name('edit');
            Route::post('/atualizar', [RentAutoController::class, 'update'])->name('update');
            Route::get('/novo', [RentAutoController::class, 'new'])->name('new');
            Route::post('/novo', [RentAutoController::class, 'insert'])->name('insert');
        });
        Route::group(['prefix' => '/caracteristica', 'as' => 'characteristic.'], function () {
            Route::get('/', [CharacteristicController::class, 'index'])->name('index');
        });
    });

    Route::group(['middleware' => ['can:view-master'], 'prefix' => '/aplicativos', 'as' => 'application.'], function () {
        Route::get('/', [ApplicationController::class, 'index'])->name('index');
    });

    // ADMIN MASTER
    Route::group(['middleware' => ['can:view-master'], 'prefix' => '/master', 'as' => 'master.'], function () {

        Route::group(['prefix' => '/empresa', 'as' => 'company.'], function () {
            Route::get('/', [MasterCompanyController::class, 'index'])->name('index');
            Route::get('/novo', [MasterCompanyController::class, 'new'])->name('new');
            Route::get('/{id}', [MasterCompanyController::class, 'edit'])->name('edit');
            Route::post('/atualizar', [MasterCompanyController::class, 'update'])->name('update');
            Route::post('/novo', [MasterCompanyController::class, 'insert'])->name('insert');

            Route::group(['prefix' => '/{company}/loja', 'as' => 'store.'], function () {
                Route::get('/novo', [MasterStoreController::class, 'new'])->name('new');
                Route::get('/{store}', [MasterStoreController::class, 'edit'])->name('edit');
                Route::post('/novo', [MasterStoreController::class, 'insert'])->name('insert');
                Route::post('/atualizar', [MasterStoreController::class, 'update'])->name('update');
            });

            Route::group(['prefix' => '/{company}/usuario', 'as' => 'user.'], function () {
                Route::get('/novo', [MasterUserController::class, 'new'])->name('new');
                Route::get('/{user}', [MasterUserController::class, 'edit'])->name('edit');
                Route::post('/novo', [MasterUserController::class, 'insert'])->name('insert');
                Route::post('/atualizar', [MasterUserController::class, 'update'])->name('update');
            });
        });

        Route::group(['prefix' => '/ajax', 'as' => 'ajax.'], function () {
            Route::group(['prefix' => '/empresa', 'as' => 'company.'], function () {
                Route::post('/buscar', [MasterCompanyController::class, 'fetch'])->name('fetch');
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
            Route::post('/buscar', [OptionalController::class, 'fetchOptionalData'])->name('fetch');
            Route::get('/buscar_opcional/{id}', [OptionalController::class, 'getOptional'])->name('get');
            Route::get('/buscar/{tipo_auto}/store/{store}', [OptionalController::class, 'getOptionals'])->name('getOptionals');
            Route::get('/buscar/{tipo_auto}/store/{store}/{auto_id}', [OptionalController::class, 'getOptionalsByAuto'])->name('getOptionalsByAuto');
            Route::post('/cadastrar', [OptionalController::class, 'insert'])->name('insert');
            Route::put('/atualizar', [OptionalController::class, 'update'])->name('update');
        });

        Route::group(['prefix' => '/complementar', 'as' => 'complementar.'], function () {
            Route::post('/buscar', [ComplementaryController::class, 'fetchComplementData'])->name('fetch');
            Route::get('/buscar_complementar/{id}', [ComplementaryController::class, 'getComplement'])->name('get');
            Route::get('/buscar/{tipo_auto}/store/{store}', [ComplementaryController::class, 'getComplemenetares'])->name('getComplemenetares');
            Route::get('/buscar/{tipo_auto}/store/{store}/{auto_id}', [ComplementaryController::class, 'getComplementaryByAuto'])->name('getComplementaryByAuto');
            Route::post('/cadastrar', [ComplementaryController::class, 'insert'])->name('insert');
            Route::put('/atualizar', [ComplementaryController::class, 'update'])->name('update');
        });

        Route::group(['prefix' => '/estadoFinanceiro', 'as' => 'financialStatus.'], function () {
            Route::post('/buscar', [FinancialStateController::class, 'fetchFinancialStateData'])->name('fetch');
            Route::get('/buscar_estadoFinanceiro/{id}', [FinancialStateController::class, 'getFinancialStatus'])->name('get');
            Route::get('/buscar/store/{store}', [FinancialStateController::class, 'getFinancialsStatus'])->name('getFinancialsStatus');
            Route::get('/buscar/store/{store}/{auto_id}', [FinancialStateController::class, 'getFinancialsStatusByAuto'])->name('getFinancialsStatusByAuto');
            Route::post('/cadastrar', [FinancialStateController::class, 'insert'])->name('insert');
            Route::put('/atualizar', [FinancialStateController::class, 'update'])->name('update');
        });

        Route::group(['prefix' => '/paginaInicial', 'as' => 'homePage.'], function () {
            Route::put('/atualizar', [HomePageController::class, 'updateOrder'])->name('updateOrder');
            Route::get('/buscar/{store}', [HomePageController::class, 'getConfigHomePageByStore'])->name('getConfigHomePageByStore');

        });

        Route::group(['prefix' => '/ckeditor', 'as' => 'ckeditor.'], function () {
            Route::post('/upload/paginaDinamica', [PageDynamicController::class, 'uploadImages'])->name('uploadImages');
            Route::post('/upload/obsAutos', [AutomobileController::class, 'uploadImagesObsAuto'])->name('uploadImagesObsAuto');
            Route::post('/upload/obsRentAutos', [RentAutoController::class, 'uploadImagesObsAuto'])->name('uploadImagesObsAuto');
        });

        Route::group(['middleware' => ['can:view-admin'], 'prefix' => '/loja', 'as' => 'store.'], function () {
            Route::get('/buscar/{store}', [StoreController::class, 'getStore'])->name('getStore');
            Route::post('/atualizar', [StoreController::class, 'update'])->name('update');

        });

        Route::group(['middleware' => ['can:view-admin'], 'prefix' => '/usuario', 'as' => 'user.'], function () {
            Route::get('/buscar/todos', [UserController::class, 'getUsers'])->name('getUsers');
            Route::get('/buscar/{user}', [UserController::class, 'getUser'])->name('getUser');
            Route::post('/cadastrar', [UserController::class, 'insert'])->name('insert');
            Route::post('/atualizar', [UserController::class, 'edit'])->name('update');
            Route::post('/inativar', [UserController::class, 'inactive'])->name('inactive');

        });

        Route::group(['prefix' => '/banner', 'as' => 'banner.'], function () {
            Route::post('/rearrangeOrderBanners', [BannerController::class, 'rearrangeOrder'])->name('rearrangeOrder');
            Route::get('/buscar/{store}', [BannerController::class, 'getBannersStore'])->name('getBannersStore');
            Route::post('/cadastro', [BannerController::class, 'insert'])->name('insert');
            Route::post('/excluir', [BannerController::class, 'remove'])->name('remove');

        });

        Route::group(['prefix' => '/depoimento', 'as' => 'testimony.'], function () {
            Route::post('/buscar', [TestimonyController::class, 'fetchTestimonyData'])->name('fetch');
            Route::delete('/excluir/{id}', [TestimonyController::class, 'remove'])->name('remove');
        });

        Route::group(['prefix' => '/formulario-contato', 'as' => 'contactForm.'], function () {
            Route::post('/buscar', [ContactController::class, 'fetchContactData'])->name('fetch');
            Route::delete('/excluir/{id}', [ContactController::class, 'remove'])->name('remove');
        });

        Route::group(['prefix' => '/sobre-loja', 'as' => 'about.'], function () {
            Route::get('/buscar/{store}', [AboutStore::class, 'getAboutStore'])->name('getAboutStore');
            Route::post('/atualizar', [AboutStore::class, 'update'])->name('update');
        });

        Route::group(['prefix' => '/automoveis', 'as' => 'automobiles.'], function () {
            Route::post('/buscar', [AutomobileController::class, 'fetchAutoData'])->name('fetch');
            Route::post('/upload-processar', [AutomobileController::class, 'setUploadImage'])->name('setUploadImage');
            Route::delete('/upload-reverter', [AutomobileController::class, 'rmUploadImage'])->name('rmUploadImage');
            Route::get('/upload-buscar/{auto}', [AutomobileController::class, 'getUploadImage'])->name('getUploadImage');
            Route::get('/qtd-estoque-por-marcas', [AutomobileController::class, 'getQtyStockByBrands'])->name('getQtyStockByBrands');
            Route::get('/qtd-estoque-por-tipo-de-automovel', [AutomobileController::class, 'getQtyStockByAutos'])->name('getQtyStockByAutos');
            Route::get('/valor-estoque-por-tipo-de-automovel', [AutomobileController::class, 'getPriceStockByAutos'])->name('getPriceStockByAutos');
        });

        Route::group(['middleware' => ['can:manage-report'], 'prefix' => '/fipe-variacao', 'as' => 'fipeVariation.'], function () {
            Route::get('/{auto}', [FipeController::class, 'getVariationAuto'])->name('getVariationAuto');
        });

        Route::group(['prefix' => '/paginaDinamica', 'as' => 'pageDynamic.'], function () {
            Route::post('/buscar', [PageDynamicController::class, 'fetchPageDynamicData'])->name('fetch');
            Route::delete('/excluir/{id}', [PageDynamicController::class, 'remove'])->name('remove');
        });

        Route::group(['prefix' => '/cores-automoveis', 'as' => 'colorAuto.'], function () {
            Route::post('/buscar', [ColorController::class, 'fetchColorData'])->name('fetch');
            Route::post('/cadastrar', [ColorController::class, 'insert'])->name('insert');
            Route::put('/atualizar', [ColorController::class, 'update'])->name('update');
            Route::get('/buscar_cor/{id}', [ColorController::class, 'getColor'])->name('get');
            Route::get('/buscar-ativas/{store}', [ColorController::class, 'getColorsActive'])->name('getActive');
        });

        Route::group(['prefix' => '/planos', 'as' => 'colorAuto.'], function () {
            Route::get('/consultar-pagamento/{payment}', [PlanController::class, 'getHistoryPayment'])->name('getHistoryPayment');
        });

        Route::group(['middleware' => ['can:manage-rent'], 'prefix' => '/aluguel', 'as' => 'rent.'], function () {
            Route::group(['prefix' => '/configuracao', 'as' => 'setting.'], function () {
                Route::get('/buscar/{store}', [SettingController::class, 'searchSetting'])->name('search');
                Route::post('/salvar', [SettingController::class, 'update'])->name('update');
            });
            Route::group(['prefix' => '/local', 'as' => 'place.'], function () {
                Route::post('/buscar', [PlaceController::class, 'fetchPlaces'])->name('fetch');
            });
            Route::group(['prefix' => '/grupo', 'as' => 'group.'], function () {
                Route::post('/buscar', [GroupController::class, 'fetchGroups'])->name('fetch');
            });
            Route::group(['prefix' => '/automovel', 'as' => 'automobile.'], function () {
                Route::post('/buscar', [RentAutoController::class, 'fetchAutomobile'])->name('fetch');
                Route::post('/upload-processar', [RentAutoController::class, 'setUploadImage'])->name('setUploadImage');
                Route::delete('/upload-reverter', [RentAutoController::class, 'rmUploadImage'])->name('rmUploadImage');
                Route::get('/upload-buscar/{auto}', [RentAutoController::class, 'getUploadImage'])->name('getUploadImage');
            });
            Route::group(['prefix' => '/caracteristica', 'as' => 'characteristic.'], function () {
                Route::get('/buscar/{tipo_auto}/loja/{store}', [CharacteristicController::class, 'getCharacteristics'])->name('getCharacteristics');
                Route::get('/buscar/{tipo_auto}/loja/{store}/{auto_id}', [CharacteristicController::class, 'getCharacteristicsByAuto'])->name('getCharacteristicsByAuto');
                Route::get('/buscar_caracteristica/{id}', [CharacteristicController::class, 'getCharacteristic'])->name('get');
                Route::post('/buscar', [CharacteristicController::class, 'fetchCharacteristicData'])->name('fetch');
                Route::post('/cadastrar', [CharacteristicController::class, 'insert'])->name('insert');
                Route::put('/atualizar', [CharacteristicController::class, 'update'])->name('update');
            });
            Route::group(['prefix' => '/valores', 'as' => 'wallet.'], function () {
                Route::get('/{id}', [WalletController::class, 'getByAuto'])->name('getByAuto');
                Route::post('/atualizar', [WalletController::class, 'update'])->name('update');
            });
        });

        Route::group(['prefix' => '/aplicativos', 'as' => 'application.'], function () {
            Route::get('/consultar-todos/{store}', [ApplicationController::class, 'searchAppsStore'])->name('searchAppsStore');
            Route::post('/instala-desinstala-app', [ApplicationController::class, 'setInstallApp'])->name('setInstallApp');
        });

    });
});
