<?php

namespace App\Models\Automobile;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ColorAuto extends Model
{
    protected $table = 'colors_auto';
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
        $color = DB::table('colors_auto')->find($id);
        return $color->nome ?? '';
    }
}
