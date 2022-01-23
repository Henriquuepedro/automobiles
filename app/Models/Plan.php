<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_transaction',
        'link_billet',
        'payment_method_id',
        'payment_type_id',
        'plan',
        'type_payment',
        'status_detail',
        'installments',
        'status',
        'gross_amount',
        'net_amount',
        'client_amount',
        'company_id',
        'store_id',
        'user_created',
        'user_updated'
    ];
    protected $guarded = [];

    public function insert($dataForm)
    {
        return $this->create($dataForm);
    }
}
