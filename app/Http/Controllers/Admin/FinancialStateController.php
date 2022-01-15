<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Automobile\FinancialState;
use App\Models\FinancialStates;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinancialStateController extends Controller
{
    private FinancialStates $estadosFinanceiro;
    private FinancialState $estadoFinanceiro;
    private Store $store;

    public function __construct(FinancialStates $estadosFinanceiro, FinancialState $estadoFinanceiro, Store $store)
    {
        $this->estadosFinanceiro = $estadosFinanceiro;
        $this->estadoFinanceiro  = $estadoFinanceiro;
        $this->store             = $store;
    }

    public function list()
    {
        $stores = $this->store->getStores($this->getStoresByUsers());

        return view('admin.register.financialsStatus.index', compact('stores'));

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

        if ($this->estadosFinanceiro->getFinancialStatusByName($name, $request->input('stores'))) {
            return response()->json(array(
                'success' => false,
                'message' => 'Nome do estado financeiro já está em uso!'
            ));
        }

        $create = $this->estadosFinanceiro->insert(array(
            'nome'          => $name,
            'ativo'         => $active,
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
            'financialStatus_id' => $create->id
        ));
    }

    public function update(Request $request): JsonResponse
    {
        $name       = filter_var($request->input('name'), FILTER_SANITIZE_STRING);
        $stateId    = filter_var($request->input('financialStatusId'), FILTER_VALIDATE_INT);
        $active     = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        if (!$this->estadosFinanceiro->getFinancialStatus($stateId)) {
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível localizar o estado financeiro. Tente novamente mais tarde!'
            ));
        }

        // Loja informada ou usuário não tem permissão
        if (!$request->has('stores') || !in_array($request->input('stores'), $this->getStoresByUsers())) {
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível identificar a loja informada!'
            ));
        }

        if ($this->estadosFinanceiro->getFinancialStatusByName($name, $request->input('stores'), $stateId)) {
            return response()->json(array(
                'success' => false,
                'message' => 'Nome do estado financeiro já está em uso!'
            ));
        }


        $update = $this->estadosFinanceiro->edit(array(
            'nome'          => $name,
            'ativo'         => $active,
            'user_update'   => $request->user()->id,
            'company_id'    => $this->store->getCompanyByStore($request->input('stores')),
            'store_id'      => $request->input('stores')
        ), $stateId);

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

    public function getFinancialStatus(int $id)
    {
        $response = $this->estadosFinanceiro->getFinancialStatus($id);

        if (!in_array($response->store_id, $this->getStoresByUsers())) {
            return [];
        }

        return response()->json($response);
    }

    public function getFinancialsStatus(int $store): JsonResponse
    {
        // Loja informada ou usuário não tem permissão
        if (!in_array($store, $this->getStoresByUsers())) {
            return response()->json([]);
        }

        $financialsStatus = $this->estadosFinanceiro->getAllFinancialsStatusByStore($store);
        $arrFinancialsStatus = array();

        foreach ($financialsStatus as $financialStatus) {
            array_push($arrFinancialsStatus, array(
                'id'                => $financialStatus->id,
                'nome'              => $financialStatus->nome,
                'tipo_campo'        => $financialStatus->tipo_campo,
                'valores_padrao'    => json_decode($financialStatus->valores_padrao) ?? null,
                'valor_salvo'       => null
            ));
        }

        return response()->json($arrFinancialsStatus);
    }

    public function getFinancialsStatusByAuto(int $store, int $auto_id): JsonResponse
    {
        // Loja informada ou usuário não tem permissão
        if (!in_array($store, $this->getStoresByUsers())) {
            return response()->json([]);
        }

        $financialsStatus = $this->estadosFinanceiro->getAllFinancialsStatusByStore($store);
        $financialStatusAuto = (array)json_decode($this->estadoFinanceiro->getFinancialsStatusByStore($auto_id)->valores ?? '{}');
        $arrFinancialsStatus = array();

        foreach ($financialsStatus as $financialStatus) {
            array_push($arrFinancialsStatus, array(
                'id'                => $financialStatus->id,
                'nome'              => $financialStatus->nome,
                'tipo_campo'        => $financialStatus->tipo_campo,
                'valores_padrao'    => json_decode($financialStatus->valores_padrao) ?? null,
                'valor_salvo'       => in_array($financialStatus->id, $financialStatusAuto)
            ));
        }

        return response()->json($arrFinancialsStatus);
    }

    public function fetchFinancialStateData(Request $request): JsonResponse
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

            $fieldsOrder = array('nome','ativo','');
            if (count($store_id) > 1) {
                $fieldsOrder[2] = 'store_id';
                $fieldsOrder[3] = '';
            }
            $fieldOrder =  $fieldsOrder[$request->input('order')[0]['column']];
            if ($fieldOrder != "") {
                $orderBy['field'] = $fieldOrder;
                $orderBy['order'] = $direction;
            }
        }

        $data = $this->estadosFinanceiro->getFinancialsStates($filters, $ini, $length, $orderBy);

        foreach ($data as $key => $value) {

            $activeColor    = $value['ativo'] ? 'success' : 'danger';
            $activeLabel    = $value['ativo'] ? 'Ativo' : 'Inativo';
            $active         = "<div class='badge badge-pill badge-lg badge-$activeColor w-100'>$activeLabel</div>";
            $button         = "<button class='btn btn-primary btn-flat btn-sm editFinancialStatus' financialStatus-id='{$value['id']}'><i class='fa fa-edit'></i></button>";

            $array = array(
                $value['nome'],
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
            "recordsTotal" => $this->estadosFinanceiro->getCountFinancialsStates($filters, false),
            "recordsFiltered" => $this->estadosFinanceiro->getCountFinancialsStates($filters),
            "data" => $result
        );

        return response()->json($output);
    }
}
