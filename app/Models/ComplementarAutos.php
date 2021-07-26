<?php

namespace App\Models;

use App\Http\Controllers\Controller;
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
        'company_id',
        'store_id',
        'user_insert',
        'user_update'
    ];
    protected $guarded = [];

    public function getComplementaresByType($type, $store)
    {
        return $this->where(array('tipo_auto' => $type, 'ativo' => 1, 'store_id' => $store))->orderBy('nome')->get();
    }

    public function getComplemenetares()
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

    public function getComplement($id)
    {
        return $this->find($id);
    }

    public function getComplementByName($name, $store, $ignoreId = 0)
    {
        return $this->where(['nome' => $name, 'store_id' => $store])->where('id', '!=', $ignoreId)->first();
    }
}
