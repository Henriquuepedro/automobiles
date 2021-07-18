<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersToStores extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'company_id',
        'store_id'
    ];
    protected $guarded = [];

    public function getStoreByUser(int $userId)
    {
        return $this->where('user_id', $userId)->get();
    }

    public function insert(array $data)
    {
        return $this->create($data);
    }

    public function removeAllStoresUser(int $user)
    {
        return $this->where('user_id', $user)->delete();
    }
}
