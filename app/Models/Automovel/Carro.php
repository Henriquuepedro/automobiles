<?php

namespace App\Models\Automovel;

use Illuminate\Database\Eloquent\Model;

class Carro extends Model
{
    protected $fillable = [
        'auto_id',
        'cambio',
        'direcao',
        'motor',
        'tipo_carro',
        'qtd_portas'
    ];
    protected $guarded = [];

    public function insert($dataForm)
    {
        return $this->create($dataForm);
    }

    public function edit($dataForm, $codAutomovel)
    {
        $tableDataFormCarro = array(
            'cambio'        => filter_var($dataForm['cambio'], FILTER_SANITIZE_STRING),
            'direcao'       => filter_var($dataForm['direcao'], FILTER_SANITIZE_STRING),
            'motor'         => filter_var($dataForm['potenciaMotor'], FILTER_SANITIZE_STRING),
            'tipo_carro'    => filter_var($dataForm['tipoVeiculo'], FILTER_SANITIZE_STRING),
            'qtd_portas'    => filter_var($dataForm['portas'], FILTER_VALIDATE_INT)
        );

        // Atualiza dados na tabela 'automoveis'
        return $this->where('auto_id', $codAutomovel)
                    ->update($tableDataFormCarro);
    }
}
