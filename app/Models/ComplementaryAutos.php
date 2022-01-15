<?php

namespace App\Models;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ComplementaryAutos extends Model
{
    protected $table = 'complementaries';
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
        return $this->whereIn('tipo_auto', ['all', $type])->where(array('ativo' => 1, 'store_id' => $store))->orderBy('nome')->get();
    }

    public function getComplemenetares()
    {
        return $this->select('complementaries.*', 'stores.store_fancy as store_name')->join('stores', 'complementaries.store_id', 'stores.id')->whereIn('store_id', Controller::getStoresByUsers())->orderBy('nome')->get();
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

    public static function getValueComplementByAutoName(int $store, string $name, int $auto): string
    {
        $complement = DB::table('complementary_auto')->where('auto_id', $auto)->first();

        if (!$complement) {
            return '';
        }

        $complements = DB::table('complementaries')->where(['nome' => $name, 'store_id' => $store])->first();

        foreach ((array)json_decode($complement->valores) as $key => $item) {
            switch ($complements->tipo_campo) {
                case 'text':
                    if ($complements->id == $key) {
                        return $item;
                    }
                    break;
                case 'select':
                    if ($complements->id == $key && $item !== null) {
                        return json_decode($complements->valores_padrao)[$item];
                    }
            }
        }

        return '';
    }

    public static function getValueComplementByAutoId(int $store, int $idComp, int $auto): string
    {
        $complement = DB::table('complementary_auto')->where('auto_id', $auto)->first();

        if (!$complement) {
            return '';
        }

        $complements = DB::table('complementaries')->where(['id' => $idComp, 'store_id' => $store])->first();

        foreach ((array)json_decode($complement->valores) as $key => $item) {
            switch ($complements->tipo_campo) {
                case 'text':
                    if ($complements->id == $key) {
                        return $item;
                    }
                    break;
                case 'select':
                    if ($complements->id == $key && $item !== null) {
                        return json_decode($complements->valores_padrao)[$item];
                    }
            }
        }

        return '';
    }

    public function getComplements($filters, $init = null, $length = null, $orderBy = array())
    {
        $testimony = $this->select("$this->table.id" ,"$this->table.nome", "$this->table.ativo", "$this->table.created_at", "$this->table.tipo_auto", "$this->table.tipo_campo", "stores.store_fancy")->whereIn('store_id', $filters['store_id']);
        $testimony->join('stores', 'stores.id', '=', $this->table.'.store_id');

        if ($filters['value']) {
            $testimony->where('nome', 'like', "%{$filters['value']}%")
                ->orWhere('ativo', 'like', "%{$filters['value']}%")
                ->orWhere('tipo_auto', 'like', "%{$filters['value']}%")
                ->orWhere('tipo_campo', 'like', "%{$filters['value']}%");
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


    public function getCountComplements($filters, $withFilter = true)
    {
        $testimony = $this->whereIn('store_id', $filters['store_id']);

        if ($withFilter && $filters['value']) {
            $testimony->where('nome', 'like', "%{$filters['value']}%")
                ->orWhere('ativo', 'like', "%{$filters['value']}%")
                ->orWhere('tipo_auto', 'like', "%{$filters['value']}%")
                ->orWhere('tipo_campo', 'like', "%{$filters['value']}%");
        }

        return $testimony->count();
    }
}
