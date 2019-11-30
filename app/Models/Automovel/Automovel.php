<?php

namespace App\Models\Automovel;

use Illuminate\Database\Eloquent\Model;
use DB;

class Automovel extends Model
{
    protected $table = 'automoveis';
    protected $fillable = [
        'NTIPOAUTO',
        'NCODMARCA',
        'CNOMEMARCA',
        'NCODMODELO',
        'CNOMEMODELO',
        'NANO',
        'CNOMEANO',
        'NVALOR',
        'CCOR',
        'NUNICODONO',
        'NACEITATROCA',
        'NPLACA',
        'NFINALPLACA',
        'NKMS',
        'NCOMBUSTIVEL'
    ];
    protected $guarded = ['NCODAUTO'];

    public function insert($dataForm)
    {

        // Cria array validado com nomes das colunas da tabela 'automoveis'
        $tableDataFormAuto = array(
            'NTIPOAUTO'             => filter_var($dataForm['autos'], FILTER_SANITIZE_STRING),
            'NCODMARCA'             => filter_var($dataForm['marcas'], FILTER_VALIDATE_INT),
            'CNOMEMARCA'            => filter_var($dataForm['marcaTxt'], FILTER_SANITIZE_STRING),
            'NCODMODELO'            => filter_var($dataForm['modelos'], FILTER_VALIDATE_INT),
            'CNOMEMODELO'           => filter_var($dataForm['modeloTxt'], FILTER_SANITIZE_STRING),
            'NANO'                  => filter_var($dataForm['anos'], FILTER_SANITIZE_STRING),
            'CNOMEANO'              => filter_var($dataForm['anoTxt'], FILTER_SANITIZE_NUMBER_INT),
            'NVALOR'                => filter_var(str_replace(',' , '.', str_replace('.', '', $dataForm['valor'])), FILTER_VALIDATE_FLOAT),
            'CCOR'                  => filter_var($dataForm['cor'], FILTER_SANITIZE_STRING),
            'NUNICODONO'            => filter_var($dataForm['unicoDono'], FILTER_VALIDATE_INT),
            'NACEITATROCA'          => filter_var($dataForm['aceitaTroca'], FILTER_VALIDATE_INT),
            'NPLACA'                => filter_var($dataForm['placa'], FILTER_SANITIZE_STRING),
            'NFINALPLACA'           => filter_var($dataForm['finalPlaca'], FILTER_VALIDATE_INT),
            'NKMS'                  => filter_var(str_replace('.' , '', $dataForm['quilometragem']), FILTER_VALIDATE_INT),
            'NCOMBUSTIVEL'          => filter_var($dataForm['combustivel'], FILTER_SANITIZE_STRING)
        );
        // Insere dados na tabela 'automoveis'
        $insertAutomovel = $this->create($tableDataFormAuto);

        return $insertAutomovel;
    }
}
