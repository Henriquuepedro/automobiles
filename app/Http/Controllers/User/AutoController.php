<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Automovel\Automovel;
use App\Models\Automovel\ComplementarAuto;
use App\Models\Automovel\CorAuto;
use App\Models\Automovel\Image;
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
    private $image;

    public function __construct(
        Automovel $automovel,
        Opcionais $opcionais,
        Opcional $opcional,
        ComplementarAutos $complementAutos,
        ComplementarAuto $complementAuto,
        Image $image
    )
    {
        $this->automovel        = $automovel;
        $this->opcionais        = $opcionais;
        $this->opcional         = $opcional;
        $this->complementAutos  = $complementAutos;
        $this->complementAuto   = $complementAuto;
        $this->image            = $image;
    }

    public function list()
    {
        return view('user.auto.list');
    }

    public function getAutos(int $page): JsonResponse
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
                "rs_valor"      => 'R$ '.number_format($auto->valor, 2, ',', '.'),
                "valor"         => number_format($auto->valor, 2, ',', '.'),
                "kms"           => number_format($auto->kms, 0, ',', '.'),
                "destaque"      => $auto->destaque == 1 ? true : false,
                'cambio'        => ComplementarAutos::getValueComplementByAutoName($this->getStoreDomain(), 'Câmbio', $auto->auto_id),
                'combustivel'   => $auto->fuel_name
            ));
        }

//        DB::getQueryLog();

        return response()->json($arrAutos);
    }

    public function getAutosFeatured(): JsonResponse
    {
        $autos = $this->automovel->getAutosSimplified($this->getStoreDomain(), 'featured');

        return response()->json($this->formatResponseAutos($autos));
    }

    public function getDataAutoPreview(int $id, bool $responseJson = true)
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
            "rs_valor"      => 'R$'.number_format($auto->valor, 2, ',', '.'),
            "valor"         => number_format($auto->valor, 2, ',', '.'),
            "kms"           => number_format($auto->kms, 0, ',', '.'),
            "destaque"      => $auto->destaque == 1,
            "placa"         => $auto->placa ? substr_replace($auto->placa, '*****', 1, -1) : '',
            'accept_exchange'   => $auto->aceita_troca == 1 ? 'Sim' : 'Não',
            'only_owner'    => $auto->unico_dono == 1 ? 'Sim' : 'Não',
            'type_auto'     => $auto->tipo_auto,
            'observation'   => $auto->observation,
            'reference'     => $auto->reference,
            'fuel'          => $auto->fuel_name
        );

        $response = [
            'auto'      => $auto,
            'optional'  => $arrOptional,
            'complement'=> $arrComplement
        ];

        if ($responseJson)
            return response()->json($response);

        return $response;
    }

    public function getAutosRecent(): JsonResponse
    {
        $autos = $this->automovel->getAutosSimplified($this->getStoreDomain(), 'recent');

        return response()->json($this->formatResponseAutos($autos));
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
        $optionals = $this->opcionais->getOptionalsByStore($this->getStoreDomain());
        return response()->json($optionals);
    }

    public function previewAuto(int $auto)
    {
        if (!$this->automovel->checkAutoStore($auto, $this->getStoreDomain()))
            return redirect()->route('user.auto.list');

        $dataAuto = $this->getDataAutoPreview($auto, false);
        $dataAuto['images'] = $this->image->getImageByAuto($auto);
//        dd($dataAuto);

        return view('user.auto.preview', compact('dataAuto'));
    }

    public function getAutosRelated(int $auto, int $registers = 3): JsonResponse
    {
        $arrayAutosRelated = array();
        $autos = $this->automovel->getAutosRelated($this->getStoreDomain(), $auto, $registers);

        foreach ($autos as $_autos)
            $arrayAutosRelated = array_merge($arrayAutosRelated, $this->formatResponseAutos($_autos));

        return response()->json($arrayAutosRelated);
    }

    private function formatResponseAutos($autos): array
    {
        $arrAutos = array();

        foreach ($autos as $auto)
            array_push($arrAutos, array(
                "file"          => empty($auto->arquivo) ? "assets/admin/dist/images/autos/no_image.png" : "assets/admin/dist/images/autos/{$auto->tipo_auto}/{$auto->auto_id}/thumbnail_{$auto->arquivo}",
                "auto_id"       => $auto->auto_id,
                "marca_nome"    => $auto->marca_nome,
                "modelo_nome"   => $auto->modelo_nome,
                "ano_nome"      => $auto->ano_nome,
                "cor"           => CorAuto::getColorById($auto->cor),
                "rs_valor"      => 'R$ '.number_format($auto->valor, 2, ',', '.'),
                "valor"         => number_format($auto->valor, 2, ',', '.'),
                "kms"           => number_format($auto->kms, 0, ',', '.'),
                "destaque"      => $auto->destaque == 1,
                'cambio'        => ComplementarAutos::getValueComplementByAutoName($this->getStoreDomain(), 'Câmbio', $auto->auto_id),
                'combustivel'   => $auto->fuel_name
            ));

        return $arrAutos;
    }
}
