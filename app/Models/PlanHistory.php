<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'status_detail',
        'status',
        'status_date'
    ];
    protected $guarded = [];

    public function insert(array $data)
    {
        return $this->create($data);
    }

    public function getHistoryPayment(int $payment)
    {
        return $this->where('plan_id', $payment)->get();
    }
}
