<?php

namespace App\Http\Controllers\Admin\MercadoPago;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Plan;
use App\Models\PlanConfig;
use App\Models\PlanHistory;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MercadoPago\SDK;
use MercadoPago\Payment;

class Notification extends Controller
{
    private Plan $plan;
    private PlanHistory $planHistory;
    private Company $company;
    private PlanConfig $planConfig;

    public function __construct(Plan $plan, PlanHistory $planHistory, Company $company, PlanConfig $planConfig)
    {
        $this->plan = $plan;
        $this->planHistory = $planHistory;
        $this->company = $company;
        $this->planConfig = $planConfig;
    }

    /**
     * Recebimento de notificação mercado pago.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function notification(Request $request): JsonResponse
    {
        Log::debug(json_encode($request->all()));

        try {
            // Veio via IPN, não será usado, apenas webhook.
            if(!$request->input('data_id')){
                echo "[JOIN] " . __LINE__ . "\n";
                return response()->json(null);
            }

            if (
                $request->input('action') != "payment.updated" ||
                $request->input('type') != "payment"
            ) {
                echo "[JOIN] " . __LINE__ . "\n";
                return response()->json(array(), 401);
            }

            $code  = $request->input('data_id');

            $plan = $this->plan->getPaymentByTransaction($code);

            if (!$plan) {
                echo "[JOIN] " . __LINE__ . "\n";
                return response()->json(array(), 401);
            }

            $planId         = (int)$plan->id;
            $companyId      = (int)$plan->company_id;
            $planConfigId   = (int)$plan->id_plan;

            // recupera dados do mercado pago
            SDK::setAccessToken(env('MP_ACCESSTOKEN'));

            try {
                $payment = new Payment();
                $dataPayment = $payment->get($code);
            } catch(Exception $e) {
                return response()->json(array($e->getMessage()), 401);
            }

            $status         = $dataPayment->status;
            $statusDetail   = $dataPayment->status_detail;

            // verificar se o status já existe
            if ($this->planHistory->getHistoryByStatusAndStatusDetail($status, $statusDetail)) {
                return response()->json(null);
            }

            try {
                $time_zone = new DateTimeZone('America/Fortaleza');
                $date = new DateTime($dataPayment->last_modified);
                $date->setTimezone($time_zone);
                $last_modified = $date->format('Y-m-d H:i:s');
            } catch (Exception $exception) {
                $last_modified = date('Y-m-d H:i:s');
            }

            $this->planHistory->insert(array(
                'plan_id'       => $planId,
                'status_detail' => $$statusDetail,
                'status'        => $status,
                'status_date'   => $last_modified
            ));

            $this->plan->edit(array(
                'status_detail' => $$statusDetail,
                'status'        => $status
            ), $planId);

            $planConfig = $this->planConfig->getPlan($planConfigId);
            $monthPlan = $planConfig->qty_months;

            // Pedido aprovado, liberar dias do plano.
            if (in_array($status, array('approved', 'authorized'))) {
                echo "[JOIN] " . __LINE__ . "\n";
                // Pagamento já teve uma aprovação anteriormente, não deve adicionar mais dias no plano.
                if (!$this->planHistory->getStatusByPlan($planId, array('approved', 'authorized'))) {
                    echo "[JOIN] " . __LINE__ . "\n";
                    // Pagamento não tem indício de cancelamento, continuar com a aprovação e adicionar os dias.
                    if (!$this->planHistory->getStatusByPlan($planId, array('rejected', 'cancelled', 'refunded', 'charged_back'))) {
                        echo "[JOIN] " . __LINE__ . "\n";
                        // Adicionar quantidade de meses conforme o plano e atualiza o plano da empresa.
                        $this->company->setDatePlanAndUpdatePlanCompany($companyId, $planId, $monthPlan);
                    }
                }
            }
            // Pedido perdeu sua aprovação, deve verificar se chegou a ocorrer alguma aprovação para reverter.
            elseif (in_array($status, array('rejected', 'cancelled', 'refunded', 'charged_back'))) {
                echo "[JOIN] " . __LINE__ . "\n";
                // Pagamento já teve uma aprovação anteriormente, deve reverter a aprovação.
                if ($this->planHistory->getStatusByPlan($planId, array('approved', 'authorized'))) {
                    echo "[JOIN] " . __LINE__ . "\n";
                    // Pagamento já perdeu a aprovação anteriormente, não deve reverter a aprovação novamente.
                    if (!$this->planHistory->getStatusByPlan($planId, array('rejected', 'cancelled', 'refunded', 'charged_back'))) {
                        echo "[JOIN] " . __LINE__ . "\n";
                        // identificar qual o plano anterior do que precisa ser cancelado.
                        $planIdOld = $this->planHistory->getPenultimatePlanConfirmedCompany($companyId, $planId);

                        // reverter os dias, pois ocorreu um cancelamento no pagamento.
                        $this->company->setDatePlanAndUpdatePlanCompany($companyId, $planIdOld, -$monthPlan);
                    }
                }
            }

            return response()->json(null);

        } catch (Exception $e) {
            return response()->json(['error' => 'invalid'], 401);
        }
    }
}
