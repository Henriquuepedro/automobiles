<?php

namespace App\Models\Rent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'auto_id',
        'day_start',
        'day_end',
        'value',
        'user_insert',
        'user_update'
    ];

    protected $guarded = [];

    public function removeAllByAuto(int $auto_id)
    {
        return $this->where('auto_id', $auto_id)->delete();
    }

    public function getByAuto(int $auto_id)
    {
        return $this->where('auto_id', $auto_id)->get();
    }
}
