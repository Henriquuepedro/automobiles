<?php

namespace App\Models;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;

class FinancialStates extends Model
{
    protected $table = 'financial_states';
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
        $query = $this->select('financial_states.*', 'stores.store_fancy as store_name')->join('stores', 'financial_states.store_id', 'stores.id')->whereIn('store_id', Controller::getStoresByUsers());

        if ($ignoreInactive) {
            return $query->orderBy('nome')->get();
        }

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

    public function getFinancialsStates($filters, $init = null, $length = null, $orderBy = array())
    {
        $testimony = $this->select("$this->table.id" ,"$this->table.nome", "$this->table.ativo", "$this->table.created_at", "stores.store_fancy")->whereIn('store_id', $filters['store_id']);
        $testimony->join('stores', 'stores.id', '=', $this->table.'.store_id');

        if ($filters['value']) {
            $testimony->where('nome', 'like', "%{$filters['value']}%")
                ->orWhere('ativo', 'like', "%{$filters['value']}%");
        }

        if (count($orderBy) !== 0) {
            $testimony->orderBy($orderBy['field'], $orderBy['order']);
        }
        else {
            $testimony->orderBy('id', 'asc');
        }

        if ($init !== null && $length !== null) {
            $testimony->offset($init)->limit($length);
        }

        return $testimony->get();
    }


    public function getCountFinancialsStates($filters, $withFilter = true)
    {
        $testimony = $this->whereIn('store_id', $filters['store_id']);

        if ($withFilter && $filters['value']) {
            $testimony->where('nome', 'like', "%{$filters['value']}%")
                ->orWhere('ativo', 'like', "%{$filters['value']}%");
        }

        return $testimony->count();
    }
}
