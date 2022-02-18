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

    public function getStatusByPlan(int $plan, $status)
    {
        if (!is_array($status)) {
            $status = array($status);
        }

        return $this->where('plan_id', $plan)->whereIn('status', $status)->first();
    }

    public function getPenultimatePlanConfirmedCompany(int $company, int $planIgnore)
    {
        $plansIgnore = array($planIgnore);
        while (true) {
            $data = $this->select('plan_configs.id as plan_config_id', 'plan_histories.plan_id as plan_id')
                ->where('company_id', $company)
                ->join('plans', 'plan_histories.plan_id', '=', 'plans.id')
                ->join('plan_configs', 'plans.id_plan', '=', 'plan_configs.id')
                ->whereNotIn('plan_histories.plan_id', $plansIgnore)
                ->whereIn('plan_histories.status', array('approved', 'authorized'))
                ->orderBy('plan_histories.id', 'DESC')
                ->first();

            // não existe mais registro, então nunca teve um plano aprovado.
            if (!$data) {
                return null;
            }

            // verifica se o pagamento não teve cancelamento.
            if (
                $this->where('company_id', $company)
                ->where('plan_id', $data->plan_id)
                ->whereIn('status', array('rejected', 'cancelled', 'refunded', 'charged_back'))
                ->count() === 0
            ) {
                return $data->plan_config_id;
            }

            $plansIgnore[] = $data->plan_id;
        }
    }
}
