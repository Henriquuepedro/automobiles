<?php

namespace App\Http\Controllers\Admin\Automovel;

use App\Http\Controllers\Admin\ComplementarController;
use App\Http\Requests\AutomovelFormRequest;
use App\Http\Controllers\Controller;
use App\Models\Automovel\Automovel;
use App\Models\Automovel\ComplementarAuto;
use App\Models\Automovel\CorAuto;
use App\Models\Automovel\FuelAuto;
use App\Models\Automovel\Image;
use App\Models\Automovel\Opcional;
use App\Models\Automovel\EstadoFinanceiro;
use App\Models\Fipe\ControlAutos;
use App\Models\Fipe\FipeAuto;
use App\Models\Fipe\FipeBrand;
use App\Models\Fipe\FipeModel;
use App\Models\Fipe\FipeYear;
use App\Models\TemporaryFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Store;
use Intervention\Image\Facades\Image as ImageUpload;
use Illuminate\Support\Facades\File;
use Illuminate\Http\JsonResponse;

class AutomovelController extends Controller
{
    private $automovel;
    private $image;
    private $opcional;
    private $estadoFinanceiro;
    private $autoImagensController;
    private $autoOpcionalController;
    private $autoFinancialStatusController;
    private $complementarAuto;
    private $complementarController;
    private $corAuto;
    private $allColors;
    private $store;
    private $fuel;
    private $controlAutos;
    private $brandFipe;
    private $modelFipe;
    private $yearFipe;
    private $autoFipe;

    public function __construct(
        Automovel $automovel,
        Image $image,
        Opcional $opcional,
        EstadoFinanceiro $estadoFinanceiro,
        AutoImagensController $autoImagensController,
        AutoOpcionalController $autoOpcionalController,
        AutoFinancialStatusController $autoFinancialStatusController,
        ComplementarAuto $complementarAuto,
        ComplementarController $complementarController,
        CorAuto $corAuto,
        Store $store,
        FuelAuto $fuel,
        ControlAutos $controlAutos,
        FipeBrand $brandFipe,
        FipeModel $modelFipe,
        FipeYear $yearFipe,
        FipeAuto $autoFipe
    )
    {
        $this->automovel                    = $automovel;
        $this->image                        = $image;
        $this->opcional                     = $opcional;
        $this->estadoFinanceiro             = $estadoFinanceiro;
        $this->autoImagensController        = $autoImagensController;
        $this->autoOpcionalController       = $autoOpcionalController;
        $this->autoFinancialStatusController= $autoFinancialStatusController;
        $this->complementarAuto             = $complementarAuto;
        $this->complementarController       = $complementarController;
        $this->corAuto                      = $corAuto;
        $this->store                        = $store;
        $this->fuel                         = $fuel;
        $this->fuel                         = $fuel;
        $this->controlAutos                 = $controlAutos;
        $this->brandFipe                    = $brandFipe;
        $this->modelFipe                    = $modelFipe;
        $this->yearFipe                     = $yearFipe;
        $this->autoFipe                     = $autoFipe;

        $this->allColors = $this->corAuto->getAllColors();
    }

    public function index()
    {
        $dataAutos = [];
        $storesUser = $this->getStoresByUsers();

        $automoveis = $this->automovel->getAutosList($storesUser);

        foreach ($automoveis as $automovel) {
            $queryImage = $this->image->where([['auto_id', $automovel->id],['primaria', 1]])->get();
            $pathImage = count($queryImage) === 0 ? "assets/admin/dist/images/autos/no_image.png" : "assets/admin/dist/images/autos/{$queryImage[0]->folder}/thumbnail_{$queryImage[0]->arquivo}";
            $data = Array(
                'codauto'   => $automovel->id,
                'path'      => $pathImage,
                'marca'     => $automovel->marca_nome,
                'modelo'    => $automovel->modelo_nome,
                'ano'       => $automovel->ano_nome,
                'cor'       => ucfirst(CorAuto::getColorById($automovel->cor)),
                'valor'     => 'R$ ' . number_format($automovel->valor, 2, ',', '.'),
                'kms'       => number_format($automovel->kms, 0, ',', '.') . ' kms',
                'destaque'  => $automovel->destaque == 1,
                'store'     => $automovel->store_fancy,
                'active'    => $automovel->active
            );

            array_push($dataAutos, $data);
        }

        return view('admin.cadastros.automoveis.listagem', compact('dataAutos', 'storesUser'));
    }

    public function cadastro()
    {
        $dataAuto = new \StdClass();
        $dataAuto->colors       = $this->allColors;
        $dataAuto->stores       = $this->store->getStores($this->getStoresByUsers());
        $dataAuto->dataFuels    = $this->fuel->getAllFuelsActive();
        $dataAuto->controlAutos = $this->controlAutos->getAllControlsActive();

        return view('admin.cadastros.automoveis.cadastro', compact('dataAuto'));
    }

    public function store(AutomovelFormRequest $request): RedirectResponse
    {
        $dataForm = $request->all(); // Dados recuperado via POST

        // loja informado o usuário não tem permissão
        if (!isset($request->stores) || !in_array($request->stores, $this->getStoresByUsers())) {
            return redirect()
                ->route('admin.automoveis.cadastro')
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Não foi possível identificar a loja informada!');
        }

        DB::beginTransaction();// Iniciando transação manual para evitar insert não desejáveis

        // Cria array validado com nomes das colunas da tabela 'automoveis'
        // Insere dados do automovel
        $insertAutomovel = $this->automovel->insert($this->formatDataUpdateInsertAuto($dataForm, true));

        $codAutomovel = $insertAutomovel->id; // Recupera código inserido no banco

        $insertEstadoFinanceiro = $this->estadoFinanceiro->insert($this->autoFinancialStatusController->getDataFormatToInsert($dataForm, $codAutomovel)); // Insere estado financeiro do automóvel
        $insertComplementares   = $this->complementarAuto->insert($this->complementarController->getDataFormatToInsert($dataForm, $codAutomovel)); // Insere complementar automóvel
        $insertOpcionais        = $this->opcional->insert($this->autoOpcionalController->getDataFormatToInsert($dataForm, $codAutomovel)); // Insere dados dos opcionais do carro

        if ($insertAutomovel && $insertEstadoFinanceiro && $insertComplementares && $insertOpcionais) {

            // Insere imagens do automóvel
            if (!$this->autoImagensController->insert($dataForm, $codAutomovel)) {
                DB::rollBack();
                return redirect()
                    ->route('admin.automoveis.cadastro')
                    ->withInput()
                    ->with('typeMessage', 'error')
                    ->with('message', 'Ocorreu um problema para salvar as imagens do automóvel, reveja os dados e tente novamente!');
            }

            DB::commit();
            return redirect()
                ->route('admin.automoveis.listagem')
                ->with('typeMessage', 'success')
                ->with('message', 'Automóvel cadastrado com sucesso!');
        }
        else {
            DB::rollBack();
            return redirect()
                ->route('admin.automoveis.cadastro')
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Ocorreu um problema para realizar o cadastro do automóvel, reveja os dados e tente novamente!');
        }
    }

    public function update(AutomovelFormRequest $request): RedirectResponse
    {
        $dataForm = $request->all(); // Dados recuperado via POST
        $codAutomovel = $dataForm['idAuto']; // Código do automóvel

        // loja informado o usuário não tem permissão
        if (!isset($request->stores) || !in_array($request->stores, $this->getStoresByUsers())) {
            return redirect()
                ->route('admin.automoveis.edit', ['codAuto' => $codAutomovel])
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Não foi possível identificar a loja informada!');
        }

        DB::beginTransaction();// Iniciando transação manual para evitar updates não desejáveis

        $updateAutomovel        = $this->automovel->edit($this->formatDataUpdateInsertAuto($dataForm, false), $codAutomovel); // Atualiza dados do automovel
        $updateEstadoFinanceiro = $this->estadoFinanceiro->edit($this->autoFinancialStatusController->getDataFormatToInsert($dataForm, $codAutomovel)); // Atualiza estado financeiro do automóvel
        $updateComplementares   = $this->complementarAuto->edit($this->complementarController->getDataFormatToInsert($dataForm, $codAutomovel)); // Atualiza complementar automóvel
        $updateOpcionais        = $this->opcional->edit($this->autoOpcionalController->getDataFormatToInsert($dataForm, $codAutomovel)); // Atualiza dados dos opcionais do carro

        if ($updateAutomovel && $updateEstadoFinanceiro && $updateComplementares && $updateOpcionais) {

            // atualiza imagens do automóvel
            if (!$this->autoImagensController->edit($dataForm)) {
                DB::rollBack();
                return redirect()
                    ->route('admin.automoveis.edit', ['codAuto' => $codAutomovel])
                    ->withInput()
                    ->with('typeMessage', 'error')
                    ->with('message', 'Ocorreu um problema para realizar a atualização das imagens do automóvel, reveja os dados e tente novamente!');
            }

            DB::commit();
            return redirect()
                ->route('admin.automoveis.listagem')
                ->with('typeMessage', 'success')
                ->with('message', 'Automóvel alterado com sucesso!');
        }
        else{
            DB::rollBack();
            return redirect()
                ->route('admin.automoveis.edit', ['codAuto' => $codAutomovel])
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Ocorreu um problema para realizar a alteração do automóvel, reveja os dados e tente novamente!');
        }
    }

    public function edit(int $codAuto)
    {
        $data = $this->automovel->getAutomovelComplete($codAuto);

        if (!$data) {
            return redirect()->route('admin.automoveis.listagem');
        }

        // format datas
        $dataAuto = new \StdClass();
        $dataAuto->tipoAuto     = $data->tipo_auto;
        $dataAuto->codAuto      = $data->auto_id;
        $dataAuto->idMarca      = $data->marca_id;
        $dataAuto->idModelo     = $data->modelo_id;
        $dataAuto->idAno        = $data->ano_id;
        $dataAuto->cor          = $data->cor;
        $dataAuto->valor        = number_format($data->valor, 2, ',', '.');
        $dataAuto->kms          = number_format($data->kms, 0, ',', '.');
        $dataAuto->unicoDono    = $data->unico_dono;
        $dataAuto->aceitaTroca  = $data->aceita_troca;
        $dataAuto->placa        = $data->placa;
        $dataAuto->motor        = $data->motor;
        $dataAuto->tipoCarro    = $data->tipo_carro;
        $dataAuto->destaque     = $data->destaque;
        $dataAuto->colors       = $this->allColors;
        $dataAuto->storeSelected= $data->store_id;
        $dataAuto->stores       = $this->store->getStores($this->getStoresByUsers());
        $dataAuto->code_auto_fipe= $data->code_auto_fipe;
        $dataAuto->reference    = $data->reference;
        $dataAuto->observation  = $data->observation;
        $dataAuto->active       = $data->active == 1;
        $dataAuto->fuel         = $data->fuel;
        $dataAuto->dataFuels    = $this->fuel->getAllFuelsActive();
        $dataAuto->controlAutos = $this->controlAutos->getAllControlsActive();
        $dataAuto->folder_images= empty($data->folder_images) ? uniqid() : $data->folder_images;

        $dataAuto->brandsFipe   = $this->brandFipe->getAllBrandByAuto($data->tipo_auto);
        $dataAuto->modelsFipe   = $this->modelFipe->getAllModelByAutoAndBrand($data->tipo_auto, $data->marca_id);
        $dataAuto->yearsFipe    = $this->yearFipe->getAllYearByAutoAndBrandAndModel($data->tipo_auto, $data->marca_id, $data->modelo_id);
        $dataAuto->autoFipe     = $this->autoFipe->getAllAutoByAutoAndBrandAndModelAndYear($data->tipo_auto, $data->marca_id, $data->modelo_id, $data->ano_id);

        // remove arquivos temporário desse automóvel
        foreach (TemporaryFile::where([
            'origin'    => 'autos',
            'ip'        => \Request::ip(),
            'user_id'   => Auth::user()->id
        ])->get() as $imageTemp) {
            $pathTemp = "assets/admin/dist/images/autos/temp/{$imageTemp->folder}/{$imageTemp->filename}";
            if (File::exists($pathTemp)) {
                File::delete($pathTemp);
            }

            TemporaryFile::where([
                'origin'    => 'autos',
                'folder'    => $imageTemp->folder,
                'filename'  => $imageTemp->filename,
                'ip'        => \Request::ip(),
                'user_id'   => Auth::user()->id
            ])->delete();
        }

        return view('admin.cadastros.automoveis.alterar', compact('dataAuto'));
    }

    /*
    public function delete(): string
    {
        $delete = $this->automovel
            ->where('id', 1)
            ->delete();

        if ($delete) {
            return 'Excluído com sucesso';
        }

        return 'Falha ao excluir';
    }
    */

    private function formatDataUpdateInsertAuto(array $dataForm, bool $isCreate): array
    {
        return array(
            'tipo_auto'     => filter_var($dataForm['autos'], FILTER_SANITIZE_STRING),
            'valor'         => filter_var(str_replace(',' , '.', str_replace('.', '', $dataForm['valor'])), FILTER_VALIDATE_FLOAT),
            'cor'           => filter_var($dataForm['cor'], FILTER_SANITIZE_STRING),
            'unico_dono'    => isset($dataForm['unicoDono']),
            'aceita_troca'  => isset($dataForm['aceitaTroca']),
            'placa'         => filter_var($dataForm['placa'], FILTER_SANITIZE_STRING),
            'final_placa'   => (int)substr($dataForm['placa'], -1),
            'kms'           => filter_var(str_replace('.' , '', $dataForm['quilometragem']), FILTER_VALIDATE_INT),
            'destaque'      => isset($dataForm['destaque']),
            'company_id'    => Auth::user()->company_id,
            'store_id'      => filter_var($dataForm['stores'], FILTER_VALIDATE_INT),
            'code_auto_fipe'=> filter_var($dataForm['codeFipe'], FILTER_SANITIZE_STRING),
            'reference'     => filter_var($dataForm['reference']),
            'observation'   => filter_var($dataForm['observation']),
            'active'        => isset($dataForm['active']),
            'fuel'          => filter_var($dataForm['fuel'], FILTER_VALIDATE_INT),
            'folder_images' => filter_var($dataForm['path-file-image'], FILTER_SANITIZE_STRING),

            $isCreate ? 'user_created' : 'user_updated'  => Auth::user()->id,
        );
    }

    /**
     * Upload de arquivos na biblioteca CKEditor
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadImagesObsAuto(Request $request): JsonResponse
    {
        if ($request->hasFile('upload')) {
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = md5(uniqid(rand(), true)) . ".$extension";

            $request->file('upload')->move(public_path('assets/admin/dist/images/obs_autos'), $fileName);

            $CKEditorFuncNum = $request->input('CKEditorFuncNum');
            $url = asset('assets/admin/dist/images/obs_autos/'.$fileName);
            $msg = 'Image uploaded successfully';
            $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";

            @header('Content-type: text/html; charset=utf-8');
            return response()->json($response);
        }

        return response()->json($request->file('upload'));
    }

    public function setUploadImage(Request $request)
    {
        $folder = $request->path;
        $fileName = 'temp.png';

        if ($request->hasFile('filepond')) {
            $file       = $request->file('filepond');
            $extension  = $file->getClientOriginalExtension();
            $fileName   = $file->getClientOriginalName();

            if (!in_array($file->getMimeType(), array(
                'image/apng',
                'image/avif',
                'image/gif',
                'image/jpeg',
                'image/png',
                'image/svg+xml',
                'image/webp',
                'image/bmp',
                'image/tiff',
            ))) {
                throw new \Exception('Imagem em um formato inválido');
            }

            $uploadPath = "assets/admin/dist/images/autos/temp/{$folder}";

            if (!File::exists($uploadPath)) File::makeDirectory($uploadPath);

            ImageUpload::make($file)
                ->fit(2400, 1800, function ($constraint) {
                    $constraint->upsize();
                })
                ->save("{$uploadPath}/{$fileName}");

            TemporaryFile::create([
                'origin'    => 'autos',
                'folder'    => $folder,
                'filename'  => $fileName,
                'action'    => 'create',
                'ip'        => $request->ip(),
                'user_id'   => $request->user()->id
            ]);
        }

        return ['key' => "{$folder}/{$fileName}", 'name' => $fileName];
    }

    public function rmUploadImage(Request $request)
    {
        $response       = new \stdClass();
        $filePath       = strip_tags(file_get_contents("php://input"));
        $filePath       = json_decode($filePath);

        $uploadPath     = "assets/admin/dist/images/autos";
        $expPathAndFile = explode('/', $filePath->key);
        $pathImage      = $expPathAndFile[0] ?? null;
        $fileImage      = $expPathAndFile[1] ?? null;


        if (isset($filePath->key) && !empty($filePath->key) && count($expPathAndFile) === 2) {

            $response->id       = $filePath;
            $response->success  = true;

            if (File::exists("{$uploadPath}/temp/{$pathImage}/{$fileImage}")) {

                File::delete("{$uploadPath}/temp/{$pathImage}/{$fileImage}");
                TemporaryFile::where([
                    'origin'    => 'autos',
                    'folder'    => $pathImage,
                    'filename'  => $fileImage,
                    'ip'        => $request->ip(),
                    'user_id'   => $request->user()->id
                ])->delete();
            } elseif (File::exists("{$uploadPath}/{$pathImage}/{$fileImage}")) {
                TemporaryFile::create([
                    'origin' => 'autos',
                    'folder' => $pathImage,
                    'filename' => $fileImage,
                    'action' => 'delete',
                    'ip' => $request->ip(),
                    'user_id' => $request->user()->id
                ]);
            }
            else {
                $response = false;
            }
        } else {
            $response = false;
        }

        return response()->json($response);
    }

    public function getUploadImage(int $auto)
    {
        $data = $this->automovel->getAutomovelComplete($auto);

        // loja informado o usuário não tem permissão
        if (!$data || !isset($data->store_id) || !in_array($data->store_id, $this->getStoresByUsers())) {
            return response()->json([]);
        }

        $images = array();

        foreach ($this->image->getImageByAuto($auto) as $image) {
            $size = File::size("assets/admin/dist/images/autos/{$image->folder}/{$image->arquivo}");

            array_push($images, [
                'folder' => $image->folder,
                'file'  => $image->arquivo,
                'size'  => $size,
            ]);
        }

        return response()->json($images);
    }

    public function getQtyStockByBrands()
    {
        $autos = $this->automovel->getAutosList($this->getStoresByUsers(), array('marca_nome', 'ASC'));
        $arrQtys = array();

        foreach ($autos as $auto) {
            if (!$auto['active']) {
                continue;
            }

            if (array_key_exists($auto['marca_nome'], $arrQtys)) {
                $arrQtys[$auto['marca_nome']] += 1;
            } else {
                $arrQtys[$auto['marca_nome']] = 1;
            }
        }

        uasort($arrQtys, function ($a, $b) {
            return $b - $a;
        });

        return response()->json(array(
            'total' => count($autos),
            'data' => $arrQtys
        ));
    }

    public function getQtyStockByAutos()
    {
        $autos = $this->automovel->getAutosList($this->getStoresByUsers());
        $controlAutos = $this->controlAutos->getAllControlsActive();
        $arrQtys = array();

        foreach ($autos as $auto) {
            if (!$auto['active']) {
                continue;
            }

            if (array_key_exists($auto['tipo_auto'], $arrQtys)) {
                $arrQtys[$auto['tipo_auto']] += 1;
            } else {
                $arrQtys[$auto['tipo_auto']] = 1;
            }
        }

        foreach ($controlAutos as $controlAuto) {
            if (array_key_exists($controlAuto['code_str'], $arrQtys)) {
                $dataValue = array('value' => $arrQtys[$controlAuto['code_str']]);
                switch ($controlAuto['code_str']) {
                    case 'carros':
                        $dataValue['icon'] = 'fa fa-car';
                        break;
                    case 'motos':
                        $dataValue['icon'] = 'fa fa-motorcycle';
                        break;
                    case 'caminhoes':
                        $dataValue['icon'] = 'fa fa-truck';
                        break;
                }

                $arrQtys[$controlAuto['name']] = $dataValue;
                unset($arrQtys[$controlAuto['code_str']]);
            }
        }

        return response()->json($arrQtys);
    }

    public function getPriceStockByAutos()
    {
        $autos = $this->automovel->getAutosList($this->getStoresByUsers());
        $controlAutos = $this->controlAutos->getAllControlsActive();
        $arrQtys = array();

        foreach ($autos as $auto) {
            if (!$auto['active']) {
                continue;
            }

            if (array_key_exists($auto['tipo_auto'], $arrQtys)) {
                $arrQtys[$auto['tipo_auto']] += $auto['valor'];
            } else {
                $arrQtys[$auto['tipo_auto']] = $auto['valor'];
            }
        }

        foreach ($controlAutos as $controlAuto) {
            if (array_key_exists($controlAuto['code_str'], $arrQtys)) {
                $dataValue = array('value' => 'R$ '.number_format($arrQtys[$controlAuto['code_str']], 2 , ',', '.'));
                switch ($controlAuto['code_str']) {
                    case 'carros':
                        $dataValue['icon'] = 'fa fa-car';
                        break;
                    case 'motos':
                        $dataValue['icon'] = 'fa fa-motorcycle';
                        break;
                    case 'caminhoes':
                        $dataValue['icon'] = 'fa fa-truck';
                        break;
                }

                $arrQtys[$controlAuto['name']] = $dataValue;
                unset($arrQtys[$controlAuto['code_str']]);
            }
        }

        return response()->json($arrQtys);
    }
}
