<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UsersToStores extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'company_id',
        'store_id'
    ];
    protected $guarded = [];

    public static function getStoreByUser(int $userId)
    {
        return DB::table('users_to_stores')->where('user_id', $userId)->get();

//        return $this->where('user_id', $userId)->get();
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
