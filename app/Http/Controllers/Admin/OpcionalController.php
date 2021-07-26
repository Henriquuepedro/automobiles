<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Automovel\Opcional;
use App\Models\Opcionais;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class OpcionalController extends Controller
{
    private $opcional;
    private $opcionais;
    private $store;

    public function __construct(Opcionais $opcionais, Opcional $opcional, Store $store)
    {
        $this->opcional  = $opcional;
        $this->opcionais = $opcionais;
        $this->store     = $store;
    }

    public function getOptionals($tipo_auto, $store): JsonResponse
    {
        // loja informado o usuário não tem permissão
        if (!in_array($store, $this->getStoresByUsers())) return response()->json([]);

        $optionals = $this->opcionais->getOptionalsByType($tipo_auto, $store);
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
        // loja informado o usuário não tem permissão
        if (!in_array($store, $this->getStoresByUsers())) return response()->json([]);

        $optionals = $this->opcionais->getOptionalsByType($tipo_auto, $store);
        $optionalAuto = (array)json_decode($this->opcional->getOptionalByAuto($auto_id)->valores ?? '{}');
        $arrOptional = array();

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
        $optionalsAuto  = $this->opcionais->getOpicionais();
        $stores         = $this->store->getStores($this->getStoresByUsers());

        return view('admin.register.optionals.listagem', compact('optionalsAuto', 'stores'));

    }

    public function insert(Request $request): JsonResponse
    {
        $name           = filter_var($request->name, FILTER_SANITIZE_STRING);
        $typeAuto       = filter_var($request->typeAuto, FILTER_SANITIZE_STRING);
        $active         = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);

        // loja informado o usuário não tem permissão
        if (!isset($request->stores) || !in_array($request->stores, $this->getStoresByUsers()))
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível identificar a loja informada!'
            ));

        if ($this->opcionais->getOptionalByName($name, $request->stores))
            return response()->json(array(
                'success' => false,
                'message' => 'Nome do opcional já está em uso!'
            ));

        $create = $this->opcionais->insert(array(
            'nome'          => $name,
            'tipo_auto'     => $typeAuto,
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
            'optional_id' => $create->id
        ));
    }

    public function update(Request $request): JsonResponse
    {
        $name       = filter_var($request->name, FILTER_SANITIZE_STRING);
        $typeAuto   = filter_var($request->typeAuto, FILTER_SANITIZE_STRING);
        $optionalId = filter_var($request->optionalId, FILTER_VALIDATE_INT);
        $active     = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);

        if (!$this->opcionais->getOptional($optionalId))
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

        if ($this->opcionais->getOptionalByName($name, $request->stores, $optionalId))
            return response()->json(array(
                'success' => false,
                'message' => 'Nome do complementar já está em uso!'
            ));

        $update = $this->opcionais->edit(array(
            'nome'          => $name,
            'tipo_auto'     => $typeAuto,
            'ativo'         => $active,
            'user_update'   => $request->user()->id,
            'company_id'    => $request->user()->company_id,
            'store_id'      => $request->stores
        ), $optionalId);

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

    public function getOptional(int $id)
    {
        $response = $this->opcionais->getoptional($id);

        if (!in_array($response->store_id, $this->getStoresByUsers())) return [];

        return response()->json($response);
    }
}
