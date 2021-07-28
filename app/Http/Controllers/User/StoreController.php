<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Store;

class StoreController extends Controller
{
    private $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    /**
     * Retorna dados da loja
     *
     * @return  JsonResponse
     */
    public function getStore(): JsonResponse
    {
        return response()->json($this->store->getStoreByStore($this->getStoreDomain()));
    }
}
