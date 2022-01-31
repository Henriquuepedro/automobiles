<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_transaction',
        'link_billet',
        'key_pix',
        'payment_method_id',
        'payment_type_id',
        'name_plan',
        'type_plan',
        'id_plan',
        'type_payment',
        'status_detail',
        'installments',
        'status',
        'gross_amount',
        'net_amount',
        'client_amount',
        'company_id',
        'user_created',
        'user_updated'
    ];
    protected $guarded = [];

    public function insert($dataForm)
    {
        return $this->create($dataForm);
    }

    public function getRequestByCompany(int $company, int $lastMonth = 6)
    {
        return $this->select(
            "plans.type_payment",
            "plans.gross_amount",
            "plans.client_amount",
            "plans.status",
            "plans.user_created",
            "plans.created_at",
            "plan_configs.name as plan",
            "users.name as user"
        )->where([
            ['plans.company_id', '=', $company],
            ['plans.created_at', '>', Carbon::now('America/Sao_Paulo')->subMonths($lastMonth)->format('Y-m-d H:i:s')]
        ])->join('plan_configs', 'plans.id_plan', '=', 'plan_configs.id')
        ->join('users', 'users.id', '=', 'plans.user_created')
        ->orderBy('plans.id', 'DESC')->get();
    }
}
