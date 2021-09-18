<?php

namespace App\Models;

use App\Http\Controllers\Controller;
use http\Env\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        return $this->whereIn('tipo_auto', ['all', $type])->where(array('ativo' => 1, 'store_id' => $store))->orderBy('nome')->get();
    }

    public function getComplemenetares()
    {
        return $this->select('complementar_autos.*', 'stores.store_fancy as store_name')->join('stores', 'complementar_autos.store_id', 'stores.id')->whereIn('store_id', Controller::getStoresByUsers())->orderBy('nome')->get();
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
        $complement = DB::table('complementar_auto')->where('auto_id', $auto)->first();

        if (!$complement) return '';

        $complements = DB::table('complementar_autos')->where(['nome' => $name, 'store_id' => $store])->first();

        foreach ((array)json_decode($complement->valores) as $key => $item) {
            switch ($complements->tipo_campo) {
                case 'text':
                    if ($complements->id == $key) return $item;
                    break;
                case 'select':
                    if ($complements->id == $key && $item !== null) return json_decode($complements->valores_padrao)[$item];
            }
        }

        return '';
    }

    public static function getValueComplementByAutoId(int $store, int $idComp, int $auto): string
    {
        $complement = DB::table('complementar_auto')->where('auto_id', $auto)->first();

        if (!$complement) return '';

        $complements = DB::table('complementar_autos')->where(['id' => $idComp, 'store_id' => $store])->first();

        foreach ((array)json_decode($complement->valores) as $key => $item) {
            switch ($complements->tipo_campo) {
                case 'text':
                    if ($complements->id == $key) return $item;
                    break;
                case 'select':
                    if ($complements->id == $key && $item !== null) return json_decode($complements->valores_padrao)[$item];
            }
        }

        return '';
    }
}
