<?php

namespace App\Models\Rent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentAutoToCharacteristic extends Model
{
    use HasFactory;

    protected $fillable = [
        'auto_id',
        'characteristic_id'
    ];
    protected $guarded = [];

    public function getByAutoAndCharacteristic(int $auto, int $characteristic)
    {
        return $this->where(array('auto_id' => $auto, 'characteristic_id' => $characteristic))->first();
    }

    public function removeByAuto(int $auto)
    {
        return $this->where('auto_id', $auto)->delete();
    }

    public function insert(array $data)
    {
        return $this->create($data);
    }
}
