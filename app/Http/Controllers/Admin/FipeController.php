<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fipe\FipeAuto;
use App\Models\Fipe\FipeBrand;
use App\Models\Fipe\FipeModel;
use App\Models\Fipe\FipeUpdatedValue;
use App\Models\Fipe\FipeYear;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Utils;
use Illuminate\Http\JsonResponse;

class FipeController extends Controller
{
    private FipeBrand $brand;
    private FipeModel $model;
    private FipeYear $year;
    private FipeAuto $auto;
    private FipeUpdatedValue $fipeUpdatedValue;
    private string $urlFipe = 'veiculos.fipe.org.br/api/veiculos';
    private Client $client;
    private array $codesReference;
    private array $headerGuzzle = array('Host' => 'veiculos.fipe.org.br', 'Referer' => 'http://veiculos.fipe.org.br', 'Content-Type' => 'application/json');

    public function __construct(
        FipeBrand $brand,
        FipeModel $model,
        FipeYear $year,
        FipeAuto $auto,
        FipeUpdatedValue $fipeUpdatedValue
    ) {
        $this->brand    = $brand;
        $this->model    = $model;
        $this->year     = $year;
        $this->auto     = $auto;
        $this->fipeUpdatedValue = $fipeUpdatedValue;
    }

    public function getBrand(string $auto): JsonResponse
    {
        return response()->json($this->brand->getAllBrandByAuto($auto));
    }

    public function getModel(string $auto, int $brand): JsonResponse
    {
        return response()->json($this->model->getAllModelByAutoAndBrand($auto, $brand));
    }

    public function getYear(string $auto, int $brand, int $model): JsonResponse
    {
        return response()->json($this->year->getAllYearByAutoAndBrandAndModel($auto, $brand, $model));
    }

    public function getAuto(string $auto, int $brand, int $model, string $year): JsonResponse
    {
        return response()->json($this->auto->getAllAutoByAutoAndBrandAndModelAndYear($auto, $brand, $model, $year));
    }

    public function getVariationAuto(int $auto): JsonResponse
    {
        try {
            $this->setClient();
            $this->setReferences(12);
        } catch (Exception $exception) {
            return response()->json([]);
        }

        $codesFipe = $this->auto->getCodesFipe($auto);

        $variations = $this->getVariationFipe($codesFipe->control_code, $codesFipe->brand_code, $codesFipe->model_code, $codesFipe->year_code);

        $variations = array_reverse($variations);

        return response()->json($variations);
    }

    /**
     * Define Client para requisições Guzzle.
     */
    private function setClient()
    {
        $this->client = new Client();
    }

    /**
     * Define o código de referência do mês.
     *
     * @throws GuzzleException|Exception
     */
    private function setReferences(int $months = 6)
    {
        try {
            $response = $this->client->post("$this->urlFipe/ConsultarTabelaDeReferencia");
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $bodyReference = $response->getBody()->getContents();
        $body = Utils::jsonDecode($bodyReference);

        if (!isset($body[0]->Codigo)) {
            throw new Exception("Código de referência não localizado.\n$bodyReference");
        }

        foreach ($body as $reference) {
            $this->codesReference[] = $reference->Codigo;

            if (count($this->codesReference) === $months) {
                break;
            }
        }
    }

    private function getVariationFipe(int $type, int $brand, int $model, string $year): array
    {
        $expYear = explode('-', $year);

        $query = array(
            'headers' => $this->headerGuzzle,
            'json' => array(
                'codigoTipoVeiculo'     => $type,
                'codigoMarca'           => $brand,
                'codigoModelo'          => $model,
                'anoModelo'             => $expYear[0],
                'codigoTipoCombustivel' => $expYear[1],
                'tipoConsulta'          => 'tradicional'
            )
        );

        $arrayFipeVariation = array();

        foreach ($this->codesReference as $reference) {

            $query['json']['codigoTabelaReferencia'] = $reference;

            try {
                $getAuto = $this->client->post("$this->urlFipe/ConsultarValorComTodosParametros", $query);
            } catch (GuzzleException $exception) {
                continue;
            }

            $bodyAuto = $getAuto->getBody()->getContents();


            if (empty($bodyAuto)) {
                continue;
            }

            $bodyDecode = Utils::jsonDecode($bodyAuto);

            if (is_object($bodyDecode) && property_exists($bodyDecode, 'erro')) {
                continue;
            }

            $arrayFipeVariation[] = array(
                'value' => str_replace(',', '.', str_replace('.', '', str_replace('R$ ', '', $bodyDecode->Valor))),
                'date'  => $this->getMonthByMonthComplete(trim($bodyDecode->MesReferencia))
            );
        }

        return $arrayFipeVariation;
    }

    private function getMonthByMonthComplete(string $month)
    {
        $monthStr = trim(explode(' de ', $month)[0]);

        switch ($monthStr) {
            case 'janeiro':
                $monthName = 'Jan';
                break;
            case 'fevereiro':
                $monthName = 'Fev';
                break;
            case 'março':
                $monthName = 'Mar';
                break;
            case 'abril':
                $monthName = 'Abr';
                break;
            case 'maio':
                $monthName = 'Mai';
                break;
            case 'junho':
                $monthName = 'Jun';
                break;
            case 'julho':
                $monthName = 'Jul';
                break;
            case 'agosto':
                $monthName = 'Ago';
                break;
            case 'setembro':
                $monthName = 'Set';
                break;
            case 'outubro':
                $monthName = 'Out';
                break;
            case 'novembro':
                $monthName = 'Nov';
                break;
            case 'dezembro':
                $monthName = 'Dez';
                break;
            default:
                $monthName = $monthStr;
        }

        return str_replace("$monthStr de ", "$monthName/" ,$month);
    }

}
