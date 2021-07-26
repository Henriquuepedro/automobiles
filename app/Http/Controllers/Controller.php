<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\UsersToStores;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public static function isAjax(){
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    public static function formatPhone($value){
        $tel = $value;

        if(strlen($value) == 10) $tel = preg_replace("/([0-9]{2})([0-9]{4})([0-9]{4})/", "($1) $2-$3", $value);
        elseif(strlen($value) == 11) $tel = preg_replace("/([0-9]{2})([0-9]{5})([0-9]{4})/", "($1) $2-$3", $value);

        return $tel;
    }

    /**
     * @return array
     */
    public static function getStoresByUsers(): array
    {
        $stores = array();

        foreach (UsersToStores::getStoreByUser(Auth::user()->id) as $data)
            array_push($stores, $data->store_id);

        return $stores;
    }

    public function getStoreDomain()
    {
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
        return $dataStore->id ?? null;
    }
}
