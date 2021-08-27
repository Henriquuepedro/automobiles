<?php

namespace App\Providers;

use App\Models\Config\Banner;
use App\Models\Config\PageDynamic;
use App\Models\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;
use App;

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

        $settings = new \StdClass();

        $path = explode('/', Request::path());

        if (count($path) > 0  && $path[0] === 'admin') { // settings admin

            view()->composer('*', function ($view)
            {
                if (Auth::check() && Auth::user()->active != 1) {
                    Auth::logout();
                    abort(redirect('/login'));
                }
            });

        } else { // settings cliente

            $host = Request::getHttpHost();
            $expHost = explode('.', $host);
            $hostShared = false;
            $nameHostShared = null;

            if (count($expHost) === 3) { // host compartilhado
                $hostShared = true;
                $nameHostShared = $expHost[0];
            } elseif (count($expHost) === 2) { // host proprio
                $nameHostShared = $host;
            }

            $store       = new Store();
            $pageDynamic = new PageDynamic();

            // consultar dominio do banco para identificar a loja
            $dataStore = $store->getStoreByDomain($hostShared, $nameHostShared);
            $pagesDynamic = $pageDynamic->getPageActive($dataStore->id);

            $settings->pages                    = $pagesDynamic;

            $settings->baseUrl                  = $host;

            $settings->logotipo                 = asset("assets/admin/dist/images/stores/{$dataStore->id}/{$dataStore->store_logo}");
            $settings->storeName                = $dataStore->store_fancy;
            $settings->storeEmail               = $dataStore->contact_email;
            $settings->storePhonePrimary        = empty($dataStore->contact_primary_phone) ? '' : Controller::formatPhone($dataStore->contact_primary_phone);
            $settings->storePhonePrimary_n      = empty($dataStore->contact_primary_phone) ? '' : $dataStore->contact_primary_phone;
            $settings->storePhoneSecondary      = empty($dataStore->contact_secondary_phone) ? '' : Controller::formatPhone($dataStore->contact_secondary_phone);
            $settings->storePhoneSecondary_n    = empty($dataStore->contact_secondary_phone) ? '' : $dataStore->contact_secondary_phone;
            $settings->storeWhatsPhonePrimary   = $dataStore->contact_primary_phone_have_whatsapp == 1;
            $settings->storeWhatsPhoneSecondary = $dataStore->contact_secondary_phone_have_whatsapp == 1;
            $settings->address                  = "{$dataStore->address_public_place}, {$dataStore->address_number} - {$dataStore->address_zipcode} - {$dataStore->address_neighborhoods} - {$dataStore->address_city}/{$dataStore->address_state}";
            $settings->shortAbout               = str_replace("\n", '<br />', $dataStore->short_store_about);
            $settings->descriptionService       = $dataStore->description_service;

            $settings->socialNetworks = array();
            if (!empty($dataStore->social_networks)) {
                foreach (json_decode($dataStore->social_networks) as $network) {
                    array_push($settings->socialNetworks, array(
                        'network'   => $network->type,
                        'link'      => $network->value
                    ));
                }
            }
        }

        view()->share('settings', $settings);
    }
}
