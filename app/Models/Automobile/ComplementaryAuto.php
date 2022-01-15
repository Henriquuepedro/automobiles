<?php

namespace App\Models\Automobile;

use Illuminate\Database\Eloquent\Model;

class ComplementaryAuto extends Model
{
    protected $table = 'complementary_auto';
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
        // Atualiza dados na tabela 'complementary_auto'
        return $this->where('auto_id', $dataForm['auto_id'])->update(array('valores' => $dataForm['valores']));
    }
}
