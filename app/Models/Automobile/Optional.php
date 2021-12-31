<?php

namespace App\Models\Automobile;

use Illuminate\Database\Eloquent\Model;

class Optional extends Model
{
    protected $table = 'opcional';
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
        // Atualiza dados na tabela 'complementar_auto'
        return $this->where('auto_id', $dataForm['auto_id'])->update(array('valores' => $dataForm['valores']));
    }

    public function getOptionalByAuto($auto_id)
    {
        return $this->where('auto_id', $auto_id)->first();
    }
}
