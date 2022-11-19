<?php

namespace App\Http\Controllers\Admin\Rent;

use App\Http\Controllers\Controller;
use App\Models\Rent\RentSetting;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    private Store $store;
    private RentSetting $rentSetting;

    public function __construct(Store $store, RentSetting $rentSetting)
    {
        $this->store = $store;
        $this->rentSetting = $rentSetting;
    }

    public function index()
    {
        $stores = $this->store->getStores($this->getStoresByUsers());
        return view('admin.rent.setting.index', compact('stores'));
    }

    public function update(Request $request)
    {
        $visibleType = (bool)$request->input('visible_type');
        $store       = (int)$request->input('stores');

        // Loja informada ou usuário não tem permissão.
        if (!in_array($store, $this->getStoresByUsers())) {
            return response()->json(array(
                'success' => false,
                'message' => 'Loja não localizada!'
            ));
        }

        if ($this->rentSetting->updateByStore($store, array('visible_type' => $visibleType))) {
            return response()->json(array(
                'success' => true,
                'message' => 'Configuração atualizada com sucesso!'
            ));
        }

        return response()->json(array(
            'success' => false,
            'message' => 'Configuração não pode ser atualizada, tente mais tarde!'
        ));
    }

    public function searchSetting(int $store): JsonResponse
    {
        // Loja informada ou usuário não tem permissão.
        if (!in_array($store, $this->getStoresByUsers())) {
            return response()->json('');
        }

        $settingRent = $this->rentSetting->getByStore($store);

        if (!$settingRent) {
            $this->rentSetting->insert(array(
                'company_id'    => $this->store->getCompanyByStore($store),
                'store_id'      => $store,
                'user_updated'  => Auth::user()->id
            ));
            $settingRent = $this->rentSetting->getByStore($store);
        }

        return response()->json($settingRent);
    }
}
