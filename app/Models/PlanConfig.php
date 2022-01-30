<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'type',
        'code',
        'value',
        'qty_months',
        'description',
        'active',
        'primary'
    ];
    protected $guarded = [];

    public function getByType(string $type)
    {
        return $this->where(['type' => $type, 'active' => true])->get();
    }

    public function getByTypeCode(string $type, string $code)
    {
        return $this->where(['type' => $type, 'code' => $code, 'active' => true])->first();
    }
}
