<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Automovel\ComplementarAuto;
use App\Models\ComplementarAutos;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $complementes = $this->complementAutos->getComplemenetaresByType($tipo_auto);
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
        $complementes = $this->complementAutos->getComplemenetaresByType($tipo_auto);
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
}
