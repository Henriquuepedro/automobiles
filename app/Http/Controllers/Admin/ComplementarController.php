<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Automovel\ComplementarAuto;
use App\Models\ComplementarAutos;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComplementarController extends Controller
{
    private $complementAuto;
    private $complementAutos;
    private $store;

    public function __construct(ComplementarAuto $complementAuto, ComplementarAutos $complementAutos, Store $store)
    {
        $this->complementAuto   = $complementAuto;
        $this->complementAutos  = $complementAutos;
        $this->store            = $store;
    }

    public function getComplemenetares($tipo_auto, $store): JsonResponse
    {
        // loja informado o usuário não tem permissão
        if (!in_array($store, $this->getStoresByUsers())) return response()->json([]);

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

    public function getComplemenetaresByAuto($tipo_auto, $store, $auto_id): JsonResponse
    {
        // loja informado o usuário não tem permissão
        if (!in_array($store, $this->getStoresByUsers())) return response()->json([]);

        $complementes = $this->complementAutos->getComplementaresByType($tipo_auto, $store);
        $complementAuto = (array)json_decode($this->complementAuto->getComplementarByAuto($auto_id)->valores ?? '{}');
        $arrComplement = array();

        foreach ($complementes as $complement) {
            array_push($arrComplement, array(
                'id'                => $complement->id,
                'nome'              => $complement->nome,
                'tipo_campo'        => $complement->tipo_campo,
                'valores_padrao'    => json_decode($complement->valores_padrao) ?? null,
                'valor_salvo'       => isset($complementAuto[$complement->id]) ? $complementAuto[$complement->id] : null
            ));
        }

        return response()->json($arrComplement);
    }

    public function getDataFormatToInsert($dataForm, $codAutomovel): array
    {
        $arrComplements = array();
        foreach($dataForm as $complement => $valueComplement) {

            if (preg_match('/.*?complement_.*?/', $complement) > 0) {
                $complementId = (int)str_replace('complement_', '', $complement);

                if (is_numeric($valueComplement)) $valueComplement = (int)$valueComplement;

                if ($complementId != '' && $complementId != null)
                    $arrComplements[$complementId] = empty($valueComplement) && $valueComplement !== 0 ? null : $valueComplement;
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
        $complementsAuto = $this->complementAutos->getComplemenetares();
        $stores          = $this->store->getStores($this->getStoresByUsers());

        return view('admin.register.complements.listagem', compact('complementsAuto', 'stores'));

    }

    public function insert(Request $request): JsonResponse
    {
        $name           = filter_var($request->name, FILTER_SANITIZE_STRING);
        $typeAuto       = filter_var($request->typeAuto, FILTER_SANITIZE_STRING);
        $typeField      = filter_var($request->typeField, FILTER_SANITIZE_STRING);
        $valuesDefault  = $typeField === 'select' ? json_encode($request->valuesDefault) : null;
        $active         = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);

        // loja informado o usuário não tem permissão
        if (!isset($request->stores) || !in_array($request->stores, $this->getStoresByUsers()))
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível identificar a loja informada!'
            ));

        if ($this->complementAutos->getComplementByName($name, $request->stores))
            return response()->json(array(
                'success' => false,
                'message' => 'Nome do complementar já está em uso!'
            ));

        $create = $this->complementAutos->insert(array(
            'nome'          => $name,
            'tipo_auto'     => $typeAuto,
            'tipo_campo'    => $typeField,
            'valores_padrao'=> $valuesDefault,
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
            'complement_id' => $create->id
        ));
    }

    public function update(Request $request): JsonResponse
    {
        $name           = filter_var($request->name, FILTER_SANITIZE_STRING);
        $typeAuto       = filter_var($request->typeAuto, FILTER_SANITIZE_STRING);
        $typeField      = filter_var($request->typeField, FILTER_SANITIZE_STRING);
        $valuesDefault  = $typeField === 'select' ? json_encode($request->valuesDefault) : null;
        $complementId   = filter_var($request->complementId, FILTER_VALIDATE_INT);
        $active         = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);

        if (!$this->complementAutos->getComplement($complementId))
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível localizar o complementar. Tente novamente mais tarde!'
            ));

        // loja informado o usuário não tem permissão
        if (!isset($request->stores) || !in_array($request->stores, $this->getStoresByUsers()))
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível identificar a loja informada!'
            ));

        if ($this->complementAutos->getComplementByName($name, $request->stores, $complementId))
            return response()->json(array(
                'success' => false,
                'message' => 'Nome do opcional já está em uso!'
            ));

        $update = $this->complementAutos->edit(array(
            'nome'          => $name,
            'tipo_auto'     => $typeAuto,
            'tipo_campo'    => $typeField,
            'valores_padrao'=> $valuesDefault,
            'ativo'         => $active,
            'user_update'   => $request->user()->id,
            'company_id'    => $request->user()->company_id,
            'store_id'      => $request->stores
        ), $complementId);

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

    public function getComplement(int $id)
    {
        $response = $this->complementAutos->getComplement($id);

        if (!in_array($response->store_id, $this->getStoresByUsers())) return [];

        return response()->json($response);
    }
}
