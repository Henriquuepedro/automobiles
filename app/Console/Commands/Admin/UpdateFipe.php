<?php

namespace App\Console\Commands\Admin;

use App\Models\Fipe\ControlAutos;
use App\Models\Fipe\FipeAuto;
use App\Models\Fipe\FipeBrand;
use App\Models\Fipe\FipeModel;
use App\Models\Fipe\FipeYear;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Utils;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class UpdateFipe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:fipe {brand_start : Código da marca de início} {brand_end : Código da marca de fim}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update FIPE table data';

    private FipeBrand $brand;
    private FipeModel $model;
    private FipeYear $year;
    private FipeAuto $auto;
    private ControlAutos $controlAutos;
    private string $urlFipe = 'veiculos.fipe.org.br/api/veiculos';
    private Client $client;
    private int $codeReference;
    private array $headerGuzzle = array('Host' => 'veiculos.fipe.org.br', 'Referer' => 'http://veiculos.fipe.org.br', 'Content-Type' => 'application/json');

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        FipeBrand $brand,
        FipeModel $model,
        FipeYear $year,
        FipeAuto $auto,
        ControlAutos $controlAutos
    ) {
        parent::__construct();
        $this->brand        = $brand;
        $this->model        = $model;
        $this->year         = $year;
        $this->auto         = $auto;
        $this->controlAutos = $controlAutos;

        $this->setProtocolEndpoint();
    }

    private function setProtocolEndpoint()
    {
        if (env('APP_ENV') === 'production') {
            $this->urlFipe = "https://$this->urlFipe";
        } else {
            $this->urlFipe = "http://$this->urlFipe";
        }
    }

    /**
     * Execute the console command.
     *
     * @return bool
     * @throws GuzzleException
     */
    public function handle(): bool
    {
        $brand_start = $this->argument('brand_start');
        $brand_end   = $this->argument('brand_end');

        $nameFile = date('Y') . '-' . date('m') . '-' . date('d') . '-' . date('H') . '-' . date('i') . "[$brand_start-$brand_end]";

        config(['logging.channels.update_fipe_debug.path' => storage_path("logs/debug/$nameFile.log")]);
        config(['logging.channels.update_fipe_info.path' => storage_path("logs/info/$nameFile.log")]);
        config(['logging.channels.update_fipe_error.path' => storage_path("logs/error/$nameFile.log")]);

        try {
            $this->setClient();
            $this->setReference();
        } catch (Exception $exception) {
            echo "[ERROR] {$exception->getMessage()}\n";
            Log::channel('update_fipe_error')->error("[ERROR] {$exception->getMessage()}");
            return false;
        }

        foreach ($this->controlAutos->getAllControlsActive() as $control) {

            $autoCode = $control->code;
            $autoCodeStr = $control->code_str;

            echo "Buscando por: $autoCode\n";

            try {
                $query = array(
                    'headers' => $this->headerGuzzle,
                    'json' => array(
                        'codigoTabelaReferencia' => $this->codeReference,
                        'codigoTipoVeiculo'      => $autoCode
                    )
                );

                $getBrands = $this->client->post("$this->urlFipe/ConsultarMarcas", $query);
            } catch (Exception $e) {
                Log::channel('update_fipe_error')->error("[$brand_start -> $brand_end] " . $e->getMessage());
                continue;
            }

            $bodyBrand = $getBrands->getBody()->getContents();

            if ($this->checkError($bodyBrand)) {
                echo "[ MARCA ] Tem erro na requisição $bodyBrand\n";
                Log::channel('update_fipe_error')->error("[$brand_start -> $brand_end] [MARCA] Tem erro na requisição $bodyBrand");
                continue;
            }

            foreach (Utils::jsonDecode($bodyBrand) as $brand) {

                echo "[ MARCA ] $brand->Value - $brand->Label\n";
                $brandId = $this->brand->getIdAndCheckBrandCorrect($autoCodeStr, $brand->Value, $brand->Label);

                if ($brandId < $brand_start || $brandId > $brand_end) {
                    echo "[ MARCA ] Ignorou brand: $brandId\n";
                    continue;
                }

                try {
                    $query['json']['codigoMarca'] = $brand->Value;
                    $getModels = $this->client->post("$this->urlFipe/ConsultarModelos", $query);
                } catch (Exception $e) {
                    Log::channel('update_fipe_error')->error("[$brand_start -> $brand_end] " . $e->getMessage());
                    continue;
                }

                $bodyModel = $getModels->getBody()->getContents();

                if ($this->checkError($bodyModel)) {
                    echo "[MODELO ] Tem erro na requisição $bodyModel\n";
                    Log::channel('update_fipe_error')->error("[$brand_start -> $brand_end] [MODELO] Tem erro na requisição $bodyModel");
                    continue;
                }

                foreach (Utils::jsonDecode($bodyModel)->Modelos as $model) {
                    echo "[MODELO ] - BRAND_BD=$brandId $model->Value - $model->Label\n";
                    $modelId = $this->model->getIdAndCheckModelCorrect($autoCodeStr, $brandId, $model->Value, $model->Label);

                    try {
                        $query['json']['codigoModelo'] = $model->Value;

                        $getYears = $this->client->post("$this->urlFipe/ConsultarAnoModelo", $query);
                    } catch (Exception $e) {
                        Log::channel('update_fipe_error')->error("[$brand_start -> $brand_end] " . $e->getMessage());
                        continue;
                    }

                    $bodyYear = $getYears->getBody()->getContents();

                    if ($this->checkError($bodyYear)) {
                        echo "[  ANO  ] Tem erro na requisição $bodyYear\n";
                        Log::channel('update_fipe_error')->error("[$brand_start -> $brand_end] [ANO] Tem erro na requisição $bodyYear");
                        continue;
                    }

                    foreach (Utils::jsonDecode($bodyYear) as $year) {
                        echo "[  ANO  ] - BRAND_BD=$brandId - MODEL_BD=$modelId $year->Value - $year->Label\n";
                        $yearId = $this->year->getIdAndCheckYearCorrect($autoCodeStr, $brandId, $modelId, $year->Value, $year->Label);

                        try {
                            $expYear = explode('-', $year->Value);

                            if (count($expYear) !== 2) {
                                Log::channel('update_fipe_error')->error("[$brand_start -> $brand_end] Não foi possível fazer a divisão do código do ano do veículo ($year->Value)" . Utils::jsonEncode($query));
                                continue;
                            }

                            $query['json']['anoModelo'] = $expYear[0];
                            $query['json']['codigoTipoCombustivel'] = $expYear[1];
                            $query['json']['tipoConsulta'] = 'tradicional';
                            $getAuto = $this->client->post("$this->urlFipe/ConsultarValorComTodosParametros", $query);
                        } catch (Exception $e) {
                            Log::channel('update_fipe_error')->error("[$brand_start -> $brand_end] " . $e->getMessage());
                            continue;
                        }

                        $bodyAuto = $getAuto->getBody()->getContents();

                        if ($this->checkError($bodyAuto)) {
                            echo "[ AUTO  ] Tem erro na requisição $bodyAuto\n";
                            Log::channel('update_fipe_error')->error("[$brand_start -> $brand_end] [ANO] Tem erro na requisição $bodyAuto");
                            continue;
                        }

                        echo "[ AUTO  ] - BRAND_BD=$brandId - MODEL_BD=$modelId - YEAR_BD=$yearId $bodyAuto\n";

                        $dataAutoFipe = Utils::jsonDecode($bodyAuto);

                        Log::channel('update_fipe_debug')->debug("[$brand_start -> $brand_end] ".json_encode(['auto' => $autoCodeStr, 'brandId' => $brandId, 'modelId' => $modelId, 'yearId' => $yearId, 'body' => $dataAutoFipe]));

                        if (
                            empty($dataAutoFipe->Valor ?? null) ||
                            empty($dataAutoFipe->Marca ?? null) ||
                            empty($dataAutoFipe->Modelo ?? null) ||
                            empty($dataAutoFipe->AnoModelo ?? null)
                        ) {
                            Log::channel('update_fipe_error')->error("[$brand_start -> $brand_end] Não encontrou dados do veículo\n$this->urlFipe/$autoCodeStr/marcas/$brand->Value/modelos/$model->Value/anos/$year->Value\n$bodyAuto\n");
                            continue;
                        }

                        $dataAutoSystem = array(
                            'type_auto'         => $autoCodeStr,
                            'value'             => str_replace(',', '.', str_replace('.', '', str_replace('R$ ', '', $dataAutoFipe->Valor ?? 0))),
                            'brand_name'        => $dataAutoFipe->Marca ?? '',
                            'model_name'        => $dataAutoFipe->Modelo ?? '',
                            'year_name'         => str_replace('32000', 'Zero Km', $dataAutoFipe->AnoModelo ?? ''),
                            'fuel'              => $dataAutoFipe->Combustivel ?? '',
                            'code_fipe'         => $dataAutoFipe->CodigoFipe ?? '',
                            'type_auto_id'      => $dataAutoFipe->TipoVeiculo ?? '',
                            'initials_fuel'     => $dataAutoFipe->SiglaCombustivel ?? '',
                            'brand_id'          => $brandId,
                            'model_id'          => $modelId,
                            'year_id'           => $yearId,
                        );

                        $response = $this->auto->getIdAndCheckAutoCorrect($autoCodeStr, $brandId, $modelId, $yearId, $dataAutoSystem);

                        echo "[ AUTO  ] - BRAND_BD=$brandId - MODEL_BD=$modelId - YEAR_BD=$yearId - response_validate=".json_encode($response)."\n";

                        if ($response !== null) {
                            Log::channel('update_fipe_info')->info("[$brand_start -> $brand_end] [$response] " . json_encode(['auto' => $autoCodeStr, 'brandId' => $brandId, 'modelId' => $modelId, 'yearId' => $yearId, 'data' => $dataAutoSystem]));
                        }
                    }
                }
            }
        }

        return true;
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
    private function setReference()
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

        $this->codeReference = $body[0]->Codigo;
    }

    private function checkError(string $body): bool
    {
        if (empty($body)) {
            return true;
        }

        $bodyDecode = Utils::jsonDecode($body);

        if (is_object($bodyDecode) && property_exists($bodyDecode, 'erro')) {
            return true;
        }

        return false;
    }
}
