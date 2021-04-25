<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opcionais extends Model
{
    protected $table = 'opcionais';
    protected $fillable = [
        'nome',
        'tipo_auto',
        'user_insert',
        'user_update'
    ];
    protected $guarded = [];

    public function getOptionalsByType($type)
    {
        return $this->where('tipo_auto', $type)->orderBy('nome')->get();
    }
}
