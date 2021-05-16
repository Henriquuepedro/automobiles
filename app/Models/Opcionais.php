<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opcionais extends Model
{
    protected $table = 'opcionais';
    protected $fillable = [
        'nome',
        'tipo_auto',
        'ativo',
        'user_insert',
        'user_update'
    ];
    protected $guarded = [];

    public function getOptionalsByType($type)
    {
        return $this->where(array('tipo_auto' => $type, 'ativo' => 1))->orderBy('nome')->get();
    }

    public function getOpicionais()
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

    public function getOptional($id)
    {
        return $this->find($id);
    }

    public function getOptionalByName($name)
    {
        return $this->where('nome', $name)->first();
    }
}
