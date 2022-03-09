<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentCharacteristic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type_auto',
        'active',
        'company_id',
        'store_id',
        'user_insert',
        'user_update'
    ];
    protected $guarded = [];

    public function getCharacteristicsByType($type, $store)
    {
        return $this->whereIn('type_auto', ['all', $type])->where(array('active' => 1, 'store_id' => $store))->orderBy('name')->get();
    }
}
