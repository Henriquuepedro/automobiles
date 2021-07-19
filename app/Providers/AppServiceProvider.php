<?php

namespace App\Providers;

use App\Models\Store;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;

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

        if (Request::getHttpHost() === 'admin') { // settings admin

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

            // consultar dominio do banco para identificar a loja
            $store = new Store();
            $dataStore = $store->getStoreByDomain($hostShared, $nameHostShared);

            $settings->logotipo = asset("admin/dist/images/stores/$dataStore->id/$dataStore->store_logo");
            $settings->storeName = $dataStore->store_fancy;
            $settings->storeEmail = $dataStore->contact_email;
            $settings->storePhonePrimary = empty($dataStore->contact_primary_phone) ? '' : Controller::formatPhone($dataStore->contact_primary_phone);
            $settings->storePhoneSecondary = empty($dataStore->contact_secondary_phone) ? '' : Controller::formatPhone($dataStore->contact_secondary_phone);
            $settings->storeWhatsPhonePrimary = $dataStore->contact_primary_phone_have_whatsapp == 1;
            $settings->storeWhatsPhoneSecondary = $dataStore->contact_secondary_phone_have_whatsapp == 1;

            $settings->address = "{$dataStore->address_public_place}, {$dataStore->address_number} - {$dataStore->address_zipcode} - {$dataStore->address_neighborhoods} - {$dataStore->address_city}/{$dataStore->address_state}";

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
