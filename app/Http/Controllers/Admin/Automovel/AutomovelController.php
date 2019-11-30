<?php

namespace App\Http\Controllers\Admin\Automovel;

use App\Http\Requests\AutomovelFormRequest;
use App\Http\Controllers\Controller;
use App\Models\Automovel\Automovel;
use App\Models\Automovel\Image;
use App\Models\Automovel\Carro;
use App\Models\Automovel\OpcionalCarro;
use App\Models\Automovel\EstadoFinanceiro;
use DB;

class AutomovelController extends Controller
{

    private $automovel;
    private $image;
    private $carro;
    private $opcionalCarro;
    private $estadoFinanceiro;

    public function __construct(Automovel $automovel, Image $image, Carro $carro, OpcionalCarro $opcionalCarro, EstadoFinanceiro $estadoFinanceiro)
    {
        $this->automovel        = $automovel;
        $this->image            = $image;
        $this->carro            = $carro;
        $this->opcionalCarro    = $opcionalCarro;
        $this->estadoFinanceiro = $estadoFinanceiro;
    }


    public function index()
    {
        $dataAutos = [];

        $automoveis = $this->automovel
                            ->join('imagensauto', 'automoveis.NCODAUTO', '=', 'imagensauto.NCODAUTO')
                            ->where('imagensauto.primary', 1)
                            ->get();

        foreach($automoveis as $automovel){
            $data = Array(
                'codauto'   => $automovel->NCODAUTO,
                'path'      => "../../admin/dist/images/autos/{$automovel->NTIPOAUTO}/{$automovel->NCODAUTO}/thumbnail_{$automovel->PATH}",
                'marca'     => $automovel->CNOMEMARCA,
                'modelo'    => $automovel->CNOMEMODELO,
                'ano'       => $automovel->CNOMEANO,
                'cor'       => ucfirst($automovel->CCOR),
                'valor'     => 'R$ ' . number_format($automovel->NVALOR, 2, ',', '.'),
                'kms'       => number_format($automovel->NKMS, 0, ',', '.') . ' kms'
            );

            array_push($dataAutos, $data);
        }

        return view('auth.cadastros.automoveis.listagem', compact('dataAutos'));
    }

    public function cadastro()
    {
        return view('auth.cadastros.automoveis.cadastro');
    }

    public function store(AutomovelFormRequest $request)
    {
        $dataForm = $request->all(); // Dados recuperado via POST

        DB::beginTransaction();// Iniciando transação manual para evitar insert não desejáveis

        $insertAutomovel        = $this->automovel->insert($dataForm); // Insere dados do automovel
        $codAutomovel           = $insertAutomovel->id; // Recupera código inserido no banco
        $insertImage            = $this->image->insert($request, $dataForm, $codAutomovel); // Insere imagens do automóvel
        $insertEstadoFinanceiro = $this->estadoFinanceiro->insert($dataForm, $codAutomovel); // Insere estado financeiro do automóvel

        if($dataForm['autos'] === "carros") { // Verifica se o automóvel a ser cadastrado é um carro
            $insertCarro = $this->carro->insert($dataForm, $codAutomovel); // Insere dados de carro
            $codCarro = $insertCarro->id; // Recupera código inserido no banco
            $insertOpcionais = $this->opcionalCarro->insert($dataForm, $codCarro); // Insere dados dos opcionais do carro
        }

        if($insertAutomovel && $insertImage && $insertCarro && $insertOpcionais && $insertEstadoFinanceiro) {
            DB::commit();
            return redirect()
                ->route('admin.automoveis.listagem')
                ->with('typeMessage', 'success')
                ->with('message', 'Automóvel cadastrado com sucesso!');
        }
        else{
            DB::rollBack();
            return redirect()
                ->route('admin.automoveis.cadastro')
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Ocorreu um problema para realizar o cadastro do automóvel, reveja os dados e tente novamente!');
        }
    }

    public function update()
    {
        $update = $this->automovel
                        ->where('NCODAUTO', 1)
                        ->update([
                                    'CNOMEMODELO' => 'Carro update 2'
                                ]);


        if($update) return 'Alterado com sucesso';
        if(!$update) return 'Falha ao alterar';
    }

    public function delete()
    {

        $delete = $this->automovel
                        ->where('NCODAUTO', 1)
                        ->delete();

        if($delete) return 'Excluido com sucesso';
        if(!$delete) return 'Falha ao excluir';
    }

    public function edit($codAuto)
    {
        return
            $this->automovel
                ->join('imagensauto', 'automoveis.NCODAUTO', '=', 'imagensauto.NCODAUTO')
                ->join('carros', 'automoveis.NCODAUTO', '=', 'carros.NCODAUTO')
                ->join('estadofinanceiro', 'automoveis.NCODAUTO', '=', 'estadofinanceiro.NCODAUTO')
                ->join('opcionalcarro', 'carros.NCODCARRO', '=', 'opcionalcarro.NCODCARRO')
                ->where('automoveis.NCODAUTO', $codAuto)
                ->get();
    }
}
