<?php

namespace App\Models\Automobile;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ColorAuto extends Model
{
    protected $table = 'colors_auto';
    protected $fillable = [
        'nome',
        'user_insert',
        'user_update',
        'store_id',
        'active'
    ];
    protected $guarded = [];

    public function insert($data)
    {
        return $this->create($data);
    }

    public function edit($data, $id)
    {
        return $this->where('id', $id)->update($data);
    }

    public function getAllColors()
    {
        return $this->orderBy('nome')->get();
    }

    public static function getNameColorById(int $id): string
    {
        $color = DB::table('colors_auto')->select('nome')->find($id);
        return $color->nome ?? '';
    }

    public function getColorById(int $id)
    {
        return $this->where('id', $id)->first();
    }

    public function getColorByStore(int $id, int $store): string
    {
        return $this->where(['id' => $id, 'store_id' => $store])->first();
    }

    public function getColorsFetch($filters, $init = null, $length = null, $orderBy = array())
    {
        $color = $this->select("$this->table.id" ,"$this->table.nome", "$this->table.active", "$this->table.created_at", "stores.store_fancy");
        $color->leftJoin('stores', 'stores.id', '=', $this->table.'.store_id');

        $color->where(function($query) use ($filters) {
            $query->whereIn('store_id', $filters['store_id'])
                ->orWhere('store_id', null);
        });

        if ($filters['value']) {
            $color->where('nome', 'like', "%{$filters['value']}%")
                ->orWhere('active', 'like', "%{$filters['value']}%");
        }

        if (count($orderBy) !== 0) {
            $color->orderBy($orderBy['field'], $orderBy['order']);
        }
        else {
            $color->orderBy('id', 'asc');
        }

        if ($init !== null && $length !== null) {
            $color->offset($init)->limit($length);
        }

        return $color->get();
    }

    public function getCountColorsFetch($filters, $withFilter = true)
    {
        $color = $this->where(function($query) use ($filters) {
            $query->whereIn('store_id', $filters['store_id'])
                ->orWhere('store_id', null);
        });

        if ($withFilter && $filters['value']) {
            $color->where('nome', 'like', "%{$filters['value']}%")
                ->orWhere('active', 'like', "%{$filters['value']}%");
        }

        return $color->count();
    }

    public function getColorByName($name, $store, $ignoreId = 0)
    {
        $color = $this->where('nome', $name)->where('id', '!=', $ignoreId);

        $color->where(function($query) use ($store) {
            $query->where('store_id', $store)
                ->orWhere('store_id', null);
        });

        return $color->first();
    }

    public function getColorsActiveByStore(int $store)
    {
        $color = $this->select('id', 'nome')
            ->where('active', true)
            ->where(function($query) use ($store) {
            $query->where('store_id', $store)
                ->orWhere('store_id', null);
        })->orderBy('nome', 'ASC');

        return $color->get();
    }
}
