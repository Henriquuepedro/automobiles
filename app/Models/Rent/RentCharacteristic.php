<?php

namespace App\Models\Rent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentCharacteristic extends Model
{
    use HasFactory;

    protected $table = 'rent_characteristics';
    protected $fillable = [
        'name',
        'type_auto',
        'active',
        'company_id',
        'store_id',
        'user_insert',
        'user_update'
    ];
    protected $guarded = [];

    public function getCharacteristicsByType($type, $store)
    {
        return $this->whereIn('type_auto', ['all', $type])->where(array('active' => 1, 'store_id' => $store))->orderBy('name')->get();
    }

    public function getCharacteristic(int $id)
    {
        return $this->find($id);
    }

    public function getCharacteristics($filters, $init = null, $length = null, $orderBy = array())
    {
        $testimony = $this->select("$this->table.id" ,"$this->table.type_auto" ,"$this->table.name", "$this->table.active", "$this->table.created_at", "stores.store_fancy")->whereIn('store_id', $filters['store_id']);
        $testimony->join('stores', 'stores.id', '=', $this->table.'.store_id');

        if ($filters['value']) {
            $testimony->where('name', 'like', "%{$filters['value']}%")
                ->orWhere('active', 'like', "%{$filters['value']}%");
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

    public function getCountCharacteristics($filters, $withFilter = true)
    {
        $testimony = $this->whereIn('store_id', $filters['store_id']);

        if ($withFilter && $filters['value']) {
            $testimony->where('name', 'like', "%{$filters['value']}%")
                ->orWhere('active', 'like', "%{$filters['value']}%");
        }

        return $testimony->count();
    }

    public function getCharacteristicByName($name, $store, $ignoreId = 0)
    {
        return $this->where(['name' => $name, 'store_id' => $store])->where('id', '!=', $ignoreId)->first();
    }

    public function insert($data)
    {
        return $this->create($data);
    }

    public function edit($data, $id)
    {
        return $this->where('id', $id)->update($data);
    }
}
