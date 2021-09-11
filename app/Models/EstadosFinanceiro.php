<?php

namespace App\Models;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;

class EstadosFinanceiro extends Model
{
    protected $table = 'estados_financeiro';
    protected $fillable = [
        'nome',
        'ativo',
        'company_id',
        'store_id',
        'user_insert',
        'user_update'
    ];
    protected $guarded = [];

    public function getFinancialsStatus($ignoreInactive = false)
    {
        $query = $this->whereIn('store_id', Controller::getStoresByUsers());

        if ($ignoreInactive)
            return $query->orderBy('nome')->get();

        return $query->where('ativo', true)->orderBy('nome')->get();
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

    public function getFinancialStatusByName($name, $store, $ignoreId = 0)
    {
        return $this->where(['nome' => $name, 'store_id' => $store])->where('id', '!=', $ignoreId)->first();
    }

    public function getAllFinancialsStatusByStore(int $store)
    {
        return $this->where(['store_id' => $store, 'ativo' => true])->orderBy('nome')->get();
    }
}
