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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Store;

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
        ControlAutos $controlAutos
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

        $this->allColors = $this->corAuto->getAllColors();
    }

    public function index()
    {
        $dataAutos = [];
        $storesUser = $this->getStoresByUsers();

        $automoveis = $this->automovel->getAutosList($storesUser);

        foreach($automoveis as $automovel){
            $queryImage = $this->image->where([['auto_id', $automovel->id],['primaria', 1]])->get();
            $pathImage = count($queryImage) === 0 ? "assets/admin/dist/images/autos/no_image.png" : "assets/admin/dist/images/autos/{$automovel->tipo_auto}/{$automovel->id}/thumbnail_{$queryImage[0]->arquivo}";
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
        if (!isset($request->stores) || !in_array($request->stores, $this->getStoresByUsers()))
            return redirect()
                ->route('admin.automoveis.cadastro')
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Não foi possível identificar a loja informada!');

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
            if (!$this->autoImagensController->insert($request, $dataForm, $codAutomovel)) {
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
        if (!isset($request->stores) || !in_array($request->stores, $this->getStoresByUsers()))
            return redirect()
                ->route('admin.automoveis.edit', ['codAuto' => $codAutomovel])
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Não foi possível identificar a loja informada!');

        DB::beginTransaction();// Iniciando transação manual para evitar updates não desejáveis

        $updateAutomovel        = $this->automovel->edit($this->formatDataUpdateInsertAuto($dataForm, false), $codAutomovel); // Atualiza dados do automovel
        $updateEstadoFinanceiro = $this->estadoFinanceiro->edit($this->autoFinancialStatusController->getDataFormatToInsert($dataForm, $codAutomovel)); // Atualiza estado financeiro do automóvel
        $updateComplementares   = $this->complementarAuto->edit($this->complementarController->getDataFormatToInsert($dataForm, $codAutomovel)); // Atualiza complementar automóvel
        $updateOpcionais        = $this->opcional->edit($this->autoOpcionalController->getDataFormatToInsert($dataForm, $codAutomovel)); // Atualiza dados dos opcionais do carro

        if($updateAutomovel && $updateEstadoFinanceiro && $updateComplementares && $updateOpcionais) {

            // atualiza imagens do automóvel
            if (!$this->autoImagensController->edit($request, $dataForm)) {
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

        if (!count($data))
            return redirect()->route('admin.automoveis.listagem');

        // format images
        $imagens = [];
        $primaryKey = 1;
        foreach ($data as $imagem){
            if($imagem->primaria == 1) $primaryKey = $imagem->image_id;
            array_push($imagens, (object) ['url' => $imagem->arquivo, 'primary' => $imagem->primaria, 'cod' => $imagem->image_id]);
        }
        if(count($data) === 1 && $data[0]->auto_id === null) $imagens = [];

        // format datas
        $dataAuto = new \StdClass();
        $dataAuto->tipoAuto     = $data[0]->tipo_auto;
        $dataAuto->codAuto      = $data[0]->auto_id;
        $dataAuto->idMarca      = $data[0]->marca_id;
        $dataAuto->idModelo     = $data[0]->modelo_id;
        $dataAuto->idAno        = $data[0]->ano_id;
        $dataAuto->cor          = $data[0]->cor;
        $dataAuto->valor        = number_format($data[0]->valor, 2, ',', '.');
        $dataAuto->kms          = number_format($data[0]->kms, 0, ',', '.');
        $dataAuto->unicoDono    = $data[0]->unico_dono;
        $dataAuto->aceitaTroca  = $data[0]->aceita_troca;
        $dataAuto->placa        = $data[0]->placa;
        $dataAuto->cambio       = $data[0]->cambio;
        $dataAuto->direcao      = $data[0]->direcao;
        $dataAuto->motor        = $data[0]->motor;
        $dataAuto->tipoCarro    = $data[0]->tipo_carro;
        $dataAuto->portas       = $data[0]->qtd_portas;
        $dataAuto->destaque     = $data[0]->destaque;
        $dataAuto->imagens      = $imagens;
        $dataAuto->primaryKey   = $primaryKey;
        $dataAuto->colors       = $this->allColors;
        $dataAuto->storeSelected= $data[0]->store_id;
        $dataAuto->stores       = $this->store->getStores($this->getStoresByUsers());
        $dataAuto->code_auto_fipe= $data[0]->code_auto_fipe;
        $dataAuto->reference    = $data[0]->reference;
        $dataAuto->observation  = $data[0]->observation;
        $dataAuto->active       = $data[0]->active == 1;
        $dataAuto->fuel         = $data[0]->fuel;
        $dataAuto->dataFuels    = $this->fuel->getAllFuelsActive();
        $dataAuto->controlAutos = $this->controlAutos->getAllControlsActive();

        return view('admin.cadastros.automoveis.alterar', compact('dataAuto'));
    }

    public function delete(): string
    {
        $delete = $this->automovel
            ->where('id', 1)
            ->delete();

        if($delete) return 'Excluido com sucesso';

        return 'Falha ao excluir';
    }

    private function formatDataUpdateInsertAuto(array $dataForm, bool $isCreate): array
    {
        return array(
            'tipo_auto'     => filter_var($dataForm['autos'], FILTER_SANITIZE_STRING),
            'valor'         => filter_var(str_replace(',' , '.', str_replace('.', '', $dataForm['valor'])), FILTER_VALIDATE_FLOAT),
            'cor'           => filter_var($dataForm['cor'], FILTER_SANITIZE_STRING),
            'unico_dono'    => filter_var($dataForm['unicoDono'], FILTER_VALIDATE_INT),
            'aceita_troca'  => filter_var($dataForm['aceitaTroca'], FILTER_VALIDATE_INT),
            'placa'         => filter_var($dataForm['placa'], FILTER_SANITIZE_STRING),
            'final_placa'   => (int)substr($dataForm['placa'], -1),
            'kms'           => filter_var(str_replace('.' , '', $dataForm['quilometragem']), FILTER_VALIDATE_INT),
            'destaque'      => filter_var($dataForm['destaque'], FILTER_VALIDATE_BOOLEAN),
            'company_id'    => Auth::user()->company_id,
            'store_id'      => filter_var($dataForm['stores'], FILTER_VALIDATE_INT),
            'code_auto_fipe'=> filter_var($dataForm['codeFipe'], FILTER_SANITIZE_STRING),
            $isCreate ? 'user_created' : 'user_updated'  => Auth::user()->id,
            'reference'     => filter_var($dataForm['reference']),
            'observation'   => filter_var($dataForm['observation']),
            'active'        => isset($dataForm['active']),
            'fuel'          => filter_var($dataForm['fuel'], FILTER_VALIDATE_INT),
        );
    }

    public function uploadImagesObsAuto(Request $request)
    {
        if($request->hasFile('upload')) {
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = md5(uniqid(rand(), true)) . ".$extension";

            $request->file('upload')->move(public_path('assets/admin/dist/images/obs_autos'), $fileName);

            $CKEditorFuncNum = $request->input('CKEditorFuncNum');
            $url = asset('assets/admin/dist/images/obs_autos/'.$fileName);
            $msg = 'Image uploaded successfully';
            $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";

            @header('Content-type: text/html; charset=utf-8');
            echo $response;
        } else echo json_encode($request->file('upload'));
    }
}
