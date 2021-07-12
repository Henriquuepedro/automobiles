<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersToStores extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'store_id'
    ];
    protected $guarded = [];

    public function getStoreByUser($userId)
    {
        return $this->where('user_id', $userId)->get();
    }
}
