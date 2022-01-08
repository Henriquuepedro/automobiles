<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimony extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'testimony',
        'picture',
        'rate',
        'active',
        'primary',
        'company_id',
        'store_id',
        'user_created',
        'user_updated'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    public function getTestimonies($filters, $init = null, $length = null, $orderBy = array())
    {
        $testimony = $this->select("testimonies.*", "stores.store_fancy")->whereIn('store_id', $filters['store_id']);
        $testimony->join('stores', 'stores.id', '=', 'testimonies.store_id');

        if ($filters['value']) {
            $testimony->where('name', 'like', "%{$filters['value']}%")
                ->orWhere('testimony', 'like', "%{$filters['value']}%")
                ->orWhere('rate', 'like', "%{$filters['value']}%");
        }

        if (count($orderBy) !== 0) {
            $testimony->orderBy($orderBy['field'], $orderBy['order']);
        }
        else {
            $testimony->orderBy('id', 'asc');
        }

        if ($init !== null && $length !== null) {
            $testimony->offset($init)->limit($length);
        }

        return $testimony->get();
    }


    public function getCountTestimonies($filters, $withFilter = true)
    {
        $testimony = $this->whereIn('store_id', $filters['store_id']);

        if ($withFilter && $filters['value']) {
            $testimony->where('name', 'like', "%{$filters['value']}%")
                ->orWhere('testimony', 'like', "%{$filters['value']}%")
                ->orWhere('rate', 'like', "%{$filters['value']}%");
        }

        return $testimony->count();
    }

    public function getTestimony($id)
    {
        return $this->find($id);
    }

    public function edit($data, $id)
    {
        return $this->where('id', $id)->update($data);
    }

    public function remove($id)
    {
        return $this->where('id', $id)->delete();
    }

    public function insert($data)
    {
        $create = $this->create($data);
        return $create->id;
    }

    public function getTestimonyPrimary($store)
    {
        return $this->select('id','name', 'testimony', 'picture', 'rate')->where(['store_id' => $store, 'primary' => 1])->get();
    }
}
