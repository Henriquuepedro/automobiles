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
    private Optionals $opcionais;
    private Store $store;
    private ControlAutos $controlAutos;

    public function __construct(Optionals $opcionais, Optional $opcional, Store $store, ControlAutos $controlAutos)
    {
        $this->opcional     = $opcional;
        $this->opcionais    = $opcionais;
        $this->store        = $store;
        $this->controlAutos = $controlAutos;
    }

    public function getOptionals($tipo_auto, $store): JsonResponse
    {
        // Loja informada ou usuário não tem permissão
        if (!in_array($store, $this->getStoresByUsers())) {
            return response()->json([]);
        }

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
        // Loja informada ou usuário não tem permissão
        if (!in_array($store, $this->getStoresByUsers())) {
            return response()->json([]);
        }

        $optionals      = $this->opcionais->getOptionalsByType($tipo_auto, $store);
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
        $optionalsAuto  = $this->opcionais->getOpicionais();
        $stores         = $this->store->getStores($this->getStoresByUsers());
        $controlAutos   = $this->controlAutos->getAllControlsActive();

        return view('admin.register.optionals.index', compact('optionalsAuto', 'stores', 'controlAutos'));

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

        if ($this->opcionais->getOptionalByName($name, $request->input('stores'))) {
            return response()->json(array(
                'success' => false,
                'message' => 'Nome do opcional já está em uso!'
            ));
        }

        $create = $this->opcionais->insert(array(
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

        if (!$this->opcionais->getOptional($optionalId)) {
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

        if ($this->opcionais->getOptionalByName($name, $request->input('stores'), $optionalId)) {
            return response()->json(array(
                'success' => false,
                'message' => 'Nome do complementar já está em uso!'
            ));
        }

        $update = $this->opcionais->edit(array(
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
        $response = $this->opcionais->getoptional($id);

        if (!in_array($response->store_id, $this->getStoresByUsers())) {
            return [];
        }

        return response()->json($response);
    }
}
