<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Automobile\ColorAuto;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    private ColorAuto $colorAuto;
    private Store $store;

    public function __construct(ColorAuto $colorAuto, Store $store)
    {
        $this->colorAuto = $colorAuto;
        $this->store = $store;
    }

    public function index()
    {
        $stores = $this->store->getStores($this->getStoresByUsers());

        return view('admin.register.color.index', compact('stores'));
    }

    public function fetchColorData(Request $request): JsonResponse
    {
        $orderBy    = array();
        $result     = array();

        $filters        = [];
        $ini            = $request->input('start');
        $draw           = $request->input('draw');
        $length         = $request->input('length');
        // Filtro do front
        $store_id   = null;

        // valida se usuário pode ver a loja
        if (!empty($request->input('store_id')) && !in_array($request->input('store_id'), $this->getStoresByUsers())) {
            return response()->json(array());
        }

        if (!empty($request->input('store_id')) && !is_array($request->input('store_id'))) {
            $store_id = array($request->input('store_id'));
        }

        if ($request->input('store_id') === null) {
            $store_id = $this->getStoresByUsers();
        }

        $filters['store_id'] = $store_id;
        $filters['value'] = null;

        $search = $request->input('search');
        if ($search['value']) {
            $filters['value'] = $search['value'];
        }

        if ($request->has('order')) {
            if ($request->input('order')[0]['dir'] == "asc") {
                $direction = "asc";
            }
            else {
                $direction = "desc";
            }

            $fieldsOrder = array('nome','created_at','active','store_id','');
            $fieldOrder =  $fieldsOrder[$request->input('order')[0]['column']];
            if ($fieldOrder != "") {
                $orderBy['field'] = $fieldOrder;
                $orderBy['order'] = $direction;
            }
        }

        $data = $this->colorAuto->getColorsFetch($filters, $ini, $length, $orderBy);

        foreach ($data as $key => $value) {

            $activeColor    = $value['active'] ? 'success' : 'danger';
            $activeLabel    = $value['active'] ? 'Ativo' : 'Inativo';
            $active         = "<div class='badge badge-pill badge-lg badge-$activeColor w-100'>$activeLabel</div>";
            $button         = $value['store_fancy'] ? "<button class='btn btn-primary btn-flat btn-sm editColor' color-id='{$value['id']}'><i class='fa fa-edit'></i></button>" : '';

            $result[$key] = array(
                $value['nome'],
                date('d/m/Y H:i', strtotime($value['created_at'])),
                $active,
                $value['store_fancy'] ?? 'System',
                $button
            );
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $this->colorAuto->getCountColorsFetch($filters, false),
            "recordsFiltered" => $this->colorAuto->getCountColorsFetch($filters),
            "data" => $result
        );

        return response()->json($output);
    }

    public function insert(Request $request): JsonResponse
    {
        $name   = filter_var($request->input('name'), FILTER_SANITIZE_STRING);
        $active = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        // Loja informada ou usuário não tem permissão
        if (!$request->has('stores') || !in_array($request->input('stores'), $this->getStoresByUsers())) {
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível identificar a loja informada!'
            ));
        }

        if ($this->colorAuto->getColorByName($name, $request->input('stores'))) {
            return response()->json(array(
                'success' => false,
                'message' => 'Nome da cor já está em uso!'
            ));
        }

        $create = $this->colorAuto->insert(array(
            'nome'          => $name,
            'active'        => $active,
            'user_insert'   => $request->user()->id,
            'store_id'      => $request->input('stores')
        ));

        if (!$create) {
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível cadastrar. Tente novamente mais tarde!'
            ));
        }

        return response()->json(array(
            'success'   => true,
            'message'   => 'Cadastrado com sucesso!',
            'color_id'  => $create->id
        ));
    }

    public function update(Request $request): JsonResponse
    {
        $name       = filter_var($request->input('name'), FILTER_SANITIZE_STRING);
        $colorId    = filter_var($request->input('colorId'), FILTER_VALIDATE_INT);
        $active     = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        // Loja informada ou usuário não tem permissão
        if (!$request->has('stores') || !in_array($request->input('stores'), $this->getStoresByUsers())) {
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível identificar a loja informada!'
            ));
        }

        if (!$this->colorAuto->getColorByStore($colorId, $request->input('stores'))) {
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível localizar a cor. Tente novamente mais tarde!'
            ));
        }

        if ($this->colorAuto->getColorByName($name, $request->input('stores'), $colorId)) {
            return response()->json(array(
                'success' => false,
                'message' => 'Nome da cor já está em uso!'
            ));
        }

        $update = $this->colorAuto->edit(array(
            'nome'          => $name,
            'active'        => $active,
            'user_update'   => $request->user()->id,
            'store_id'      => $request->input('stores')
        ), $colorId);

        if (!$update) {
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível atualizar. Tente novamente mais tarde!'
            ));
        }

        return response()->json(array(
            'success' => true,
            'message' => 'Atualizado com sucesso!'
        ));
    }

    public function getColor(int $id)
    {
        $response = $this->colorAuto->getColorById($id);

        if (!in_array($response->store_id, $this->getStoresByUsers())) {
            return [];
        }

        return response()->json($response);
    }

    public function getColorsActive(int $store)
    {
        if (!in_array($store, $this->getStoresByUsers())) {
            return [];
        }

        return response()->json($this->colorAuto->getColorsActiveByStore($store));
    }
}
