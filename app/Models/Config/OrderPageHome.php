<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPageHome extends Model
{
    use HasFactory;

    protected $table = 'order_page_homes';
    protected $fillable = [
        'page_id',
        'order',
        'company_id',
        'store_id'
    ];
    protected $guarded = [];

    public function getOrderPagesActived()
    {
        return $this->orderBy('order', 'ASC')->get();
    }

    public function removeAllOrderPagesActived($store)
    {
        return $this->where('store_id', $store)->delete();
    }

    public function insert($data)
    {
        return $this->create($data);
    }
}
