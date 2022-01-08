<?php

namespace App\Models;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;

class Optionals extends Model
{
    protected $table = 'optionals';
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
        return $this->whereIn('tipo_auto', ['all', $type])->where(array('ativo' => 1, 'store_id' => $store))->orderBy('nome')->get();
    }

    public function getAllOptionals()
    {
        return $this->select('optionals.*', 'stores.store_fancy as store_name')->join('stores', 'optionals.store_id', 'stores.id')->whereIn('store_id', Controller::getStoresByUsers())->orderBy('nome')->get();
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

    public function getOptionalsByStore($store)
    {
        return $this->select('id','nome as name')->where(array('ativo' => 1, 'store_id' => $store))->orderBy('nome')->get();
    }

    public function getOptionals($filters, $init = null, $length = null, $orderBy = array())
    {
        $testimony = $this->select("$this->table.id" ,"$this->table.nome", "$this->table.ativo", "$this->table.created_at", "$this->table.tipo_auto", "stores.store_fancy")->whereIn('store_id', $filters['store_id']);
        $testimony->join('stores', 'stores.id', '=', $this->table.'.store_id');

        if ($filters['value']) {
            $testimony->where('nome', 'like', "%{$filters['value']}%")
                ->orWhere('ativo', 'like', "%{$filters['value']}%")
                ->orWhere('tipo_auto', 'like', "%{$filters['value']}%");
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


    public function getCountOptionals($filters, $withFilter = true)
    {
        $testimony = $this->whereIn('store_id', $filters['store_id']);

        if ($withFilter && $filters['value']) {
            $testimony->where('nome', 'like', "%{$filters['value']}%")
                ->orWhere('ativo', 'like', "%{$filters['value']}%")
                ->orWhere('tipo_auto', 'like', "%{$filters['value']}%");
        }

        return $testimony->count();
    }
}
