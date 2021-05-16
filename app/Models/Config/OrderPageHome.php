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
        'order'
    ];
    protected $guarded = [];

    public function getOrderPagesActived()
    {
        return $this->orderBy('order', 'ASC')->get();
    }

    public function removeAllOrderPagesActived()
    {
        return $this->whereNotNull('id')->delete();
    }

    public function insert($data)
    {
        return $this->create($data);
    }
}
