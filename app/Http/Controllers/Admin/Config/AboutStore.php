<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AboutStore extends Controller
{
    private $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function index()
    {
        $stores = $this->store->getStores($this->getStoresByUsers());
        return view('admin.config.about.index', compact('stores'));
    }

    public function getAboutStore(int $store): JsonResponse
    {
        $dataStore = $this->store->getStoreByStore($store);

        // loja informado o usuário não tem permissão
        if (!in_array($dataStore->id, $this->getStoresByUsers()))
            return response()->json('');

        return response()->json(array('long' => $dataStore->store_about, 'short' => $dataStore->short_store_about));
    }

    public function update(Request $request): JsonResponse
    {
        // loja informado o usuário não tem permissão
        if (!in_array($request->stores, $this->getStoresByUsers()))
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível identificar a loja informada!'
            ]);

        $update = $this->store->edit([
            'store_about' => $request->conteudo,
            'short_store_about' => $request->shortAbout
        ], $request->stores, $request->user()->company_id);

        if($update)
            return response()->json([
                'success' => true,
                'message' => 'Sobre a loja atualizada com sucesso!'
            ]);

        return response()->json([
            'success' => false,
            'message' => 'Sobre a loja não pode ser atualizada, tente novamente!'
        ]);
    }
}
