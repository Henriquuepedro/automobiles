<?php

namespace App\Console\Commands\Admin;

use App\Models\Fipe\ControlAutos;
use App\Models\Fipe\FipeAuto;
use App\Models\Fipe\FipeBrand;
use App\Models\Fipe\FipeModel;
use App\Models\Fipe\FipeYear;
use GuzzleHttp\Exception\GuzzleException;
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
    protected $signature = 'update:fipe {brand_start : Código da marca de ínicio} {brand_end : Código da marca de fim}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update FIPE table data';

    private $brand;
    private $model;
    private $year;
    private $auto;
    private $controlAutos;
    private $urlFipe = 'parallelum.com.br/fipe/api/v1';

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
        if (env('APP_ENV') === 'production') $this->urlFipe = "https://{$this->urlFipe}";
        else $this->urlFipe = "http://{$this->urlFipe}";
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
        $client = new Client();

//      foreach (['carros', 'motos', 'caminhoes'] as $auto) {
        foreach ($this->controlAutos->getAllControlsActive() as $control) {

            $auto = $control->code_str;

            echo "Buscanco por: {$auto}\n";

            try {
                $getBrands = $client->request('GET', "{$this->urlFipe}/{$auto}/marcas");
            } catch (\Exception $e) {
                Log::channel('update_fipe_error')->error("[{$brand_start} -> {$brand_end}] " . $e->getMessage());
                continue;
            }

            if ($getBrands->getStatusCode() === 200) {
                foreach (json_decode($getBrands->getBody()) as $brand) {

                    echo "[ MARCA ] {$brand->codigo} - {$brand->nome}\n";
                    $brandId = $this->brand->getIdAndCheckBrandCorrect($auto, $brand->codigo, $brand->nome);

                    if ($brandId < $brand_start || $brandId > $brand_end) {
                        echo "[ MARCA] Ignorou brand: {$brandId}\n";
                        continue;
                    }

                    try {
                        $getModels = $client->request('GET', "{$this->urlFipe}/{$auto}/marcas/{$brand->codigo}/modelos");
                    } catch (\Exception $e) {
                        Log::channel('update_fipe_error')->error("[{$brand_start} -> {$brand_end}] " . $e->getMessage());
                        continue;
                    }

                    if ($getModels->getStatusCode() === 200) {
                        foreach (json_decode($getModels->getBody())->modelos as $model) {

                            echo "[MODELO ] - BRAND_BD={$brandId} {$model->codigo} - {$model->nome}\n";
                            $modelId = $this->model->getIdAndCheckModelCorrect($auto, $brandId, $model->codigo, $model->nome);

                            try {
                                $getYears = $client->request('GET', "{$this->urlFipe}/{$auto}/marcas/{$brand->codigo}/modelos/{$model->codigo}/anos");
                            } catch (\Exception $e) {
                                Log::channel('update_fipe_error')->error("[{$brand_start} -> {$brand_end}] " . $e->getMessage());
                                continue;
                            }

                            if ($getYears->getStatusCode() === 200) {
                                foreach (json_decode($getYears->getBody()) as $year) {

                                    echo "[  ANO  ] - BRAND_BD={$brandId} - MODEL_BD={$modelId} {$year->codigo} - {$year->nome}\n";

                                    $yearId = $this->year->getIdAndCheckYearCorrect($auto, $brandId, $modelId, $year->codigo, str_replace('32000', date('Y'), $year->nome));

                                    try {
                                        $getAuto = $client->request('GET', "{$this->urlFipe}/{$auto}/marcas/{$brand->codigo}/modelos/{$model->codigo}/anos/{$year->codigo}");
                                    } catch (\Exception $e) {
                                        Log::channel('update_fipe_error')->error("[{$brand_start} -> {$brand_end}] " . $e->getMessage());
                                        continue;
                                    }

                                    if ($getAuto->getStatusCode() === 200) {

                                        echo "[  ANO  ] - BRAND_BD={$brandId} - MODEL_BD={$modelId} - YEAR_BD={$yearId} {$getAuto->getBody()}\n";

                                        $dataAutoFipe = json_decode($getAuto->getBody());

                                        if (
                                            !isset($dataAutoFipe->Valor) ||
                                            empty($dataAutoFipe->Valor) ||
                                            !isset($dataAutoFipe->Marca) ||
                                            empty($dataAutoFipe->Marca) ||
                                            !isset($dataAutoFipe->Modelo) ||
                                            empty($dataAutoFipe->Modelo) ||
                                            !isset($dataAutoFipe->AnoModelo) ||
                                            empty($dataAutoFipe->AnoModelo)
                                        ) {
                                            Log::channel('update_fipe_error')->error("[{$brand_start} -> {$brand_end}] Não encontrou dados do veículo\n{$this->urlFipe}/{$auto}/marcas/{$brand->codigo}/modelos/{$model->codigo}/anos/{$year->codigo}\n{$getAuto->getBody()}\n");
                                            continue;
                                        }

                                        $dataAutoSystem = array(
                                            'type_auto'         => $auto,
                                            'value'             => str_replace(',', '.', str_replace('.', '', str_replace('R$ ', '', $dataAutoFipe->Valor ?? 0))),
                                            'brand_name'        => $dataAutoFipe->Marca ?? '',
                                            'model_name'        => $dataAutoFipe->Modelo ?? '',
                                            'year_name'         => str_replace('32000', date('Y'), $dataAutoFipe->AnoModelo ?? ''),
                                            'fuel'              => $dataAutoFipe->Combustivel ?? '',
                                            'code_fipe'         => $dataAutoFipe->CodigoFipe ?? '',
                                            'type_auto_id'      => $dataAutoFipe->TipoVeiculo ?? '',
                                            'initials_fuel'     => $dataAutoFipe->SiglaCombustivel ?? '',
                                            'brand_id'          => $brandId,
                                            'model_id'          => $modelId,
                                            'year_id'           => $yearId,
                                        );

                                        $response = $this->auto->getIdAndCheckAutoCorrect($auto, $brandId, $modelId, $yearId, $dataAutoSystem);

                                        echo "\n\nresponse validate ".json_encode($response)."\n\n";

                                        if ($response !== null) {
                                            Log::channel('update_fipe_info')->info("[{$brand_start} -> {$brand_end}] [$response] " . json_encode([$auto, $brandId, $modelId, $yearId, $dataAutoSystem]));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return true;
    }
}
