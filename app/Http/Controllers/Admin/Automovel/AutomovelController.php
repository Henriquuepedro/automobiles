<?php

namespace App\Http\Controllers\Admin\Automovel;

use App\Http\Controllers\Admin\ComplementarController;
use App\Http\Requests\AutomovelFormRequest;
use App\Http\Controllers\Controller;
use App\Models\Automovel\Automovel;
use App\Models\Automovel\ComplementarAuto;
use App\Models\Automovel\CorAuto;
use App\Models\Automovel\Image;
use App\Models\Automovel\Carro;
use App\Models\Automovel\Opcional;
use App\Models\Automovel\EstadoFinanceiro;
use App\Models\EstadosFinanceiro;
use Illuminate\Support\Facades\DB;

class AutomovelController extends Controller
{

    private $automovel;
    private $image;
    private $opcional;
    private $estadoFinanceiro;
    private $estadosFinanceiro;
    private $autoImagensController;
    private $autoOpcionalController;
    private $autoFinancialStatusController;
    private $complementarAuto;
    private $complementarController;
    private $corAuto;
    private $allColors;

    public function __construct(
        Automovel $automovel,
        Image $image,
        Carro $carro,
        Opcional $opcional,
        EstadoFinanceiro $estadoFinanceiro,
        EstadosFinanceiro $estadosFinanceiro,
        AutoImagensController $autoImagensController,
        AutoOpcionalController $autoOpcionalController,
        AutoFinancialStatusController $autoFinancialStatusController,
        ComplementarAuto $complementarAuto,
        ComplementarController $complementarController,
        CorAuto $corAuto
    )
    {
        $this->automovel         = $automovel;
        $this->image             = $image;
        $this->carro             = $carro;
        $this->opcional     = $opcional;
        $this->estadoFinanceiro  = $estadoFinanceiro;
        $this->estadosFinanceiro = $estadosFinanceiro;
        $this->autoImagensController = $autoImagensController;
        $this->autoOpcionalController = $autoOpcionalController;
        $this->autoFinancialStatusController = $autoFinancialStatusController;
        $this->complementarAuto = $complementarAuto;
        $this->complementarController = $complementarController;
        $this->corAuto = $corAuto;

        $this->allColors = $this->corAuto->getAllColors();
    }


    public function index()
    {
        $dataAutos = [];

        $automoveis = $this->automovel->orderBy('id')->get();

        foreach($automoveis as $automovel){
            $queryImage = $this->image->where([['auto_id', $automovel->id],['primaria', 1]])->get();
            $pathImage = count($queryImage) === 0 ? "admin/dist/images/autos/no_image.png" : "admin/dist/images/autos/{$automovel->tipo_auto}/{$automovel->id}/thumbnail_{$queryImage[0]->arquivo}";
            $data = Array(
                'codauto'   => $automovel->id,
                'path'      => $pathImage,
                'marca'     => $automovel->marca_nome,
                'modelo'    => $automovel->modelo_nome,
                'ano'       => $automovel->ano_nome,
                'cor'       => ucfirst($automovel->cor),
                'valor'     => 'R$ ' . number_format($automovel->valor, 2, ',', '.'),
                'kms'       => number_format($automovel->kms, 0, ',', '.') . ' kms',
                'destaque'  => $automovel->destaque == 1
            );

            array_push($dataAutos, $data);
        }

        return view('auth.cadastros.automoveis.listagem', compact('dataAutos'));
    }

    public function cadastro()
    {
        $dataAuto = new \StdClass();
        $dataAuto->colors       = $this->allColors;
        $dataAuto->financials   = $this->estadosFinanceiro->getFinancialsStatus();

        return view('auth.cadastros.automoveis.cadastro', compact('dataAuto'));
    }

    public function store(AutomovelFormRequest $request)
    {
        $dataForm = $request->all(); // Dados recuperado via POST

        DB::beginTransaction();// Iniciando transação manual para evitar insert não desejáveis

        // Cria array validado com nomes das colunas da tabela 'automoveis'
        // Insere dados do automovel
        $insertAutomovel = $this->automovel->insert($this->formatDataUpdateInsertAuto($dataForm));

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

    public function update(AutomovelFormRequest $request)
    {
        $dataForm = $request->all(); // Dados recuperado via POST
        $codAutomovel = $dataForm['idAuto']; // Código do automóvel

        DB::beginTransaction();// Iniciando transação manual para evitar updates não desejáveis

        $updateAutomovel        = $this->automovel->edit($this->formatDataUpdateInsertAuto($dataForm), $codAutomovel); // Atualiza dados do automovel
        $updateEstadoFinanceiro = $this->estadoFinanceiro->edit($this->autoFinancialStatusController->getDataFormatToInsert($dataForm, $codAutomovel)); // Atualiza estado financeiro do automóvel
        $updateComplementares   = $this->complementarAuto->edit($this->complementarController->getDataFormatToInsert($dataForm, $codAutomovel)); // Atualiza complementar automóvel
        $updateOpcionais        = $this->opcional->edit($this->autoOpcionalController->getDataFormatToInsert($dataForm, $codAutomovel)); // Atualiza dados dos opcionais do carro

        if($updateAutomovel && $updateEstadoFinanceiro && $updateComplementares && $updateOpcionais) {

            // atualiza imagens do automóvel
            if (!$this->autoImagensController->edit($request, $dataForm)) {
                DB::rollBack();
                return redirect()
                    ->route('admin.automoveis.edit', ['codAuto', $codAutomovel])
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
                ->route('admin.automoveis.edit', ['codAuto', $codAutomovel])
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Ocorreu um problema para realizar a alteração do automóvel, reveja os dados e tente novamente!');
        }
    }

    public function edit($codAuto)
    {
        $data = $this->automovel->getAutomovelComplete($codAuto);

        // format images
        $imagens = [];
        $primaryKey = 1;
        foreach ($data as $imagem){
            if($imagem->primaria == 1) $primaryKey = $imagem->image_id;
            array_push($imagens, (object) ['url' => $imagem->arquivo, 'primary' => $imagem->primaria, 'cod' => $imagem->image_id]);
        }
        if(count($data) === 1 && $data[0]->id === null) $imagens = [];

        // format financials
        $financialsStatus = $this->estadosFinanceiro->getFinancialsStatus();
        $financialsStatusAuto = (array)json_decode($this->estadoFinanceiro->getOptionalByAuto($codAuto)->valores ?? '{}');
        $arrFinancialStatus = array();

        foreach ($financialsStatus as $financialStatus) {
            array_push($arrFinancialStatus, array(
                'id'        => $financialStatus->id,
                'nome'      => $financialStatus->nome,
                'checked'   => in_array($financialStatus->id, $financialsStatusAuto),
            ));
        }

        // format datas
        $dataAuto = new \StdClass();
        $dataAuto->tipoAuto     = $data[0]->tipo_auto;
        $dataAuto->codAuto      = $data[0]->auto_id;
        $dataAuto->nomeMarca    = $data[0]->marca_nome;
        $dataAuto->nomeModelo   = $data[0]->modelo_nome;
        $dataAuto->nomeAno      = $data[0]->ano_nome;
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
        $dataAuto->financials   = $arrFinancialStatus;
        $dataAuto->colors       = $this->allColors;

        return view('auth.cadastros.automoveis.alterar', compact('dataAuto'));
    }

    public function delete()
    {

        $delete = $this->automovel
            ->where('id', 1)
            ->delete();

        if($delete) return 'Excluido com sucesso';
        if(!$delete) return 'Falha ao excluir';
    }

    private function formatDataUpdateInsertAuto($dataForm)
    {
        return array(
            'tipo_auto'     => filter_var($dataForm['autos'], FILTER_SANITIZE_STRING),
            'marca_id'      => filter_var($dataForm['marcas'], FILTER_VALIDATE_INT),
            'marca_nome'    => filter_var($dataForm['marcaTxt'], FILTER_SANITIZE_STRING),
            'modelo_id'     => filter_var($dataForm['modelos'], FILTER_VALIDATE_INT),
            'modelo_nome'   => filter_var($dataForm['modeloTxt'], FILTER_SANITIZE_STRING),
            'ano_id'        => filter_var($dataForm['anos'], FILTER_SANITIZE_STRING),
            'ano_nome'      => filter_var($dataForm['anoTxt'], FILTER_SANITIZE_NUMBER_INT),
            'valor'         => filter_var(str_replace(',' , '.', str_replace('.', '', $dataForm['valor'])), FILTER_VALIDATE_FLOAT),
            'cor'           => filter_var($dataForm['cor'], FILTER_SANITIZE_STRING),
            'unico_dono'    => filter_var($dataForm['unicoDono'], FILTER_VALIDATE_INT),
            'aceita_troca'  => filter_var($dataForm['aceitaTroca'], FILTER_VALIDATE_INT),
            'placa'         => filter_var($dataForm['placa'], FILTER_SANITIZE_STRING),
            'final_placa'   => (int)substr($dataForm['placa'], -1),
            'kms'           => filter_var(str_replace('.' , '', $dataForm['quilometragem']), FILTER_VALIDATE_INT),
            'destaque'      => filter_var($dataForm['destaque'], FILTER_VALIDATE_BOOLEAN)
        );
    }
}
