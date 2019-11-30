<?php

namespace App\Models\Automovel;

use Illuminate\Database\Eloquent\Model;

class Carro extends Model
{
    protected $fillable = [
        'NCODAUTO',
        'NCAMBIO',
        'NDIRECAO',
        'NMOTOR',
        'NTIPOCARRO',
        'NPORTAS'
    ];
    protected $guarded = ['NCODCARRO'];

    public function insert($dataForm, $codAutomovel)
    {
        $tableDataFormCarro = array(
            'NCODAUTO'      => $codAutomovel,
            'NCAMBIO'       => filter_var($dataForm['cambio'], FILTER_SANITIZE_STRING),
            'NDIRECAO'      => filter_var($dataForm['direcao'], FILTER_SANITIZE_STRING),
            'NMOTOR'        => filter_var($dataForm['potenciaMotor'], FILTER_SANITIZE_STRING),
            'NTIPOCARRO'    => filter_var($dataForm['tipoVeiculo'], FILTER_SANITIZE_STRING),
            'NPORTAS'       => filter_var($dataForm['portas'], FILTER_VALIDATE_INT)
        );

        return $this->create($tableDataFormCarro);
    }
}
