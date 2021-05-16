<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Automovel\Opcional;
use App\Models\Opcionais;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class OpcionalController extends Controller
{
    private $opcional;
    private $opcionais;

    public function __construct(Opcionais $opcionais, Opcional $opcional)
    {
        $this->opcional     = $opcional;
        $this->opcionais    = $opcionais;
    }

    public function getOptionals($tipo_auto): JsonResponse
    {
        $optionals = $this->opcionais->getOptionalsByType($tipo_auto);
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

    public function getOptionalsByAuto($tipo_auto, $auto_id): JsonResponse
    {
        $optionals = $this->opcionais->getOptionalsByType($tipo_auto);
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
        $optionalsAuto = $this->opcionais->getOpicionais();

        return view('auth.register.optionals.listagem', compact('optionalsAuto'));

    }

    public function insert(Request $request): JsonResponse
    {
        $name           = filter_var($request->name, FILTER_SANITIZE_STRING);
        $typeAuto       = filter_var($request->typeAuto, FILTER_SANITIZE_STRING);
        $active         = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'unique:opcionais,nome'
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

        $create = $this->opcionais->insert(array(
            'nome'          => $name,
            'tipo_auto'     => $typeAuto,
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
            'optional_id' => $create->id
        ));
    }

    public function update(Request $request): JsonResponse
    {
        $name       = filter_var($request->name, FILTER_SANITIZE_STRING);
        $typeAuto   = filter_var($request->typeAuto, FILTER_SANITIZE_STRING);
        $optionalId = filter_var($request->optionalId, FILTER_VALIDATE_INT);
        $active     = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'unique:complementar_autos,nome,' . $optionalId
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

        if (!$this->opcionais->getOptional($optionalId))
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível localizar o opcional. Tente novamente mais tarde!'
            ));

        $update = $this->opcionais->edit(array(
            'nome'          => $name,
            'tipo_auto'     => $typeAuto,
            'ativo'         => $active,
            'user_update'   => $request->user()->id
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
        return response()->json($this->opcionais->getoptional($id));
    }
}
