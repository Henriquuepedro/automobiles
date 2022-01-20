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
        'company_id',
        'user_id',
        'user_insert',
        'user_update'
    ];
    protected $guarded = [];

    public function insert($dataForm)
    {
        return $this->create($dataForm);
    }
}
