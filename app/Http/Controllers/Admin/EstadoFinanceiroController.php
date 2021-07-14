<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EstadosFinanceiro;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EstadoFinanceiroController extends Controller
{
    private $estadosFinanceiro;

    public function __construct(EstadosFinanceiro $estadosFinanceiro)
    {
        $this->estadosFinanceiro = $estadosFinanceiro;
    }

    public function list()
    {
        $financialsStatusAuto = $this->estadosFinanceiro->getFinancialsStatus(true);

        return view('admin.register.financialsStatus.listagem', compact('financialsStatusAuto'));

    }

    public function insert(Request $request): JsonResponse
    {
        $name   = filter_var($request->name, FILTER_SANITIZE_STRING);
        $active = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'unique:estados_financeiro,nome'
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

        $create = $this->estadosFinanceiro->insert(array(
            'nome'          => $name,
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
            'financialStatus_id' => $create->id
        ));
    }

    public function update(Request $request): JsonResponse
    {
        $name       = filter_var($request->name, FILTER_SANITIZE_STRING);
        $stateId    = filter_var($request->financialStatusId, FILTER_VALIDATE_INT);
        $active     = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'unique:complementar_autos,nome,' . $stateId
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

        if (!$this->estadosFinanceiro->getFinancialStatus($stateId))
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível localizar o estado financeiro. Tente novamente mais tarde!'
            ));

        $update = $this->estadosFinanceiro->edit(array(
            'nome'          => $name,
            'ativo'         => $active,
            'user_update'   => $request->user()->id
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
        return response()->json($this->estadosFinanceiro->getFinancialStatus($id));
    }
}
