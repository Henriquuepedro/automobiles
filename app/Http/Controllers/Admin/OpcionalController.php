<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Automovel\Opcional;
use App\Models\Opcionais;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
}
