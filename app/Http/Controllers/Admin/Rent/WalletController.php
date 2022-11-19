<?php

namespace App\Http\Controllers\Admin\Rent;

use App\Http\Controllers\Controller;
use App\Models\Rent\RentAutomobile;
use App\Models\Rent\RentWallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use StdClass;

class WalletController extends Controller
{
    private RentAutomobile $rentAutomobile;
    private RentWallet $rentWallet;

    public function __construct()
    {
        $this->rentAutomobile   = new RentAutomobile();
        $this->rentWallet       = new RentWallet();
    }

    public function update(Request $request): JsonResponse
    {
        $autoId = $request->input('auto_id');

        if (!$this->rentAutomobile->getAutomobileComplete($autoId)) {
            return response()->json(['success' => false, 'message' => "Automóvel nao localizado."]);
        }

        // data period
        $qtyPeriods     = isset($request->day_start) ? count($request->day_start) : 0;
        $arrDaysVerify  = array();
        $createPeriods  = true;

        DB::beginTransaction();// Iniciando transação manual para evitar updates não desejáveis

        $this->rentWallet->removeAllByAuto($autoId);

        for ($per = 0; $per < $qtyPeriods; $per++) {
            $periodUser = $per+1;
            $dataPeriod = $this->formatDataPeriod($request, $per);

            // dia inicial maior que o final
            if ($dataPeriod->day_start > $dataPeriod->day_end) {
                return response()->json(['success' => false, 'message' => "Existem erros no período. O dia final do {$periodUser}º período não pode ser menor que o inicial, deve ser informado em ordem crescente."]);
            }

            // adiciona valor em array para validação
            for ($countPer = $dataPeriod->day_start; $countPer <= $dataPeriod->day_end; $countPer++) {
                // dia informado já está dentro de um prazo
                if (in_array($countPer, $arrDaysVerify)) {
                    return response()->json(['success' => false, 'message' => "Existem erros no período. O {$periodUser}º período está inválido, já existe algum dia em outros período."]);
                }

                $arrDaysVerify[] = $countPer;
            }

            if ($dataPeriod->day_start < 0 || $dataPeriod->day_end <= 0 || $dataPeriod->value_period <= 0) {
                return response()->json(['success' => false, 'message' => 'Existem erros no período. Dia inicial não pode ser negativo. Dia final deve ser maior que zero e valor deve ser maior que zero']);
            }

            $this->rentWallet = new RentWallet();
            $this->rentWallet->setAttribute('auto_id', $autoId);
            $this->rentWallet->setAttribute('day_start', $dataPeriod->day_start);
            $this->rentWallet->setAttribute('day_end', $dataPeriod->day_end);
            $this->rentWallet->setAttribute('value', $dataPeriod->value_period);
            $this->rentWallet->setAttribute('user_insert', $request->user()->id);
            $this->rentWallet->setAttribute('user_update', $request->user()->id);

            if (!$this->rentWallet->save()) {
                $createPeriods = false;
            }
        }

        if($createPeriods) {
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Valores do automóvel atualizado com sucesso!']);
        }

        DB::rollBack();
        return response()->json(['success' => true, 'message' => 'Não foi possível atualizar os valores do automóvel, tente novamente!']);
    }

    public function getByAuto(int $id): JsonResponse
    {
        if (!$this->rentAutomobile->getAutomobileComplete($id)) {
            return response()->json([]);
        }

        $arrWallet = array();

        foreach ($this->rentWallet->getByAuto($id) as $wallet) {
            $arrWallet[] = array(
                'day_start' => $wallet->day_start,
                'day_end'   => $wallet->day_end,
                'value'     => number_format($wallet->value, 2, ',', '.')
            );
        }

        return response()->json($arrWallet);
    }

    private function formatDataPeriod($request, $per): stdClass
    {
        $obj = new \stdClass;

        $obj->day_start      = filter_var((int)$request->day_start[$per], FILTER_VALIDATE_INT);
        $obj->day_end        = filter_var((int)$request->day_end[$per], FILTER_VALIDATE_INT);
        $obj->value_period   = $this->transformMoneyBr_En($request->value_period[$per]);

        return $obj;
    }
}
