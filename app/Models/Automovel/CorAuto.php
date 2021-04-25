<?php

namespace App\Models\Automovel;

use Illuminate\Database\Eloquent\Model;

class CorAuto extends Model
{
    protected $table = 'cor_autos';
    protected $fillable = [
        'auto_id',
        'valores'
    ];
    protected $guarded = [];

    public function getAllColors()
    {
        return $this->orderBy('nome')->get();
    }
}
