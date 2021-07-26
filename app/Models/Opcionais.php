<?php

namespace App\Models;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;

class Opcionais extends Model
{
    protected $table = 'opcionais';
    protected $fillable = [
        'nome',
        'tipo_auto',
        'ativo',
        'company_id',
        'store_id',
        'user_insert',
        'user_update'
    ];
    protected $guarded = [];

    public function getOptionalsByType($type, $store)
    {
        return $this->where(array('tipo_auto' => $type, 'ativo' => 1, 'store_id' => $store))->orderBy('nome')->get();
    }

    public function getOpicionais()
    {
        return $this->whereIn('store_id', Controller::getStoresByUsers())->orderBy('nome')->get();
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

    public function getOptionalByName($name, $store, $ignoreId = 0)
    {
        return $this->where(['nome' => $name, 'store_id' => $store])->where('id', '!=', $ignoreId)->first();
    }
}
