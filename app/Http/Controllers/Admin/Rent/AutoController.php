<?php

namespace App\Http\Controllers\Admin\Rent;

use App\Http\Controllers\Controller;
use App\Models\Automobile\FuelAuto;
use App\Models\Fipe\ControlAuto;
use App\Models\Fipe\FipeAuto;
use App\Models\Fipe\FipeBrand;
use App\Models\Fipe\FipeModel;
use App\Models\Fipe\FipeYear;
use App\Models\Rent\RentAutomobile;
use App\Models\Rent\RentImageAutomobile;
use App\Models\Store;
use App\Models\TemporaryFile;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image as ImageUpload;
use StdClass;

class AutoController extends Controller
{
    private RentAutomobile $rentAutomobile;
    private RentImageAutomobile $rentImageAutomobile;
    private CharacteristicController $characteristicController;
    private AutoImagesController $autoImagesController;
    private Store $store;
    private FuelAuto $fuel;
    private ControlAuto $controlAutos;
    private FipeBrand $brandFipe;
    private FipeModel $modelFipe;
    private FipeYear $yearFipe;
    private FipeAuto $autoFipe;

    public function __construct(
        RentAutomobile           $rentAutomobile,
        RentImageAutomobile      $rentImageAutomobile,
        CharacteristicController $characteristicController,
        AutoImagesController     $autoImagesController,
        Store                    $store,
        FuelAuto                 $fuel,
        ControlAuto              $controlAutos,
        FipeBrand                $brandFipe,
        FipeModel                $modelFipe,
        FipeYear                 $yearFipe,
        FipeAuto                 $autoFipe
    )
    {
        $this->rentAutomobile           = $rentAutomobile;
        $this->rentImageAutomobile      = $rentImageAutomobile;
        $this->characteristicController = $characteristicController;
        $this->autoImagesController     = $autoImagesController;
        $this->store                    = $store;
        $this->fuel                     = $fuel;
        $this->controlAutos             = $controlAutos;
        $this->brandFipe                = $brandFipe;
        $this->modelFipe                = $modelFipe;
        $this->yearFipe                 = $yearFipe;
        $this->autoFipe                 = $autoFipe;
    }

    public function index()
    {
        $storesUser = $this->getStoresByUsers();

        $filter = array();
        $filter['brand']    = $this->rentAutomobile->getBrandsFilter($storesUser);
        $filter['stores']   = $this->store->getStores($this->getStoresByUsers());

        return view('admin.rent.automobile.index', compact('storesUser', 'filter'));
    }

    public function fetchAutomobile(Request $request): JsonResponse
    {
        $orderBy    = array();
        $result     = array();

        $ini        = $request->input('start');
        $draw       = $request->input('draw');
        $length     = $request->input('length');
        $search     = $request->input('search');
        $store_id   = null;

        // valida se usuário pode ver a loja
        if (!empty($request->input('filter_store')) && !in_array($request->input('filter_store'), $this->getStoresByUsers())) {
            return response()->json(array());
        }

        if (!empty($request->input('filter_store')) && !is_array($request->input('filter_store'))) {
            $store_id = array($request->input('filter_store'));
        }

        if ($request->input('filter_store') === null) {
            $store_id = $this->getStoresByUsers();
        }

        // Filtro do front
        $reference  = $request->input('filter_ref')     === ''  ? null : $request->input('filter_ref');
        $license    = $request->input('filter_license') === ''  ? null : $request->input('filter_license');
        $active     = $request->input('filter_active')  === ''  ? null : $request->input('filter_active');
        $feature    = $request->input('filter_feature') === ''  ? null : $request->input('filter_feature');
        $brand      = $request->input('filter_brand');

        $filters = [
            'value'     => null,
            'store_id'  => $store_id,
            'reference' => $reference,
            'license'   => $license,
            'active'    => $active,
            'feature'   => $feature,
            'brand'     => $brand
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

            $fieldsOrder = array('rent_automobiles.id','fipe_autos.brand_name','colors_auto.nome','rent_automobiles.kilometers');

            if (count($this->getStoresByUsers()) > 1) {
                $fieldsOrder[] = 'rent_automobiles.store_id';
            }
            $fieldsOrder[] = 'rent_automobiles.id';

            $fieldOrder =  $fieldsOrder[$request->order[0]['column']];
            if ($fieldOrder != "") {
                $orderBy['field'] = $fieldOrder;
                $orderBy['order'] = $direction;
            }
        }

        $data = $this->rentAutomobile->getAutosFetch($filters, $ini, $length, $orderBy);

        foreach ($data as $key => $value) {

            $img = $value->file ? "assets/admin/dist/images/rent/autos/$value->folder/thumbnail_$value->file" : "assets/admin/dist/images/rent/autos/no_image.png";

            $badge          = $value['active'] ? "success" : "danger";
            $statusActive   = $value['active'] ? "Ativo" : "Inativo";
            $nameAuto       = "<span class='badge badge-pill badge-lg badge-$badge'>$statusActive</span>";
            $nameAuto       .= $value['featured'] ? ' <b class="text-yellow"><i class="fa fa-star"></i> DESTAQUE </b><br/>' : '<br/>';
            $nameAuto       .= "{$value['marca_nome']}<br/>{$value['modelo_nome']}";

            $responseAuto = array(
                '<img height="60" src="'.asset($img).'">',
                $nameAuto,
                "{$value['color_name']} <br/> {$value['ano_nome']}",
                number_format($value['kilometers'], 0, '', '.')." km"
            );

            $button = '<a class="btn btn-primary btn-flat btn-sm" href="'.route('admin.rent.automobile.edit', ['id' => $value['auto_id']]).'" data-toggle="tooltip" title="Atualizar Cadastro"><i class="fa fa-edit"></i></a>';

            if (count($this->getStoresByUsers()) > 1) {
                $responseAuto[] = $value['store_name'];
            }

            $responseAuto[] = $button;

            $result[$key] = $responseAuto;
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $this->rentAutomobile->getAutosFetch($filters, null, null, array(), false, true),
            "recordsFiltered" => $this->rentAutomobile->getAutosFetch($filters, null, null, array(), true, true),
            "data" => $result
        );

        return response()->json($output);
    }

    public function new()
    {
        $dataAuto = new StdClass();
        $dataAuto->stores       = $this->store->getStores($this->getStoresByUsers());
        $dataAuto->dataFuels    = $this->fuel->getAllFuelsActive();
        $dataAuto->controlAutos = $this->controlAutos->getAllControlsActive();

        return view('admin.rent.automobile.create', compact('dataAuto'));
    }

    public function insert(Request $request): RedirectResponse
    {
        $dataForm = $request->all(); // Dado recuperado via POST

        // Loja informada ou usuário não tem permissão
        if (!$request->has('stores') || !in_array($request->input('stores'), $this->getStoresByUsers())) {
            return redirect()
                ->route('admin.rent.automobile.new')
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Não foi possível identificar a loja informada!');
        }

        DB::beginTransaction();// Iniciando transação manual para evitar insert não desejáveis

        // Cria array validado com nomes das colunas da tabela 'automobiles.'
        // Insere dados do automóvel
        try {
            $insertAutomobiles      = $this->rentAutomobile->insert($this->formatDataUpdateInsertAuto($dataForm, true));
            $autoId                 = $insertAutomobiles->id; // Recupera código inserido no banco

            $this->characteristicController->insert($autoId, $dataForm['characteristic']);
        } catch (Exception $exception) {
            DB::rollBack();
            return redirect()
                ->route('admin.rent.automobile.new')
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', $exception->getMessage());
        }

        if ($insertAutomobiles) {
            // Insere imagens do automóvel
            if (!$this->autoImagesController->insert($dataForm, $autoId)) {
                DB::rollBack();
                return redirect()
                    ->route('admin.rent.automobile.new')
                    ->withInput()
                    ->with('typeMessage', 'error')
                    ->with('message', 'Ocorreu um problema para salvar as imagens do automóvel, reveja os dados e tente novamente!');
            }

            DB::commit();
            return redirect()
                ->route('admin.rent.automobile.index')
                ->with('typeMessage', 'success')
                ->with('message', 'Automóvel cadastrado com sucesso!');
        }
        else {
            DB::rollBack();
            return redirect()
                ->route('admin.rent.automobile.new')
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Ocorreu um problema para realizar o cadastro do automóvel, reveja os dados e tente novamente!');
        }
    }

    public function edit(int $codAuto)
    {
        $data = $this->rentAutomobile->getAutomobileComplete($codAuto);

        if (!$data) {
            return redirect()->route('admin.rent.automobile.index');
        }

        // Loja informada ou usuário não tem permissão.
        if (!in_array($data->store_id, $this->getStoresByUsers())) {
            return redirect()
                ->route('admin.automobiles.index');
        }

        $user = Auth::user();

        // format datas
        $dataAuto = new StdClass();
        $dataAuto->auto             = $data;
        $dataAuto->stores           = $this->store->getStores($this->getStoresByUsers());
        $dataAuto->dataFuels        = $this->fuel->getAllFuelsActive();
        $dataAuto->controlAutos     = $this->controlAutos->getAllControlsActive();
        $dataAuto->folder_images    = empty($data->folder_images) ? uniqid() : $data->folder_images;

        $dataAuto->brandsFipe       = $this->brandFipe->getAllBrandByAuto($data->tipo_auto);
        $dataAuto->modelsFipe       = $this->modelFipe->getAllModelByAutoAndBrand($data->tipo_auto, $data->marca_id);
        $dataAuto->yearsFipe        = $this->yearFipe->getAllYearByAutoAndBrandAndModel($data->tipo_auto, $data->marca_id, $data->modelo_id);
        $dataAuto->autoFipe         = $this->autoFipe->getAllAutoByAutoAndBrandAndModelAndYear($data->tipo_auto, $data->marca_id, $data->modelo_id, $data->ano_id);

        // Remove os arquivos temporários do automóvel.
        foreach (TemporaryFile::where([
            'origin'    => 'rent_autos',
            'ip'        => \Request::ip(),
            'user_id'   => $user->id
        ])->get() as $imageTemp) {
            $pathTemp = "assets/admin/dist/images/rent/autos/temp/$imageTemp->folder/$imageTemp->filename";
            if (File::exists($pathTemp)) {
                File::delete($pathTemp);
            }

            TemporaryFile::where([
                'origin'    => 'rent_autos',
                'folder'    => $imageTemp->folder,
                'filename'  => $imageTemp->filename,
                'ip'        => \Request::ip(),
                'user_id'   => $user->id
            ])->delete();
        }

        return view('admin.rent.automobile.update', compact('dataAuto'));
    }

    public function update(Request $request): RedirectResponse
    {
        $dataForm = $request->all(); // Dados recuperado via POST
        $autoId = $dataForm['idAuto']; // Código do automóvel

        // Loja informada ou usuário não tem permissão
        if (!$request->has('stores') || !in_array($request->input('stores'), $this->getStoresByUsers())) {
            return redirect()
                ->route('admin.rent.automobile.edit', ['codAuto' => $autoId])
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Não foi possível identificar a loja informada!');
        }

        DB::beginTransaction();// Iniciando transação manual para evitar updates não desejáveis

        try {
            $updateAutomobiles = $this->rentAutomobile->edit($this->formatDataUpdateInsertAuto($dataForm, false), $autoId);

            $this->characteristicController->update($autoId, $dataForm['characteristic']);
        } catch (Exception $exception) {
            DB::rollBack();
            return redirect()
                ->route('admin.rent.automobile.edit', ['id' => $autoId])
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', $exception->getMessage());
        }

        if ($updateAutomobiles) {
            // atualiza imagens do automóvel
            if (!$this->autoImagesController->edit($dataForm)) {
                DB::rollBack();
                return redirect()
                    ->route('admin.rent.automobile.edit', ['id' => $autoId])
                    ->withInput()
                    ->with('typeMessage', 'error')
                    ->with('message', 'Ocorreu um problema para realizar a atualização das imagens do automóvel, reveja os dados e tente novamente!');
            }

            DB::commit();
            return redirect()
                ->route('admin.rent.automobile.index')
                ->with('typeMessage', 'success')
                ->with('message', 'Automóvel alterado com sucesso!');
        }
        else {
            DB::rollBack();
            return redirect()
                ->route('admin.automobiles.edit', ['id' => $autoId])
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Ocorreu um problema para realizar a alteração do automóvel, reveja os dados e tente novamente!');
        }
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

            $request->file('upload')->move(public_path('assets/admin/dist/images/obs_rent_autos'), $fileName);

            $CKEditorFuncNum = $request->input('CKEditorFuncNum');
            $url = asset("assets/admin/dist/images/obs_rent_autos/$fileName");
            $msg = 'Image uploaded successfully';
            $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";

            @header('Content-type: text/html; charset=utf-8');
            return response()->json($response);
        }

        return response()->json($request->file('upload'));
    }

    public function getUploadImage(int $auto): JsonResponse
    {
        $data = $this->rentAutomobile->getAutomobileComplete($auto);

        // Loja informada ou usuário não tem permissão.
        if (!$data || !isset($data->store_id) || !in_array($data->store_id, $this->getStoresByUsers())) {
            return response()->json([]);
        }

        $images = array();

        foreach ($this->rentImageAutomobile->getImageByAuto($auto) as $image) {
            $size = File::size("assets/admin/dist/images/rent/autos/$image->folder/$image->file");

            $images[] = [
                'folder'    => $image->folder,
                'file'      => $image->file,
                'size'      => $size,
            ];
        }

        return response()->json($images);
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

            $uploadPath = "assets/admin/dist/images/rent/autos/temp/$folder";

            try {
                makePathDir($uploadPath);
            } catch (Exception $exception) {
                throw new Exception('Não foi possível criar a pasta para salvar a imagem do automóvel. ' . $exception->getMessage());
            }

            ImageUpload::make($file)
                ->fit(2400, 1800, function ($constraint) {
                    $constraint->upsize();
                })
                ->save("$uploadPath/$fileName");

            TemporaryFile::create([
                'origin'    => 'rent_autos',
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

        $uploadPath     = "assets/admin/dist/images/rent/autos";
        $expPathAndFile = explode('/', $filePath->key);
        $pathImage      = $expPathAndFile[0] ?? null;
        $fileImage      = $expPathAndFile[1] ?? null;

        if (isset($filePath->key) && !empty($filePath->key) && count($expPathAndFile) === 2) {
            $response->id       = $filePath;
            $response->success  = true;

            if (File::exists("$uploadPath/temp/$pathImage/$fileImage")) {
                File::delete("$uploadPath/temp/$pathImage/$fileImage");
                TemporaryFile::where([
                    'origin'    => 'rent_autos',
                    'folder'    => $pathImage,
                    'filename'  => $fileImage,
                    'ip'        => $request->ip(),
                    'user_id'   => $request->user()->id
                ])->delete();
            }
            elseif (File::exists("$uploadPath/$pathImage/$fileImage")) {
                TemporaryFile::create([
                    'origin'    => 'rent_autos',
                    'folder'    => $pathImage,
                    'filename'  => $fileImage,
                    'action'    => 'delete',
                    'ip'        => $request->ip(),
                    'user_id'   => $request->user()->id
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

    private function formatDataUpdateInsertAuto(array $dataForm, bool $isCreate): array
    {
        return array(
            'tipo_auto'     => filter_var($dataForm['autos'], FILTER_SANITIZE_STRING),
            'color'         => filter_var($dataForm['color'], FILTER_SANITIZE_STRING),
            'license'       => filter_var($dataForm['placa'], FILTER_SANITIZE_STRING),
            'kilometers'    => filter_var(str_replace('.' , '', $dataForm['quilometragem']), FILTER_VALIDATE_INT),
            'featured'      => isset($dataForm['destaque']),
            'company_id'    => $this->store->getCompanyByStore(filter_var($dataForm['stores'], FILTER_VALIDATE_INT)),
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
}
