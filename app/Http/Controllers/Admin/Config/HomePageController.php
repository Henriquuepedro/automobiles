<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Models\Config\ControlPageHome;
use App\Models\Config\OrderPageHome;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    private $orderPageHome;
    private $controlPageHome;

    public function __construct(OrderPageHome $orderPageHome, ControlPageHome $controlPageHome)
    {
        $this->orderPageHome = $orderPageHome;
        $this->controlPageHome = $controlPageHome;
    }

    public function homePage()
    {
        $controlPages = $this->controlPageHome->getControlPagesActived();

        return view('auth.config.homePage', compact('controlPages'));
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

        // removo todos os registro para atualizar
        $this->orderPageHome->removeAllOrderPagesActived();

        // insiro os novos registro
        foreach ($request->orderIds as $order => $orderId) {
            $this->orderPageHome->insert(array(
                'page_id'   => $orderId,
                'order'     => $order
            ));
        }

        return response()->json(array(
            'success' => true,
            'message' => 'Dados atualizados com sucesso!'
        ));
    }
}
