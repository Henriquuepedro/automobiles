<?php

namespace App\Http\Controllers\Admin\Automobile;

use App\Http\Controllers\Admin\ComplementaryController;
use App\Http\Requests\AutomobileFormRequest;
use App\Http\Controllers\Controller;
use App\Models\Automobile\Automobile;
use App\Models\Automobile\ComplementaryAuto;
use App\Models\Automobile\ColorAuto;
use App\Models\Automobile\FuelAuto;
use App\Models\Automobile\Image;
use App\Models\Automobile\Optional;
use App\Models\Automobile\FinancialState;
use App\Models\Fipe\ControlAutos;
use App\Models\Fipe\FipeAuto;
use App\Models\Fipe\FipeBrand;
use App\Models\Fipe\FipeModel;
use App\Models\Fipe\FipeYear;
use App\Models\TemporaryFile;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Store;
use Intervention\Image\Facades\Image as ImageUpload;
use Illuminate\Support\Facades\File;
use Illuminate\Http\JsonResponse;
use StdClass;

class AutomobileController extends Controller
{
    private Automobile $automobile;
    private Image $image;
    private Optional $optional;
    private FinancialState $financialState;
    private AutoImagesController $autoImagesController;
    private AutoOptionalController $autoOptionalController;
    private AutoFinancialStatusController $autoFinancialStatusController;
    private ComplementaryAuto $complementaryAuto;
    private ComplementaryController $complementaryController;
    private ColorAuto $colorAuto;
    private Store $store;
    private FuelAuto $fuel;
    private ControlAutos $controlAutos;
    private FipeBrand $brandFipe;
    private FipeModel $modelFipe;
    private FipeYear $yearFipe;
    private FipeAuto $autoFipe;
    private $allColors;

    public function __construct(
        Automobile $automobile,
        Image $image,
        Optional $optional,
        FinancialState $financialState,
        AutoImagesController $autoImagesController,
        AutoOptionalController $autoOptionalController,
        AutoFinancialStatusController $autoFinancialStatusController,
        ComplementaryAuto $complementaryAuto,
        ComplementaryController $complementaryController,
        ColorAuto $colorAuto,
        Store $store,
        FuelAuto $fuel,
        ControlAutos $controlAutos,
        FipeBrand $brandFipe,
        FipeModel $modelFipe,
        FipeYear $yearFipe,
        FipeAuto $autoFipe
    )
    {
        $this->automobile                       = $automobile;
        $this->image                            = $image;
        $this->optional                         = $optional;
        $this->financialState                   = $financialState;
        $this->autoImagesController             = $autoImagesController;
        $this->autoOptionalController           = $autoOptionalController;
        $this->autoFinancialStatusController    = $autoFinancialStatusController;
        $this->complementaryAuto                = $complementaryAuto;
        $this->complementaryController          = $complementaryController;
        $this->colorAuto                        = $colorAuto;
        $this->store                            = $store;
        $this->fuel                             = $fuel;
        $this->fuel                             = $fuel;
        $this->controlAutos                     = $controlAutos;
        $this->brandFipe                        = $brandFipe;
        $this->modelFipe                        = $modelFipe;
        $this->yearFipe                         = $yearFipe;
        $this->autoFipe                         = $autoFipe;

        $this->allColors = $this->colorAuto->getAllColors();
    }

    public function index()
    {
        $storesUser = $this->getStoresByUsers();

        $filter = array();
        $filter['brand'] = $this->automobile->getBrandsFilter($storesUser);
        $filter['price'] = $this->automobile->getFilterRangePrice($storesUser);

        return view('admin.automobile.index', compact('storesUser', 'filter'));
    }

    public function cadastro()
    {
        $dataAuto = new StdClass();
        $dataAuto->colors       = $this->allColors;
        $dataAuto->stores       = $this->store->getStores($this->getStoresByUsers());
        $dataAuto->dataFuels    = $this->fuel->getAllFuelsActive();
        $dataAuto->controlAutos = $this->controlAutos->getAllControlsActive();

        return view('admin.automobile.create', compact('dataAuto'));
    }

    public function store(AutomobileFormRequest $request): RedirectResponse
    {
        $dataForm = $request->all(); // Dado recuperado via POST

        // Loja informada ou usuário não tem permissão
        if (!$request->has('stores') || !in_array($request->input('stores', array()), $this->getStoresByUsers())) {
            return redirect()
                ->route('admin.automobiles.cadastro')
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Não foi possível identificar a loja informada!');
        }

        DB::beginTransaction();// Iniciando transação manual para evitar insert não desejáveis

        // Cria array validado com nomes das colunas da tabela 'automobiles.'
        // Insere dados do automóvel
        $insertAutomobiles = $this->automobile->insert($this->formatDataUpdateInsertAuto($dataForm, true));

        $autoId = $insertAutomobiles->id; // Recupera código inserido no banco

        $insertFinancialState = $this->financialState->insert($this->autoFinancialStatusController->getDataFormatToInsert($dataForm, $autoId)); // Insere estado financeiro do automóvel
        $insertComplementares   = $this->complementaryAuto->insert($this->complementaryController->getDataFormatToInsert($dataForm, $autoId)); // Insere complementar automóvel
        $insertOpcionais        = $this->optional->insert($this->autoOptionalController->getDataFormatToInsert($dataForm, $autoId)); // Insere dados dos opcionais do carro

        if ($insertAutomobiles && $insertFinancialState && $insertComplementares && $insertOpcionais) {
            // Insere imagens do automóvel
            if (!$this->autoImagesController->insert($dataForm, $autoId)) {
                DB::rollBack();
                return redirect()
                    ->route('admin.automobiles.cadastro')
                    ->withInput()
                    ->with('typeMessage', 'error')
                    ->with('message', 'Ocorreu um problema para salvar as imagens do automóvel, reveja os dados e tente novamente!');
            }

            DB::commit();
            return redirect()
                ->route('admin.automobiles.index')
                ->with('typeMessage', 'success')
                ->with('message', 'Automóvel cadastrado com sucesso!');
        }
        else {
            DB::rollBack();
            return redirect()
                ->route('admin.automobiles.cadastro')
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Ocorreu um problema para realizar o cadastro do automóvel, reveja os dados e tente novamente!');
        }
    }

    public function update(AutomobileFormRequest $request): RedirectResponse
    {
        $dataForm = $request->all(); // Dados recuperado via POST
        $autoId = $dataForm['idAuto']; // Código do automóvel

        // Loja informada ou usuário não tem permissão
        if (!$request->has('stores') || !in_array($request->input('stores', array()), $this->getStoresByUsers())) {
            return redirect()
                ->route('admin.automobiles.edit', ['codAuto' => $autoId])
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Não foi possível identificar a loja informada!');
        }

        DB::beginTransaction();// Iniciando transação manual para evitar updates não desejáveis

        $updateAutomobiles        = $this->automobile->edit($this->formatDataUpdateInsertAuto($dataForm, false), $autoId); // Atualiza dados do automovel
        $updateFinancialState   = $this->financialState->edit($this->autoFinancialStatusController->getDataFormatToInsert($dataForm, $autoId)); // Atualiza estado financeiro do automóvel
        $updateComplementares   = $this->complementaryAuto->edit($this->complementaryController->getDataFormatToInsert($dataForm, $autoId)); // Atualiza complementar automóvel
        $updateOpcionais        = $this->optional->edit($this->autoOptionalController->getDataFormatToInsert($dataForm, $autoId)); // Atualiza dados dos opcionais do carro

        if ($updateAutomobiles && $updateFinancialState && $updateComplementares && $updateOpcionais) {
            // atualiza imagens do automóvel
            if (!$this->autoImagesController->edit($dataForm)) {
                DB::rollBack();
                return redirect()
                    ->route('admin.automobiles.edit', ['codAuto' => $autoId])
                    ->withInput()
                    ->with('typeMessage', 'error')
                    ->with('message', 'Ocorreu um problema para realizar a atualização das imagens do automóvel, reveja os dados e tente novamente!');
            }

            DB::commit();
            return redirect()
                ->route('admin.automobiles.index')
                ->with('typeMessage', 'success')
                ->with('message', 'Automóvel alterado com sucesso!');
        }
        else {
            DB::rollBack();
            return redirect()
                ->route('admin.automobiles.edit', ['codAuto' => $autoId])
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Ocorreu um problema para realizar a alteração do automóvel, reveja os dados e tente novamente!');
        }
    }

    public function edit(int $codAuto)
    {
        $data = $this->automobile->getAutomobileComplete($codAuto);

        if (!$data) {
            return redirect()->route('admin.automobiles.index');
        }

        $user = Auth::user();

        // format datas
        $dataAuto = new StdClass();
        $dataAuto->tipoAuto         = $data->tipo_auto;
        $dataAuto->codAuto          = $data->auto_id;
        $dataAuto->idMarca          = $data->marca_id;
        $dataAuto->idModelo         = $data->modelo_id;
        $dataAuto->idAno            = $data->ano_id;
        $dataAuto->cor              = $data->cor;
        $dataAuto->valor            = number_format($data->valor, 2, ',', '.');
        $dataAuto->kms              = number_format($data->kms, 0, ',', '.');
        $dataAuto->unicoDono        = $data->unico_dono;
        $dataAuto->aceitaTroca      = $data->aceita_troca;
        $dataAuto->placa            = $data->placa;
        $dataAuto->motor            = $data->motor;
        $dataAuto->tipoCarro        = $data->tipo_carro;
        $dataAuto->destaque         = $data->destaque;
        $dataAuto->colors           = $this->allColors;
        $dataAuto->storeSelected    = $data->store_id;
        $dataAuto->stores           = $this->store->getStores($this->getStoresByUsers());
        $dataAuto->code_auto_fipe   = $data->code_auto_fipe;
        $dataAuto->reference        = $data->reference;
        $dataAuto->observation      = $data->observation;
        $dataAuto->active           = $data->active == 1;
        $dataAuto->fuel             = $data->fuel;
        $dataAuto->dataFuels        = $this->fuel->getAllFuelsActive();
        $dataAuto->controlAutos     = $this->controlAutos->getAllControlsActive();
        $dataAuto->folder_images    = empty($data->folder_images) ? uniqid() : $data->folder_images;

        $dataAuto->brandsFipe       = $this->brandFipe->getAllBrandByAuto($data->tipo_auto);
        $dataAuto->modelsFipe       = $this->modelFipe->getAllModelByAutoAndBrand($data->tipo_auto, $data->marca_id);
        $dataAuto->yearsFipe        = $this->yearFipe->getAllYearByAutoAndBrandAndModel($data->tipo_auto, $data->marca_id, $data->modelo_id);
        $dataAuto->autoFipe         = $this->autoFipe->getAllAutoByAutoAndBrandAndModelAndYear($data->tipo_auto, $data->marca_id, $data->modelo_id, $data->ano_id);

        // Remove os arquivos temporários do automóvel
        foreach (TemporaryFile::where([
            'origin'    => 'autos',
            'ip'        => \Request::ip(),
            'user_id'   => $user->id
        ])->get() as $imageTemp) {
            $pathTemp = "assets/admin/dist/images/autos/temp/$imageTemp->folder/$imageTemp->filename";
            if (File::exists($pathTemp)) {
                File::delete($pathTemp);
            }

            TemporaryFile::where([
                'origin'    => 'autos',
                'folder'    => $imageTemp->folder,
                'filename'  => $imageTemp->filename,
                'ip'        => \Request::ip(),
                'user_id'   => $user->id
            ])->delete();
        }

        return view('admin.automobile.update', compact('dataAuto'));
    }

    private function formatDataUpdateInsertAuto(array $dataForm, bool $isCreate): array
    {
        $user = Auth::user();
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
            'company_id'    => $user->company_id,
            'store_id'      => filter_var($dataForm['stores'], FILTER_VALIDATE_INT),
            'code_auto_fipe'=> filter_var($dataForm['codeFipe'], FILTER_SANITIZE_STRING),
            'reference'     => filter_var($dataForm['reference']),
            'observation'   => filter_var($dataForm['observation']),
            'active'        => isset($dataForm['active']),
            'fuel'          => filter_var($dataForm['fuel'], FILTER_VALIDATE_INT),
            'folder_images' => filter_var($dataForm['path-file-image'], FILTER_SANITIZE_STRING),

            $isCreate ? 'user_created' : 'user_updated'  => $user->id,
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
            $url = asset("assets/admin/dist/images/obs_autos/$fileName");
            $msg = 'Image uploaded successfully';
            $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";

            @header('Content-type: text/html; charset=utf-8');
            return response()->json($response);
        }

        return response()->json($request->file('upload'));
    }

    /**
     * @throws Exception
     */
    public function setUploadImage(Request $request): array
    {
        $folder = $request->input('path');
        $fileName = 'temp.png';

        if ($request->hasFile('filepond')) {
            $file       = $request->file('filepond');
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
                throw new Exception('Imagem em um formato inválido');
            }

            $uploadPath = "assets/admin/dist/images/autos/temp/$folder";

            try {
                if (!File::exists($uploadPath)) {
                    File::makeDirectory($uploadPath);
                }
            } catch (Exception $exception) {
                throw new Exception('Não foi possível criar a pasta para salvar a imagem do automóvel');
            }

            ImageUpload::make($file)
                ->fit(2400, 1800, function ($constraint) {
                    $constraint->upsize();
                })
                ->save("$uploadPath/$fileName");

            TemporaryFile::create([
                'origin'    => 'autos',
                'folder'    => $folder,
                'filename'  => $fileName,
                'action'    => 'create',
                'ip'        => $request->ip(),
                'user_id'   => $request->user()->id
            ]);
        }

        return ['key' => "$folder/$fileName", 'name' => $fileName];
    }

    public function rmUploadImage(Request $request): JsonResponse
    {
        $response       = new stdClass();
        $filePath       = strip_tags(file_get_contents("php://input"));
        $filePath       = json_decode($filePath);

        $uploadPath     = "assets/admin/dist/images/autos";
        $expPathAndFile = explode('/', $filePath->key);
        $pathImage      = $expPathAndFile[0] ?? null;
        $fileImage      = $expPathAndFile[1] ?? null;


        if (isset($filePath->key) && !empty($filePath->key) && count($expPathAndFile) === 2) {
            $response->id       = $filePath;
            $response->success  = true;

            if (File::exists("$uploadPath/temp/$pathImage/$fileImage")) {
                File::delete("$uploadPath/temp/$pathImage/$fileImage");
                TemporaryFile::where([
                    'origin'    => 'autos',
                    'folder'    => $pathImage,
                    'filename'  => $fileImage,
                    'ip'        => $request->ip(),
                    'user_id'   => $request->user()->id
                ])->delete();
            }
            elseif (File::exists("$uploadPath/$pathImage/$fileImage")) {
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
        }
        else {
            $response = false;
        }

        return response()->json($response);
    }

    public function getUploadImage(int $auto): JsonResponse
    {
        $data = $this->automobile->getAutomobileComplete($auto);

        // Loja informada ou usuário não tem permissão
        if (!$data || !isset($data->store_id) || !in_array($data->store_id, $this->getStoresByUsers())) {
            return response()->json([]);
        }

        $images = array();

        foreach ($this->image->getImageByAuto($auto) as $image) {
            $size = File::size("assets/admin/dist/images/autos/$image->folder/$image->arquivo");

            array_push($images, [
                'folder' => $image->folder,
                'file'  => $image->arquivo,
                'size'  => $size,
            ]);
        }

        return response()->json($images);
    }

    public function getQtyStockByBrands(): JsonResponse
    {
        $autos = $this->automobile->getAutosList($this->getStoresByUsers(), array('marca_nome', 'ASC'));
        $arrQts = array();

        foreach ($autos as $auto) {
            if (!$auto['active']) {
                continue;
            }

            if (array_key_exists($auto['marca_nome'], $arrQts)) {
                $arrQts[$auto['marca_nome']] += 1;
            }
            else {
                $arrQts[$auto['marca_nome']] = 1;
            }
        }

        uasort($arrQts, function ($a, $b) {
            return $b - $a;
        });

        return response()->json(array(
            'total' => count($autos),
            'data' => $arrQts
        ));
    }

    public function getQtyStockByAutos(): JsonResponse
    {
        $autos = $this->automobile->getAutosList($this->getStoresByUsers());
        $controlAutos = $this->controlAutos->getAllControlsActive();
        $arrQts = array();

        foreach ($autos as $auto) {
            if (!$auto['active']) {
                continue;
            }

            if (array_key_exists($auto['tipo_auto'], $arrQts)) {
                $arrQts[$auto['tipo_auto']] += 1;
            }
            else {
                $arrQts[$auto['tipo_auto']] = 1;
            }
        }

        foreach ($controlAutos as $controlAuto) {
            if (array_key_exists($controlAuto['code_str'], $arrQts)) {
                $dataValue = array('value' => $arrQts[$controlAuto['code_str']]);
                $dataValue['icon'] = $this->getIconAuto($controlAuto['code_str']);

                $arrQts[$controlAuto['name']] = $dataValue;
                unset($arrQts[$controlAuto['code_str']]);
            }
        }

        return response()->json($arrQts);
    }

    public function getPriceStockByAutos(): JsonResponse
    {
        $autos = $this->automobile->getAutosList($this->getStoresByUsers());
        $controlAutos = $this->controlAutos->getAllControlsActive();
        $arrQts = array();

        foreach ($autos as $auto) {
            if (!$auto['active']) {
                continue;
            }

            if (array_key_exists($auto['tipo_auto'], $arrQts)) {
                $arrQts[$auto['tipo_auto']] += $auto['valor'];
            }
            else {
                $arrQts[$auto['tipo_auto']] = $auto['valor'];
            }
        }

        foreach ($controlAutos as $controlAuto) {
            if (array_key_exists($controlAuto['code_str'], $arrQts)) {
                $dataValue = array('value' => 'R$ '.number_format($arrQts[$controlAuto['code_str']], 2 , ',', '.'));
                $dataValue['icon'] = $this->getIconAuto($controlAuto['code_str']);

                $arrQts[$controlAuto['name']] = $dataValue;
                unset($arrQts[$controlAuto['code_str']]);
            }
        }

        return response()->json($arrQts);
    }

    public function fetchAutoData(Request $request): JsonResponse
    {
        //DB::enableQueryLog();

        $orderBy    = array();
        $result     = array();

        $ini    = $request->input('start');
        $draw   = $request->input('draw');
        $length = $request->input('length');
        $search = $request->input('search');

        // Filtro do front
        $reference  = $request->input('filter_ref')     === ''  ? null : $request->input('filter_ref');
        $license    = $request->input('filter_license') === ''  ? null : $request->input('filter_license');
        $active     = $request->input('filter_active')  === ''  ? null : $request->input('filter_active');
        $feature    = $request->input('filter_feature') === ''  ? null : $request->input('filter_feature');
        $brand      = $request->input('filter_brand');

        $filters = [
            'value'     => null,
            'store_id'  => $this->getStoresByUsers(),
            'reference' => $reference,
            'license'   => $license,
            'active'    => $active,
            'feature'   => $feature,
            'brand'     => $brand,
            'price'     => $request->input('filter_price')
        ];

        if ($search['value']) {
            $filters['value'] = $search['value'];
        }

        if (isset($request->order)) {
            if ($request->order[0]['dir'] == "asc") {
                $direction = "asc";
            }
            else {
                $direction = "desc";
            }

            $fieldsOrder = array('automobiles.id','fipe_autos.brand_name','colors_auto.nome','automobiles.valor');

            if (count($filters['store_id']) > 1) {
                array_push($fieldsOrder, 'automobiles.store_id');
            }
            array_push($fieldsOrder, 'automobiles.id');

            $fieldOrder =  $fieldsOrder[$request->order[0]['column']];
            if ($fieldOrder != "") {
                $orderBy['field'] = $fieldOrder;
                $orderBy['order'] = $direction;
            }
        }

        $data = $this->automobile->getAutosFetch($filters, $ini, $length, $orderBy);

        foreach ($data as $key => $value) {

            $img = $value->arquivo ? "assets/admin/dist/images/autos/$value->folder/thumbnail_$value->arquivo" : "assets/admin/dist/images/autos/no_image.png";

            $badge          = $value['active'] ? "success" : "danger";
            $statusActive   = $value['active'] ? "Ativo" : "Inativo";
            $nameAuto       = "<span class='badge badge-pill badge-lg badge-$badge'>$statusActive</span>";
            $nameAuto       .= $value['destaque'] ? ' <b class="text-yellow"><i class="fa fa-star"></i> DESTAQUE </b><br/>' : '<br/>';
            $nameAuto       .= "{$value['marca_nome']}<br/>{$value['modelo_nome']}";

            $responseAuto = array(
                '<img height="60" src="'.asset($img).'">',//json_encode(DB::getQueryLog()),
                $nameAuto,
                "{$value['color_name']} <br/> {$value['ano_nome']}",
                'R$ ' . number_format($value['valor'], 2, ',', '.') . "<br/> " . number_format($value['kms'], 0, '', '.')." km"
            );

            if (count($filters['store_id']) > 1) {
                array_push($responseAuto, $value['store_name']);
            }

            array_push($responseAuto, '<a class="btn btn-primary btn-flat btn-sm" href="'.route('admin.automobiles.edit', ['codAuto' => $value['auto_id']]).'"><i class="fa fa-edit"></i></button>');

            $result[$key] = $responseAuto;
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $this->automobile->getAutosFetch($filters, null, null, array(), false, true),
            "recordsFiltered" => $this->automobile->getAutosFetch($filters, null, null, array(), true, true),
            "data" => $result
        );

        return response()->json($output);
    }

    public function getIconAuto(string $code): string
    {
        $icon = '';
        switch ($code) {
            case 'carros':
                $icon = 'fa fa-car';
                break;
            case 'motos':
                $icon = 'fa fa-motorcycle';
                break;
            case 'caminhoes':
                $icon = 'fa fa-truck';
                break;
        }

        return $icon;
    }
}
