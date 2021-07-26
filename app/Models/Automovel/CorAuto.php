<?php

namespace App\Models\Automovel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public static function getColorById(int $id)
    {
        $color = DB::table('cor_autos')->find($id);
        return $color->nome ?? '';
    }
}
