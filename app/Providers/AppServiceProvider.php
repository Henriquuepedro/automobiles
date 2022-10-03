<?php

namespace App\Providers;

use App\Models\Config\PageDynamic;
use App\Models\Store;
use App\Models\Company;
use DateTime;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;
use StdClass;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind('path.public', function() {
            return base_path().'/public_html';
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        $settings = new StdClass();
        if (Request::userAgent() === "Symfony") {
            return view()->share('settings', $settings);
        }

        $hostCompartilhado = env('SHARED_DOMAIN_PUBLIC');
        //$hostCompartilhado = ['pedrohenrique', 'net'];

        $settings    = new StdClass();
        $company     = new Company();

        $path = explode('/', Request::path());

        if (count($path) > 0  && $path[0] === 'admin') { // settings admin

            view()->composer('*', function ($view) use ($company)
            {
                if (Auth::user() !== null) {
                    $dataCompany = $company->getCompany(Auth::user()->company_id);
                    $config = App::make('config');

                    $planExpirationDate = new DateTime($dataCompany->plan_expiration_date);
                    $dateNow            = new DateTime();
                    $view->with('settings',
                        (object)[
                            'system' => (object)array(
                                'name'  => $config->get('app.name')
                            ),
                            'company' => (object)[
                                'fancy'                      => $dataCompany->company_fancy,
                                'plan_expiration_date'       => $planExpirationDate->format('d/m/y'),
                                'plan_expiration_date_color' => $planExpirationDate->diff($dateNow)->days >= 15 ? 'text-white' : ($planExpirationDate->diff($dateNow)->days < 7 ? 'text-red' : 'text-orange')
                            ]
                        ]
                    );

                    if (!$dataCompany || Auth::check() && Auth::user()->active != 1) {
                        Auth::logout();
                        abort(redirect('admin/login'));
                    }

                    // check store plano expirado
                    if ($planExpirationDate->getTimestamp() < $dateNow->getTimestamp() && $view->getName() !== 'admin.lockscreen') {
                        abort(redirect('admin/bloqueado'));
                    }
                    elseif ($planExpirationDate->getTimestamp() > $dateNow->getTimestamp() && $view->getName() === 'admin.lockscreen') {
                        abort(redirect('admin'));
                    }
                }
            });

        }
        else { // settings client
            $host = Request::getHttpHost();
            $hostShared = false;

            $parseHost   = parse_url($host);
            $parseShared = parse_url($hostCompartilhado);
            $expHost     = explode('.', $parseHost['host'] ?? $parseHost['path']);

            $nameHostStore = $parseHost['host'] ?? $parseHost['path'];
            if (array_key_exists('port', $parseHost)) {
                $nameHostStore .= ":{$parseHost['port']}";
            }

            if (count($expHost) === 3) {
                $nameHostStore = $expHost[0];
                array_shift($expHost);
                $impHost = implode('.', $expHost);

                if ($impHost === ($parseShared['host'] ?? $parseShared['path'])) {
                    $hostShared = true;
                }
            }

            $store       = new Store();
            $pageDynamic = new PageDynamic();

            // consultar domínio do banco para identificar a loja
            $dataStore = $store->getStoreByDomain($hostShared, $nameHostStore);

            // Verifica se encontrou a loja e se o plano está válido.
            if (!$dataStore || strtotime($dataStore->plan_expiration_date) < strtotime(date('Y-m-d'))) {
                abort(404);
            }

            $pagesDynamic = $pageDynamic->getPageActive($dataStore->id);

            $settings->pages = $pagesDynamic;

            $settings->baseUrl = $host;

            $settings->logotipo                 = asset("assets/admin/dist/images/stores/$dataStore->id/$dataStore->store_logo");
            $settings->storeName                = $dataStore->store_fancy;
            $settings->storeEmail               = $dataStore->contact_email;
            $settings->storePhonePrimary        = empty($dataStore->contact_primary_phone) ? '' : Controller::formatPhone($dataStore->contact_primary_phone);
            $settings->storePhonePrimary_n      = empty($dataStore->contact_primary_phone) ? '' : $dataStore->contact_primary_phone;
            $settings->storePhoneSecondary      = empty($dataStore->contact_secondary_phone) ? '' : Controller::formatPhone($dataStore->contact_secondary_phone);
            $settings->storePhoneSecondary_n    = empty($dataStore->contact_secondary_phone) ? '' : $dataStore->contact_secondary_phone;
            $settings->storeWhatsPhonePrimary   = $dataStore->contact_primary_phone_have_whatsapp == 1;
            $settings->storeWhatsPhoneSecondary = $dataStore->contact_secondary_phone_have_whatsapp == 1;
            $settings->address                  = "$dataStore->address_public_place, $dataStore->address_number - $dataStore->address_zipcode - $dataStore->address_neighborhoods - $dataStore->address_city/$dataStore->address_state";
            $settings->shortAbout               = str_replace("\n", '<br />', $dataStore->short_store_about);
            $settings->descriptionService       = $dataStore->description_service;

            $settings->socialNetworks = array();
            if (!empty($dataStore->social_networks)) {
                foreach (json_decode($dataStore->social_networks) as $network) {
                    $settings->socialNetworks[] = array(
                        'network' => $network->type,
                        'link' => $network->value
                    );
                }
            }
        }

        view()->share('settings', $settings);
    }
}
