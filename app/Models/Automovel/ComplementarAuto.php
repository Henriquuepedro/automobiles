<?php

namespace App\Models\Automovel;

use Illuminate\Database\Eloquent\Model;

class ComplementarAuto extends Model
{
    protected $table = 'complementar_auto';
    protected $fillable = [
        'auto_id',
        'valores'
    ];
    protected $guarded = [];

    public function getComplementarByAuto($auto_id)
    {
        return $this->where('auto_id', $auto_id)->first();
    }

    public function insert($dataForm)
    {
        return $this->create($dataForm);
    }

    public function edit($dataForm)
    {
        // Atualiza dados na tabela 'complementar_auto'
        return $this->where('auto_id', $dataForm['auto_id'])->update(array('valores' => $dataForm['valores']));
    }
}
