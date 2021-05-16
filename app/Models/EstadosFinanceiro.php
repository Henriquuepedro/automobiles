<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadosFinanceiro extends Model
{
    protected $table = 'estados_financeiro';
    protected $fillable = [
        'nome',
        'ativo',
        'user_insert',
        'user_update'
    ];
    protected $guarded = [];

    public function getFinancialsStatus($ignoreInactive = false)
    {
        if ($ignoreInactive)
            return $this->orderBy('nome')->get();

        return $this->where('ativo', true)->orderBy('nome')->get();
    }

    public function insert($data)
    {
        return $this->create($data);
    }

    public function edit($data, $id)
    {
        return $this->where('id', $id)->update($data);
    }

    public function getFinancialStatus($id)
    {
        return $this->find($id);
    }

    public function getFinancialStatusByName($name)
    {
        return $this->where('nome', $name)->first();
    }
}
