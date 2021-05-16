<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Automovel\ComplementarAuto;
use App\Models\ComplementarAutos;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComplementarController extends Controller
{
    private $complementAuto;
    private $complementAutos;

    public function __construct(ComplementarAuto $complementAuto, ComplementarAutos $complementAutos)
    {
        $this->complementAuto   = $complementAuto;
        $this->complementAutos  = $complementAutos;
    }

    public function getComplemenetares($tipo_auto): JsonResponse
    {
        $complementes = $this->complementAutos->getComplementaresByType($tipo_auto);
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

    public function getComplemenetaresByAuto($tipo_auto, $auto_id): JsonResponse
    {
        $complementes = $this->complementAutos->getComplementaresByType($tipo_auto);
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

                if (is_numeric($valueComplement)) (int)$valueComplement;

                if (!empty($complementId))
                    $arrComplements[$complementId] = $valueComplement;
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

        return view('auth.register.complements.listagem', compact('complementsAuto'));

    }

    public function insert(Request $request): JsonResponse
    {
        $name           = filter_var($request->name, FILTER_SANITIZE_STRING);
        $typeAuto       = filter_var($request->typeAuto, FILTER_SANITIZE_STRING);
        $typeField      = filter_var($request->typeField, FILTER_SANITIZE_STRING);
        $valuesDefault  = $typeField === 'select' ? json_encode($request->valuesDefault) : null;
        $active         = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'unique:complementar_autos,nome'
            ],
            [
                'name.*' => 'O nome escolhido já está em uso.'
            ]
        );


        if ($validator->fails())
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível cadastrar... ' .
                    implode(
                        '|',
                        array_map(function ($value) {
                            return $value[0];
                        }, $validator->getMessageBag()->toArray()
                        )
                    )
            ));

        $create = $this->complementAutos->insert(array(
            'nome'          => $name,
            'tipo_auto'     => $typeAuto,
            'tipo_campo'    => $typeField,
            'valores_padrao'=> $valuesDefault,
            'ativo'         => $active,
            'user_insert'   => $request->user()->id
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

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'unique:complementar_autos,nome,' . $complementId
            ],
            [
                'name.*' => 'O nome escolhido já está em uso.'
            ]
        );


        if ($validator->fails())
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível atualizar... ' .
                    implode(
                        '|',
                        array_map(function ($value) {
                            return $value[0];
                        }, $validator->getMessageBag()->toArray()
                        )
                    )
            ));

        if (!$this->complementAutos->getComplement($complementId))
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível localizar o complementar. Tente novamente mais tarde!'
            ));

        $update = $this->complementAutos->edit(array(
            'nome'          => $name,
            'tipo_auto'     => $typeAuto,
            'tipo_campo'    => $typeField,
            'valores_padrao'=> $valuesDefault,
            'ativo'         => $active,
            'user_update'   => $request->user()->id
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
        return response()->json($this->complementAutos->getComplement($id));
    }
}
