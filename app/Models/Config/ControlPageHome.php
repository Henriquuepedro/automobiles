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

    public function getControlPagesActived(int $store, $ignoreInactive = false)
    {
        $query = $this->select('control_page_homes.*', 'order_page_homes.order')
                    ->leftJoin('order_page_homes', function($join) use ($store) {
                        $join->on('order_page_homes.page_id', '=', 'control_page_homes.id');
                        $join->where(function($join) use ($store) {
                            $join->where('order_page_homes.store_id', $store)
                                ->orWhere('order_page_homes.store_id', null);
                        });
                    });

        if ($ignoreInactive) $query->where('order_page_homes.store_id', '!=', null);

        return $query->where('control_page_homes.ativo', true)
            ->orderBy('order_page_homes.order', 'ASC')
            ->get();
    }

    public function getControlById($id)
    {
        return $this->find($id);
    }
}
