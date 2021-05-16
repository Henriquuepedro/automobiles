<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageDynamic extends Model
{
    use HasFactory;

    protected $table = 'page_dynamics';
    protected $fillable = [
        'nome',
        'conteudo',
        'ativo',
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
        return $this->get();
    }

    public function getPageDynamic($id)
    {
        return $this->find($id);
    }
}
