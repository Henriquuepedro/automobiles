<?php

namespace App\Models;

use http\Env\Request;
use Illuminate\Database\Eloquent\Model;

class ComplementarAutos extends Model
{
    protected $table = 'complementar_autos';
    protected $fillable = [
        'nome',
        'tipo_auto',
        'tipo_campo',
        'valores_padrao',
        'ativo',
        'user_insert',
        'user_update'
    ];
    protected $guarded = [];

    public function getComplementaresByType($type)
    {
        return $this->where(array('tipo_auto' => $type, 'ativo' => 1))->orderBy('nome')->get();
    }

    public function getComplemenetares()
    {
        return $this->orderBy('nome')->get();
    }

    public function insert($data)
    {
        return $this->create($data);
    }

    public function edit($data, $id)
    {
        return $this->where('id', $id)->update($data);
    }

    public function getComplement($id)
    {
        return $this->find($id);
    }

    public function getComplementByName($name)
    {
        return $this->where('nome', $name)->first();
    }
}
