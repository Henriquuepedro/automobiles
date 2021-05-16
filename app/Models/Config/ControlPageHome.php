<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ControlPageHome extends Model
{
    use HasFactory;

    protected $table = 'control_page_homes';
    protected $fillable = [
        'nome',
        'ativo'
    ];
    protected $guarded = [];

    public function getControlPagesActived()
    {
        return $this->select('control_page_homes.*', 'order_page_homes.order')->leftJoin('order_page_homes', 'order_page_homes.page_id', '=', 'control_page_homes.id')->where('ativo', true)->orderBy('order_page_homes.order', 'ASC')->get();
    }

    public function getControlById($id)
    {
        return $this->find($id);
    }
}
