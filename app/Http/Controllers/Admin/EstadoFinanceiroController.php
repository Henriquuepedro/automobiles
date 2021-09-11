<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Automovel\EstadoFinanceiro;
use App\Models\EstadosFinanceiro;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EstadoFinanceiroController extends Controller
{
    private $estadosFinanceiro;
    private $estadoFinanceiro;
    private $store;

    public function __construct(EstadosFinanceiro $estadosFinanceiro, EstadoFinanceiro $estadoFinanceiro, Store $store)
    {
        $this->estadosFinanceiro = $estadosFinanceiro;
        $this->estadoFinanceiro  = $estadoFinanceiro;
        $this->store             = $store;
    }

    public function list()
    {
        $financialsStatusAuto   = $this->estadosFinanceiro->getFinancialsStatus(true);
        $stores                 = $this->store->getStores($this->getStoresByUsers());

        return view('admin.register.financialsStatus.listagem', compact('financialsStatusAuto', 'stores'));

    }

    public function insert(Request $request): JsonResponse
    {
        $name   = filter_var($request->name, FILTER_SANITIZE_STRING);
        $active = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);

        // loja informado o usuário não tem permissão
        if (!isset($request->stores) || !in_array($request->stores, $this->getStoresByUsers()))
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível identificar a loja informada!'
            ));

        if ($this->estadosFinanceiro->getFinancialStatusByName($name, $request->stores))
            return response()->json(array(
                'success' => false,
                'message' => 'Nome do estado financeiro já está em uso!'
            ));

        $create = $this->estadosFinanceiro->insert(array(
            'nome'          => $name,
            'ativo'         => $active,
            'user_insert'   => $request->user()->id,
            'company_id'    => $request->user()->company_id,
            'store_id'      => $request->stores
        ));

        if (!$create)
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível cadastrar. Tente novamente mais tarde!'
            ));


        return response()->json(array(
            'success' => true,
            'message' => 'Cadastrado com sucesso!',
            'financialStatus_id' => $create->id
        ));
    }

    public function update(Request $request): JsonResponse
    {
        $name       = filter_var($request->name, FILTER_SANITIZE_STRING);
        $stateId    = filter_var($request->financialStatusId, FILTER_VALIDATE_INT);
        $active     = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);

        if (!$this->estadosFinanceiro->getFinancialStatus($stateId))
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível localizar o estado financeiro. Tente novamente mais tarde!'
            ));

        // loja informado o usuário não tem permissão
        if (!isset($request->stores) || !in_array($request->stores, $this->getStoresByUsers()))
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível identificar a loja informada!'
            ));

        if ($this->estadosFinanceiro->getFinancialStatusByName($name, $request->stores, $stateId))
            return response()->json(array(
                'success' => false,
                'message' => 'Nome do estado financeiro já está em uso!'
            ));


        $update = $this->estadosFinanceiro->edit(array(
            'nome'          => $name,
            'ativo'         => $active,
            'user_update'   => $request->user()->id,
            'company_id'    => $request->user()->company_id,
            'store_id'      => $request->stores
        ), $stateId);

        if (!$update)
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível atualizar. Tente novamente mais tarde!'
            ));


        return response()->json(array(
            'success' => true,
            'message' => 'Atualizado com sucesso!'
        ));
    }

    public function getFinancialStatus(int $id)
    {
        $response = $this->estadosFinanceiro->getFinancialStatus($id);

        if (!in_array($response->store_id, $this->getStoresByUsers())) return [];

        return response()->json($response);
    }

    public function getFinancialsStatus(int $store): JsonResponse
    {
        // loja informado o usuário não tem permissão
        if (!in_array($store, $this->getStoresByUsers())) return response()->json([]);

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
        // loja informado o usuário não tem permissão
        if (!in_array($store, $this->getStoresByUsers())) return response()->json([]);

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
}
