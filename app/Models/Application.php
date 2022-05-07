<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'image',
        'active',
        'user_created',
        'user_updated'
    ];
    protected $guarded = [];

    public function getAllAppsByStore(int $store)
    {
        return $this->select(
            'applications.id',
            'applications.name',
            'applications.description',
            'applications.image',
            'application_to_stores.active'
        )
        ->Leftjoin('application_to_stores', function($join) use ($store) {
            $join->on('application_to_stores.app_id', '=', 'applications.id');
            $join->where('application_to_stores.store_id', '=', $store);
        })
        ->where('applications.active', true)->get();
    }

    public function getAppByStore(int $store, int $app)
    {
        return $this->select(
            'applications.id',
            'applications.name',
            'applications.description',
            'applications.image',
            'application_to_stores.active'
        )
        ->Leftjoin('application_to_stores', function($join) use ($store) {
            $join->on('application_to_stores.app_id', '=', 'applications.id');
            $join->where('application_to_stores.store_id', '=', $store);
        })
        ->where(array('applications.active' => true, 'applications.id' => $app))
        ->first();
    }
}
