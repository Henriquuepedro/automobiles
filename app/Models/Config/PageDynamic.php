<?php

namespace App\Models\Config;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageDynamic extends Model
{
    use HasFactory;

    protected $table = 'page_dynamics';
    protected $fillable = [
        'nome',
        'title',
        'conteudo',
        'ativo',
        'company_id',
        'store_id',
        'user_insert',
        'user_update'
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

    public function getPageDynamics()
    {
        return $this->whereIn('store_id', Controller::getStoresByUsers())->get();
    }

    public function getPageDynamic($id, $store)
    {
        if (!is_array($store)) {
            $store = array($store);
        }

        return $this->where('id',$id)->whereIn('store_id', $store)->first();
    }

    public function getPageByName($name, $store, $ignoreId = 0)
    {
        return $this->where(['nome' => $name, 'store_id' => $store])->where('id', '!=', $ignoreId)->first();
    }

    public function getPageActiveByName($name, $store)
    {
        return $this->where(['nome' => $name, 'store_id' => $store, 'ativo' => 1])->first();
    }

    public function getPageActive($store)
    {
        return $this->where(['store_id' => $store, 'ativo' => 1])->get();
    }
}
