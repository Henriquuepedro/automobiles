<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Automobile\ComplementaryAuto;
use App\Models\ComplementaryAutos;
use App\Models\Fipe\ControlAutos;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComplementaryController extends Controller
{
    private ComplementaryAuto $complementAuto;
    private ComplementaryAutos $complementAutos;
    private Store $store;
    private ControlAutos $controlAutos;

    public function __construct(ComplementaryAuto $complementAuto, ComplementaryAutos $complementAutos, Store $store, ControlAutos $controlAutos)
    {
        $this->complementAuto   = $complementAuto;
        $this->complementAutos  = $complementAutos;
        $this->store            = $store;
        $this->controlAutos     = $controlAutos;
    }

    public function getComplemenetares($tipo_auto, $store): JsonResponse
    {
        // Loja informada ou usuário não tem permissão
        if (!in_array($store, $this->getStoresByUsers())) {
            return response()->json([]);
        }

        $complementes = $this->complementAutos->getComplementaresByType($tipo_auto, $store);
        $arrComplement = array();

        foreach ($complementes as $complement) {
            array_push($arrComplement, array(
                'id'                => $complement->id,
                'nome'              => $complement->nome,
                'tipo_campo'        => $complement->tipo_campo,
                'valores_padrao'    => json_decode($complement->valores_padrao) ?? null,
                'valor_salvo'       => null
            ));
        }

        return response()->json($arrComplement);
    }

    public function getComplementaryByAuto($tipo_auto, $store, $auto_id): JsonResponse
    {
        // Loja informada ou usuário não tem permissão
        if (!in_array($store, $this->getStoresByUsers())) {
            return response()->json([]);
        }

        $complementes = $this->complementAutos->getComplementaresByType($tipo_auto, $store);
        $complementAuto = (array)json_decode($this->complementAuto->getComplementarByAuto($auto_id)->valores ?? '{}');
        $arrComplement = array();

        foreach ($complementes as $complement) {
            array_push($arrComplement, array(
                'id'                => $complement->id,
                'nome'              => $complement->nome,
                'tipo_campo'        => $complement->tipo_campo,
                'valores_padrao'    => json_decode($complement->valores_padrao) ?? null,
                'valor_salvo'       => $complementAuto[$complement->id] ?? null
            ));
        }

        return response()->json($arrComplement);
    }

    public function getDataFormatToInsert($dataForm, $codAutomovel): array
    {
        $arrComplements = array();
        foreach ($dataForm as $complement => $valueComplement) {
            if (preg_match('/.*?complement_.*?/', $complement) > 0) {
                $complementId = (int)str_replace('complement_', '', $complement);

                if (is_numeric($valueComplement)) {
                    $valueComplement = (int)$valueComplement;
                }

                if ($complementId != '' && $complementId != null) {
                    $arrComplements[$complementId] = empty($valueComplement) && $valueComplement !== 0 ? null : $valueComplement;
                }
            }
        }
        asort($arrComplements);

        return array(
            'auto_id'   => $codAutomovel,
            'valores'   => json_encode($arrComplements)
        );
    }

    public function list()
    {
        $stores          = $this->store->getStores($this->getStoresByUsers());
        $controlAutos    = $this->controlAutos->getAllControlsActive();

        return view('admin.register.complements.index', compact('stores', 'controlAutos'));
    }

    public function insert(Request $request): JsonResponse
    {
        $name           = filter_var($request->input('name'), FILTER_SANITIZE_STRING);
        $typeAuto       = filter_var($request->input('typeAuto'), FILTER_SANITIZE_STRING);
        $typeField      = filter_var($request->input('typeField'), FILTER_SANITIZE_STRING);
        $valuesDefault  = $typeField === 'select' ? json_encode($request->input('valuesDefault')) : null;
        $active         = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        // Loja informada ou usuário não tem permissão
        if (!$request->has('stores') || !in_array($request->input('stores', array()), $this->getStoresByUsers())) {
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível identificar a loja informada!'
            ));
        }

        if ($this->complementAutos->getComplementByName($name, $request->input('stores'))) {
            return response()->json(array(
                'success' => false,
                'message' => 'Nome do complementar já está em uso!'
            ));
        }

        $create = $this->complementAutos->insert(array(
            'nome'          => $name,
            'tipo_auto'     => $typeAuto,
            'tipo_campo'    => $typeField,
            'valores_padrao'=> $valuesDefault,
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
            'complement_id' => $create->id
        ));
    }

    public function update(Request $request): JsonResponse
    {
        $name           = filter_var($request->input('name'), FILTER_SANITIZE_STRING);
        $typeAuto       = filter_var($request->input('typeAuto'), FILTER_SANITIZE_STRING);
        $typeField      = filter_var($request->input('typeField'), FILTER_SANITIZE_STRING);
        $valuesDefault  = $typeField === 'select' ? json_encode($request->input('valuesDefault')) : null;
        $complementId   = filter_var($request->input('complementId'), FILTER_VALIDATE_INT);
        $active         = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        if (!$this->complementAutos->getComplement($complementId)) {
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

        if ($this->complementAutos->getComplementByName($name, $request->input('stores'), $complementId)) {
            return response()->json(array(
                'success' => false,
                'message' => 'Nome do opcional já está em uso!'
            ));
        }

        $update = $this->complementAutos->edit(array(
            'nome'          => $name,
            'tipo_auto'     => $typeAuto,
            'tipo_campo'    => $typeField,
            'valores_padrao'=> $valuesDefault,
            'ativo'         => $active,
            'user_update'   => $request->user()->id,
            'company_id'    => $request->user()->company_id,
            'store_id'      => $request->input('stores')
        ), $complementId);

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

    public function getComplement(int $id)
    {
        $response = $this->complementAutos->getComplement($id);

        if (!in_array($response->store_id, $this->getStoresByUsers())) {
            return [];
        }

        return response()->json($response);
    }

    public function fetchComplementData(Request $request): JsonResponse
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

            $fieldsOrder = array('nome','tipo_auto','tipo_campo','ativo','');
            if (count($store_id) > 1) {
                $fieldsOrder[4] = 'store_id';
                $fieldsOrder[5] = '';
            }
            $fieldOrder =  $fieldsOrder[$request->input('order')[0]['column']];
            if ($fieldOrder != "") {
                $orderBy['field'] = $fieldOrder;
                $orderBy['order'] = $direction;
            }
        }

        $data = $this->complementAutos->getComplements($filters, $ini, $length, $orderBy);

        foreach ($data as $key => $value) {

            $activeColor    = $value['ativo'] ? 'success' : 'danger';
            $activeLabel    = $value['ativo'] ? 'Ativo' : 'Inativo';
            $active         = "<div class='badge badge-pill badge-lg badge-$activeColor w-100'>$activeLabel</div>";
            $button         = "<button class='btn btn-primary btn-flat btn-sm editComplement' complement-id='{$value['id']}'><i class='fa fa-edit'></i></button>";

            $array = array(
                $value['nome'],
                $value['tipo_auto'] === 'all' ? 'Todos' : ucfirst($value['tipo_auto']),
                $value['tipo_campo'],
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
            "recordsTotal" => $this->complementAutos->getCountComplements($filters, false),
            "recordsFiltered" => $this->complementAutos->getCountComplements($filters),
            "data" => $result
        );

        return response()->json($output);
    }
}
