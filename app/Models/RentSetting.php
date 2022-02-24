<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'visible_type',
        'company_id',
        'store_id',
        'user_updated'
    ];

    protected $guarded = [];

    public function getByStore(int $store): ?object
    {
        return $this->select('visible_type')->where('store_id', $store)->first();
    }

    public function insert(array $data)
    {
        return $this->create($data);
    }

    public function updateByStore(int $store, array $data)
    {
        return $this->where('store_id', $store)->update($data);
    }
}
