<?php

namespace App\Models\Automovel;

use Illuminate\Database\Eloquent\Model;

class EstadoFinanceiro extends Model
{
    protected $table = 'estadofinanceiro';
    protected $fillable = [
        'NCODAUTO',
        'FINANCIADO',
        'MULTAS',
        'IPVAPAGO',
        'LEILAO'
    ];
    protected $guarded = ['NCODESTADOFINANCEIRO'];

    public function insert($dataForm, $codAutomovel)
    {
        $tableDataFormEstadoFinanceiro = array(
            'NCODAUTO'      => $codAutomovel,
            'FINANCIADO'    => isset($dataForm['financiado']) ? 1 : 0,
            'MULTAS'        => isset($dataForm['comMultas']) ? 1 : 0,
            'IPVAPAGO'      => isset($dataForm['ipvaPago']) ? 1 : 0,
            'LEILAO'        => isset($dataForm['leilao']) ? 1 : 0
        );
        return $this->create($tableDataFormEstadoFinanceiro);
    }
}
