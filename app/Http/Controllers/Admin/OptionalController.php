<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Automobile\Optional;
use App\Models\Fipe\ControlAutos;
use App\Models\Optionals;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class OptionalController extends Controller
{
    private Optional $opcional;
    private Optionals $optionals;
    private Store $store;
    private ControlAutos $controlAutos;

    public function __construct(Optionals $optionals, Optional $opcional, Store $store, ControlAutos $controlAutos)
    {
        $this->opcional     = $opcional;
        $this->optionals    = $optionals;
        $this->store        = $store;
        $this->controlAutos = $controlAutos;
    }

    public function getOptionals($tipo_auto, $store): JsonResponse
    {
        // Loja informada ou usuário não tem permissão
        if (!in_array($store, $this->getStoresByUsers())) {
            return response()->json([]);
        }

        $optionals = $this->optionals->getOptionalsByType($tipo_auto, $store);
        $arrOptional = array();

        foreach ($optionals as $optional) {
            array_push($arrOptional, array(
                'id'        => $optional->id,
                'nome'      => $optional->nome,
                'checked'   => false,
            ));
        }

        return response()->json($arrOptional);
    }

    public function getOptionalsByAuto($tipo_auto, $store, $auto_id): JsonResponse
    {
        // Loja informada ou usuário não tem permissão
        if (!in_array($store, $this->getStoresByUsers())) {
            return response()->json([]);
        }

        $optionals      = $this->optionals->getOptionalsByType($tipo_auto, $store);
        $optionalAuto   = (array)json_decode($this->opcional->getOptionalByAuto($auto_id)->valores ?? '{}');
        $arrOptional    = array();

        foreach ($optionals as $optional) {
            array_push($arrOptional, array(
                'id'        => $optional->id,
                'nome'      => $optional->nome,
                'checked'   => in_array($optional->id, $optionalAuto),
            ));
        }

        return response()->json($arrOptional);
    }

    public function list()
    {
        $stores         = $this->store->getStores($this->getStoresByUsers());
        $controlAutos   = $this->controlAutos->getAllControlsActive();

        return view('admin.register.optionals.index', compact('stores', 'controlAutos'));

    }

    public function insert(Request $request): JsonResponse
    {
        $name           = filter_var($request->input('name'), FILTER_SANITIZE_STRING);
        $typeAuto       = filter_var($request->input('typeAuto'), FILTER_SANITIZE_STRING);
        $active         = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        // Loja informada ou usuário não tem permissão
        if (!$request->has('stores') || !in_array($request->input('stores', array()), $this->getStoresByUsers())) {
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível identificar a loja informada!'
            ));
        }

        if ($this->optionals->getOptionalByName($name, $request->input('stores'))) {
            return response()->json(array(
                'success' => false,
                'message' => 'Nome do opcional já está em uso!'
            ));
        }

        $create = $this->optionals->insert(array(
            'nome'          => $name,
            'tipo_auto'     => $typeAuto,
            'ativo'         => $active,
            'user_insert'   => $request->user()->id,
            'company_id'    => $request->user()->company_id,
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
            'optional_id' => $create->id
        ));
    }

    public function update(Request $request): JsonResponse
    {
        $name       = filter_var($request->input('name'), FILTER_SANITIZE_STRING);
        $typeAuto   = filter_var($request->input('typeAuto'), FILTER_SANITIZE_STRING);
        $optionalId = filter_var($request->input('optionalId'), FILTER_VALIDATE_INT);
        $active     = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        if (!$this->optionals->getOptional($optionalId)) {
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível localizar o complementar. Tente novamente mais tarde!'
            ));
        }

        // Loja informada ou usuário não tem permissão
        if (!$request->has('stores') || !in_array($request->input('stores', array()), $this->getStoresByUsers())) {
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível identificar a loja informada!'
            ));
        }

        if ($this->optionals->getOptionalByName($name, $request->input('stores'), $optionalId)) {
            return response()->json(array(
                'success' => false,
                'message' => 'Nome do complementar já está em uso!'
            ));
        }

        $update = $this->optionals->edit(array(
            'nome'          => $name,
            'tipo_auto'     => $typeAuto,
            'ativo'         => $active,
            'user_update'   => $request->user()->id,
            'company_id'    => $request->user()->company_id,
            'store_id'      => $request->input('stores')
        ), $optionalId);

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

    public function getOptional(int $id)
    {
        $response = $this->optionals->getoptional($id);

        if (!in_array($response->store_id, $this->getStoresByUsers())) {
            return [];
        }

        return response()->json($response);
    }

    public function fetchOptionalData(Request $request): JsonResponse
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

            $fieldsOrder = array('nome','tipo_auto','ativo','');
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

        $data = $this->optionals->getOptionals($filters, $ini, $length, $orderBy);

        foreach ($data as $key => $value) {

            $activeColor    = $value['ativo'] ? 'success' : 'danger';
            $activeLabel    = $value['ativo'] ? 'Ativo' : 'Inativo';
            $active         = "<div class='badge badge-pill badge-lg badge-$activeColor w-100'>$activeLabel</div>";
            $button         = "<button class='btn btn-primary btn-flat btn-sm editOptional' optional-id='{$value['id']}'><i class='fa fa-edit'></i></button>";

            $array = array(
                $value['nome'],
                $value['tipo_auto'] === 'all' ? 'Todos' : ucfirst($value['tipo_auto']),
                $active
            );

            if (count($this->getStoresByUsers()) > 1) {
                array_push($array, $value['store_fancy']);
            }

            array_push($array, $button);

            $result[$key] = $array;
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $this->optionals->getCountOptionals($filters, false),
            "recordsFiltered" => $this->optionals->getCountOptionals($filters),
            "data" => $result
        );

        return response()->json($output);
    }
}
