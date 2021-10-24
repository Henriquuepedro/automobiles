<?php

namespace App\Models\Fipe;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class FipeUpdatedValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'auto_fipe_id',
        'new_value',
        'old_value',
        'date_updated'
    ];

    protected $guarded = [];

    public function getVariationByAuto(int $auto, int $months = 6)
    {
        return $this->where([
            [
                'created_at',
                '>',
                Carbon::now('America/Sao_Paulo')->subMonth($months)->format('Y-m-d H:i:s')
            ],
            'auto_fipe_id' => $auto
        ])->orderBy('created_at', 'ASC')->get();
    }
}
