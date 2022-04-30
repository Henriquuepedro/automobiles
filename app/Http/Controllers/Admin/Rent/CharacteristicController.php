<?php

namespace App\Http\Controllers\Admin\Rent;

use App\Http\Controllers\Controller;
use App\Models\Fipe\ControlAutos;
use App\Models\RentAutoToCharacteristic;
use App\Models\RentCharacteristic;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CharacteristicController extends Controller
{
    private RentCharacteristic $rentCharacteristic;
    private RentAutoToCharacteristic $rentAutoToCharacteristic;
    private Store $store;
    private ControlAutos $controlAutos;

    public function __construct(RentCharacteristic $rentCharacteristic, RentAutoToCharacteristic  $rentAutoToCharacteristic, Store $store, ControlAutos $controlAutos)
    {
        $this->rentCharacteristic = $rentCharacteristic;
        $this->rentAutoToCharacteristic = $rentAutoToCharacteristic;
        $this->store = $store;
        $this->controlAutos = $controlAutos;
    }

    public function getCharacteristicsByAuto($tipo_auto, $store, $auto_id): JsonResponse
    {
        // Loja informada ou usuário não tem permissão
        if (!in_array($store, $this->getStoresByUsers())) {
            return response()->json([]);
        }

        $characteristics    = $this->rentCharacteristic->getCharacteristicsByType($tipo_auto, $store);
        $arrCharacteristic    = array();

        foreach ($characteristics as $characteristic) {
            $arrCharacteristic[] = array(
                'id'        => $characteristic->id,
                'name'      => $characteristic->name,
                'checked'   => (bool)$this->rentAutoToCharacteristic->getByAutoAndCharacteristic($auto_id, $characteristic->id)
            );
        }

        return response()->json($arrCharacteristic);
    }

    public function getCharacteristics($tipo_auto, $store): JsonResponse
    {
        // Loja informada ou usuário não tem permissão
        if (!in_array($store, $this->getStoresByUsers())) {
            return response()->json([]);
        }

        $characteristics    = $this->rentCharacteristic->getCharacteristicsByType($tipo_auto, $store);
        $arrCharacteristic    = array();

        foreach ($characteristics as $characteristic) {
            $arrCharacteristic[] = array(
                'id'        => $characteristic->id,
                'name'      => $characteristic->name,
                'checked'   => false
            );
        }

        return response()->json($arrCharacteristic);
    }

    public function getCharacteristic(int $id)
    {
        $response = $this->rentCharacteristic->getCharacteristic($id);

        if (!in_array($response->store_id, $this->getStoresByUsers())) {
            return [];
        }

        return response()->json($response);
    }

    /*
    public function insert(int $auto, array $characteristics): bool
    {
        foreach ($characteristics as $characteristic) {
            $this->rentAutoToCharacteristic->insert(array(
                'auto_id'           => $auto,
                'characteristic_id' => $characteristic
            ));
        }

        return true;
    }

    public function edit(int $auto, array $characteristics): bool
    {
        $this->rentAutoToCharacteristic->removeByAuto($auto);

        foreach ($characteristics as $characteristic) {
            $this->rentAutoToCharacteristic->insert(array(
                'auto_id'           => $auto,
                'characteristic_id' => $characteristic
            ));
        }

        return true;
    }
    */

    public function index()
    {
        $stores         = $this->store->getStores($this->getStoresByUsers());
        $controlAutos   = $this->controlAutos->getAllControlsActive();

        return view('admin.register.characteristic.index', compact('stores', 'controlAutos'));

    }

    public function fetchCharacteristicData(Request $request): JsonResponse
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

            $fieldsOrder = array('name','type_auto','active','');
            if (count($store_id) > 1) {
                $fieldsOrder[3] = 'store_id';
                $fieldsOrder[4] = '';
            }
            $fieldOrder =  $fieldsOrder[$request->input('order')[0]['column']];
            if ($fieldOrder != "") {
                $orderBy['field'] = $fieldOrder;
                $orderBy['order'] = $direction;
            }
        }

        $data = $this->rentCharacteristic->getCharacteristics($filters, $ini, $length, $orderBy);

        foreach ($data as $key => $value) {

            $activeColor    = $value['active'] ? 'success' : 'danger';
            $activeLabel    = $value['active'] ? 'Ativo' : 'Inativo';
            $active         = "<div class='badge badge-pill badge-lg badge-$activeColor w-100'>$activeLabel</div>";
            $button         = "<button class='btn btn-primary btn-flat btn-sm editCharacteristic' characteristic-id='{$value['id']}'><i class='fa fa-edit'></i></button>";

            $array = array(
                $value['name'],
                $value['type_auto'] === 'all' ? 'Todos' : ucfirst($value['type_auto']),
                $active
            );

            if (count($this->getStoresByUsers()) > 1) {
                $array[] = $value['store_fancy'];
            }

            $array[] = $button;

            $result[$key] = $array;
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $this->rentCharacteristic->getCountCharacteristics($filters, false),
            "recordsFiltered" => $this->rentCharacteristic->getCountCharacteristics($filters),
            "data" => $result
        );

        return response()->json($output);
    }

    public function insert(Request $request): JsonResponse
    {
        $name       = filter_var($request->input('name'), FILTER_SANITIZE_STRING);
        $active     = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);
        $typeAuto   = filter_var($request->input('typeAuto'), FILTER_SANITIZE_STRING);

        // Loja informada ou usuário não tem permissão.
        if (!$request->has('stores') || !in_array($request->input('stores'), $this->getStoresByUsers())) {
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível identificar a loja informada!'
            ));
        }

        if ($this->rentCharacteristic->getCharacteristicByName($name, $request->input('stores'))) {
            return response()->json(array(
                'success' => false,
                'message' => 'Nome da característica já está em uso!'
            ));
        }

        $create = $this->rentCharacteristic->insert(array(
            'name'          => $name,
            'active'        => $active,
            'type_auto'     => $typeAuto,
            'user_insert'   => $request->user()->id,
            'company_id'    => $this->store->getCompanyByStore($request->input('stores')),
            'store_id'      => $request->input('stores')
        ));

        if (!$create) {
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível cadastrar. Tente novamente mais tarde!'
            ));
        }

        return response()->json(array(
            'success' => true,
            'message' => 'Cadastrado com sucesso!',
            'characteristic_id' => $create->id
        ));
    }

    public function update(Request $request): JsonResponse
    {
        $name               = filter_var($request->input('name'), FILTER_SANITIZE_STRING);
        $characteristicId   = filter_var($request->input('characteristicId'), FILTER_VALIDATE_INT);
        $active             = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);
        $typeAuto           = filter_var($request->input('typeAuto'), FILTER_SANITIZE_STRING);

        if (!$this->rentCharacteristic->getCharacteristic($characteristicId)) {
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível localizar a característica. Tente novamente mais tarde!'
            ));
        }

        // Loja informada ou usuário não tem permissão.
        if (!$request->has('stores') || !in_array($request->input('stores'), $this->getStoresByUsers())) {
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível identificar a loja informada!'
            ));
        }

        if ($this->rentCharacteristic->getCharacteristicByName($name, $request->input('stores'), $characteristicId)) {
            return response()->json(array(
                'success' => false,
                'message' => 'Nome da característica já está em uso!'
            ));
        }


        $update = $this->rentCharacteristic->edit(array(
            'name'          => $name,
            'active'        => $active,
            'type_auto'     => $typeAuto,
            'user_update'   => $request->user()->id,
            'company_id'    => $this->store->getCompanyByStore($request->input('stores')),
            'store_id'      => $request->input('stores')
        ), $characteristicId);

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
}
