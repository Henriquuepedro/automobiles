<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Automovel\Automovel;
use App\Models\Automovel\ComplementarAuto;
use App\Models\Automovel\CorAuto;
use App\Models\Automovel\Opcional;
use App\Models\ComplementarAutos;
use App\Models\Opcionais;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AutoController extends Controller
{
    private $automovel;
    private $opcionais;
    private $opcional;
    private $complementAutos;
    private $complementAuto;

    public function __construct(
        Automovel $automovel,
        Opcionais $opcionais,
        Opcional $opcional,
        ComplementarAutos $complementAutos,
        ComplementarAuto $complementAuto
    )
    {
        $this->automovel = $automovel;
        $this->opcionais = $opcionais;
        $this->opcional = $opcional;
        $this->complementAutos = $complementAutos;
        $this->complementAuto = $complementAuto;
    }

    public function list()
    {
        return view('user.auto.list');
    }

    public function getAutos($page): JsonResponse
    {
        $page--;
//        DB::enableQueryLog();

        $filters = Request::capture()->filters;

        $filterAuto = array(
            'order'         => $filters['order'] ?? 0,
            'search'        => $filters['filter'] ?? array(),
            'optional'      => $filters['optionals'] ?? array()
        );

        $arrAutos = array();
        $autos = $this->automovel->getAutosSimplified($this->getStoreDomain(), null, $filterAuto, $page);

        foreach ($autos as $auto) {
            array_push($arrAutos, array(
                "file"          => empty($auto->arquivo) ? "assets/admin/dist/images/autos/no_image.png" : "assets/admin/dist/images/autos/{$auto->tipo_auto}/{$auto->auto_id}/thumbnail_{$auto->arquivo}",
                "auto_id"       => $auto->auto_id,
                "marca_nome"    => $auto->marca_nome,
                "modelo_nome"   => $auto->modelo_nome,
                "ano_nome"      => $auto->ano_nome,
                "cor"           => CorAuto::getColorById($auto->cor),
                "valor"         => 'R$ '.number_format($auto->valor, 2, ',', '.'),
                "kms"           => number_format($auto->kms, 0, ',', '.'),
                "destaque"      => $auto->destaque == 1 ? true : false,
                'cambio'        => ComplementarAutos::getValueComplementByAutoName($this->getStoreDomain(), 'Câmbio', $auto->auto_id),
                'combustivel'   => ComplementarAutos::getValueComplementByAutoName($this->getStoreDomain(), 'Combustível', $auto->auto_id)
            ));
        }

//        DB::getQueryLog();

        return response()->json($arrAutos);
    }

    public function getAutosFeatured(): JsonResponse
    {
        $arrAutos = array();
        $autos = $this->automovel->getAutosSimplified($this->getStoreDomain(), 'featured');

        foreach ($autos as $auto) {
            array_push($arrAutos, array(
                "file"          => empty($auto->arquivo) ? "assets/admin/dist/images/autos/no_image.png" : "assets/admin/dist/images/autos/{$auto->tipo_auto}/{$auto->auto_id}/thumbnail_{$auto->arquivo}",
                "auto_id"       => $auto->auto_id,
                "marca_nome"    => $auto->marca_nome,
                "modelo_nome"   => $auto->modelo_nome,
                "ano_nome"      => $auto->ano_nome,
                "cor"           => CorAuto::getColorById($auto->cor),
                "valor"         => 'R$ '.number_format($auto->valor, 2, ',', '.'),
                "kms"           => number_format($auto->kms, 0, ',', '.'),
                "destaque"      => $auto->destaque == 1 ? true : false,
                'cambio'        => ComplementarAutos::getValueComplementByAutoName($this->getStoreDomain(), 'Câmbio', $auto->auto_id),
                'combustivel'   => ComplementarAutos::getValueComplementByAutoName($this->getStoreDomain(), 'Combustível', $auto->auto_id)
            ));
        }

        return response()->json($arrAutos);
    }

    public function getDataAutoPreview(int $id): JsonResponse
    {
        $store = $this->getStoreDomain();
        $auto = $this->automovel->getDataPreview($id, $store);

        if (empty($auto)) return response()->json([]);

        //complementar
        $complementes = $this->complementAutos->getComplementaresByType($auto->tipo_auto, $store);
        $complementAuto = (array)json_decode($this->complementAuto->getComplementarByAuto($id)->valores ?? '{}');
        $arrComplement = array();

        foreach ($complementes as $complement) {

            if (!isset($complementAuto[$complement->id]) || $complementAuto[$complement->id] === null) continue;

            $valueDefault = $complementAuto[$complement->id];
            if (isset($complement->valores_padrao) && !empty($complement->valores_padrao)) {
                $value = json_decode($complement->valores_padrao);

                // não encontrou o valor
                if (!array_key_exists($complementAuto[$complement->id], $value)) continue;

                $valueDefault = $value[$complementAuto[$complement->id]];
            }

            array_push($arrComplement, array(
                'name'  => $complement->nome,
                'value' => $valueDefault
            ));
        }

        // opcionais
        $optionals = $this->opcionais->getOptionalsByType($auto->tipo_auto, $store);
        $optionalAuto = (array)json_decode($this->opcional->getOptionalByAuto($id)->valores ?? '{}');
        $arrOptional = array();

        foreach ($optionals as $optional) {
            if (!in_array($optional->id, $optionalAuto)) continue;

            array_push($arrOptional, array(
                'name' => $optional->nome
            ));
        }

        //auto
            $auto = array(
                "file"          => empty($auto->arquivo) ? "assets/admin/dist/images/autos/no_image.png" : "assets/admin/dist/images/autos/{$auto->tipo_auto}/{$auto->auto_id}/thumbnail_{$auto->arquivo}",
                "auto_id"       => $auto->auto_id,
                "marca_nome"    => $auto->marca_nome,
                "modelo_nome"   => $auto->modelo_nome,
                "ano_nome"      => $auto->ano_nome,
                "cor"           => CorAuto::getColorById($auto->cor),
                "valor"         => 'R$'.number_format($auto->valor, 2, ',', '.'),
                "kms"           => number_format($auto->kms, 0, ',', '.'),
                "destaque"      => $auto->destaque == 1 ? true : false
            );

        return response()->json([
            'auto'      => $auto,
            'optional'  => $arrOptional,
            'complement'=> $arrComplement
        ]);
    }

    public function getAutosRecent(): JsonResponse
    {
        $arrAutos = array();
        $autos = $this->automovel->getAutosSimplified($this->getStoreDomain(), 'recent');

        foreach ($autos as $auto) {
            array_push($arrAutos, array(
                "file"          => empty($auto->arquivo) ? "assets/admin/dist/images/autos/no_image.png" : "assets/admin/dist/images/autos/{$auto->tipo_auto}/{$auto->auto_id}/thumbnail_{$auto->arquivo}",
                "auto_id"       => $auto->auto_id,
                "marca_nome"    => $auto->marca_nome,
                "modelo_nome"   => $auto->modelo_nome,
                "ano_nome"      => $auto->ano_nome,
                "cor"           => CorAuto::getColorById($auto->cor),
                "valor"         => 'R$ '.number_format($auto->valor, 2, ',', '.'),
                "kms"           => number_format($auto->kms, 0, ',', '.'),
                "destaque"      => $auto->destaque == 1 ? true : false,
                'cambio'        => ComplementarAutos::getValueComplementByAutoName($this->getStoreDomain(), 'Câmbio', $auto->auto_id),
                'combustivel'   => ComplementarAutos::getValueComplementByAutoName($this->getStoreDomain(), 'Combustível', $auto->auto_id)
            ));
        }

        return response()->json($arrAutos);
    }

    public function getFilterAutos(): JsonResponse
    {
        $brand  = array();
        $model  = array();
        $year   = array();
        $color  = array();

        foreach($this->automovel->getFilterAuto($this->getStoreDomain()) as $filter) {
            if (!array_key_exists($filter->brand_code, $brand)) $brand[$filter->brand_code] = $filter->brand;

            if (!array_key_exists($filter->model_code, $model)) $model[$filter->model_code] = $filter->model;

            if (!array_key_exists($filter->year_code, $year)) $year[$filter->year_code] = $filter->year;

            if (!array_key_exists($filter->color_code, $color)) $color[$filter->color_code] = $filter->color;
        }

//        asort($brand);
//        asort($model);
//        asort($year);
//        asort($color);

        $filterPrice = $this->automovel->getFilterRangePrice($this->getStoreDomain());

        return response()->json(array(
            'brand'         => $brand,
            'model'         => $model,
            'year'          => $year,
            'color'         => $color,
            'range_price'   => $filterPrice
        ));
    }

    public function getOptionalsAutos(): JsonResponse
    {
        $arrAutos = array();
        $optionals = $this->opcionais->getOptionalsByStore($this->getStoreDomain());

        return response()->json($optionals);
    }
}
