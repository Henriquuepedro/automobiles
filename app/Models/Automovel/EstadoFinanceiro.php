<?php

namespace App\Models\Automovel;

use Illuminate\Database\Eloquent\Model;

class EstadoFinanceiro extends Model
{
    protected $table = 'estadofinanceiro';
    protected $fillable = [
        'auto_id',
        'valores'
    ];
    protected $guarded = [];

    public function insert($dataForm)
    {
        return $this->create($dataForm);
    }

    public function edit($dataForm)
    {
        // Atualiza dados na tabela 'estado_financeiro'
        return $this->where('auto_id', $dataForm['auto_id'])->update(array('valores' => $dataForm['valores']));
    }

    public function getFinancialsStatusByStore($auto_id)
    {
        return $this->where('auto_id', $auto_id)->first();
    }
}
