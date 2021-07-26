<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Models\Config\ControlPageHome;
use App\Models\Config\OrderPageHome;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    private $orderPageHome;
    private $controlPageHome;
    private $store;

    public function __construct(OrderPageHome $orderPageHome, ControlPageHome $controlPageHome, Store $store)
    {
        $this->orderPageHome = $orderPageHome;
        $this->controlPageHome = $controlPageHome;
        $this->store = $store;
    }

    public function homePage()
    {
        $stores         = $this->store->getStores($this->getStoresByUsers());

        return view('admin.config.homePage', compact('stores'));
    }

    public function updateOrder(Request $request): JsonResponse
    {
        // verifica se todos os ids, sao válidos
        foreach ($request->orderIds as $order) {
            if (!$this->controlPageHome->getControlById($order))
                return response()->json(array(
                    'success' => false,
                    'message' => 'Não foi possível salvar sua alteração. Tente novamente mais tarde!'
                ));
        }

        // loja informado o usuário não tem permissão
        if (!isset($request->stores) || !in_array($request->stores, $this->getStoresByUsers()))
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível identificar a loja informada!'
            ));

        // removo todos os registro para atualizar
        $this->orderPageHome->removeAllOrderPagesActived($request->stores);

        // insiro os novos registro
        foreach ($request->orderIds as $order => $orderId) {
            $this->orderPageHome->insert(array(
                'page_id'    => $orderId,
                'order'      => $order,
                'company_id' => $request->user()->company_id,
                'store_id'   => $request->stores
            ));
        }

        return response()->json(array(
            'success' => true,
            'message' => 'Dados atualizados com sucesso!'
        ));
    }

    public function getConfigHomePageByStore(int $store)
    {
        // loja informado o usuário não tem permissão
        if (!in_array($store, $this->getStoresByUsers()))
            return response()->json(array());

        $controlPages   = $this->controlPageHome->getControlPagesActived($store);

        $inactives = array();
        $actives = array();
        foreach($controlPages as $control) {
            if($control['order'] === null)
                array_push($inactives, array('order' => $control['id'], 'name'  => $control['nome']));
            if ($control['order'] !== null)
                array_push($actives, array('order' => $control['id'], 'name'  => $control['nome']));
        }

        return response()->json(array(
            'inactives' => $inactives,
            'actives' => $actives
        ));
    }
}
