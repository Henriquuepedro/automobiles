<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadosFinanceiro extends Model
{
    protected $table = 'estados_financeiro';
    protected $fillable = [
        'nome',
        'user_insert',
        'user_update'
    ];
    protected $guarded = [];

    public function getFinancialStatus()
    {
        return $this->orderBy('nome')->get();
    }
}
