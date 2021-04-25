<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplementarAutos extends Model
{
    protected $table = 'complementar_autos';
    protected $fillable = [
        'auto_id',
        'valores'
    ];
    protected $guarded = [];

    public function getComplemenetaresByType($type)
    {
        return $this->where('tipo_auto', $type)->orderBy('nome')->get();
    }
}
