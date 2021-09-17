<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'active', 'permission', 'company_id','user_created', 'user_updated'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function insert(array $data)
    {
        return $this->create($data);
    }

    public function edit(array $data, int $id)
    {
        return $this->where('id', $id)->update($data);
    }

    public function getUser(int $id, int $company)
    {
        return $this->select('users.id as user_id', 'users.active as user_active', 'users.name as user_name', 'users.email as user_email', 'users_to_stores.store_id', 'users.permission', 'users.company_id')
            ->join('users_to_stores', 'users_to_stores.user_id', '=', 'users.id')
            ->where(['users.id' => $id,'users.company_id' => $company])
            ->get();
    }

    public function getUsersByCompany(int $company)
    {
        return $this->select('id', 'active', 'name', 'email')->where('company_id', $company)->get();
    }

    public function getAllDataUsersByCompany(int $company)
    {
        return $this->where('company_id', $company)->get();
    }
}
