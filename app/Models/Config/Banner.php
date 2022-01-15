<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'path', 'order', 'store_id', 'company_id'
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

    public function getBanners(int $store, int $id = null)
    {
        if ($id) {
            return $this->where(['id' => $id, 'store_id' => $store])->first();
        }

        return $this->where('store_id', $store)->orderBy('order')->get();
    }

    public function getLastNumberOrder(int $store)
    {
        $lastBanner = $this->where('store_id', $store)->orderBy('order', 'DESC')->first();

        if (!$lastBanner) {
            return 0;
        }

        return $lastBanner->order;
    }

    public function insert($data)
    {
        return $this->create($data);
    }

    public function remove(int $id)
    {
        return $this->where('id', $id)->delete();
    }

    public function edit($data, $id)
    {
        return $this->where('id', $id)->update($data);
    }

    public function rearrangeOrder(int $store)
    {
        $banners = $this->where('store_id', $store)->orderBy('order')->get();
        $order = 0;
        $updated = true;

        foreach ($banners as $banner) {
            $order++;
            $update = $this->where('id', $banner['id'])->update(['order' => $order]);
            if (!$update) {
                $updated = false;
            }
        }

        return $updated;

    }
}
