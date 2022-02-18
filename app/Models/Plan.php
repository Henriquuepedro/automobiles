<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_transaction',
        'link_billet',
        'barcode_billet',
        'date_of_expiration',
        'key_pix',
        'base64_key_pix',
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
        $where = array(['plans.created_at', '>', Carbon::now('America/Sao_Paulo')->subMonths($lastMonth)->format('Y-m-d H:i:s')]);
        if ($company !== 1) {
            $where[] = ['plans.company_id', '=', $company];
        }

        return $this->select(
            "plans.id",
            "plans.type_payment",
            "plans.gross_amount",
            "plans.client_amount",
            "plans.status",
            "plans.user_created",
            "plans.created_at",
            "plan_configs.name as plan",
            "users.name as user",
            "companies.company_fancy as company"
        )->where($where)->join('plan_configs', 'plans.id_plan', '=', 'plan_configs.id')
        ->join('users', 'users.id', '=', 'plans.user_created')
        ->join('companies', 'companies.id', '=', 'plans.company_id')
        ->orderBy('plans.id', 'DESC')->get();
    }

    public function getPayment(int $payment)
    {
        return $this->find($payment);
    }

    public static function getNamePlan(int $plan)
    {
        $query = DB::table('plan_configs')->select('name')->where('id', $plan)->first();
        return $query->name;
    }

    public function getPaymentByTransaction(int $transaction)
    {
        return $this->where('id_transaction', $transaction)->first();
    }
}
