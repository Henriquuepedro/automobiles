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
                $this->debugEcho("data_id not found.", $request->input('debug'));
                return response()->json(array('success' => true));
            }

            if (
                $request->input('action') != "payment.updated" ||
                $request->input('type') != "payment"
            ) {
                $this->debugEcho("type or action don't accept. [action={$request->input('action')} | type={$request->input('type')}].", $request->input('debug'));
                return response()->json(array(), 401);
            }

            $code  = $request->input('data_id');

            $plan = $this->plan->getPaymentByTransaction($code);

            if (!$plan) {
                $this->debugEcho("plan code ($code) not found.", $request->input('debug'));
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
                $this->debugEcho("get payment ($code) to mercadoPago found a error. {$e->getMessage()}", $request->input('debug'));
                return response()->json(array($e->getMessage()), 401);
            }

            $status         = $dataPayment->status;
            $statusDetail   = $dataPayment->status_detail;

            $this->debugEcho("[CODE_TRANSACTION=$code]", $request->input('debug'));
            $this->debugEcho("[PLAN=$planId]", $request->input('debug'));
            $this->debugEcho("[STATUS=$status]", $request->input('debug'));
            $this->debugEcho("[STATUS_DETAIL=$statusDetail]", $request->input('debug'));
            $this->debugEcho("[COMPANY=$companyId]", $request->input('debug'));

            // verificar se o status já existe
            if ($this->planHistory->getHistoryByStatusAndStatusDetail($planId, $status, $statusDetail)) {
                $this->debugEcho("status ($status) and status_detail ($statusDetail) in use to plan_id ($planId).", $request->input('debug'));
                return response()->json(array('success' => true));
            }

            try {
                $time_zone = new DateTimeZone('America/Fortaleza');
                $date = new DateTime($dataPayment->last_modified);
                $date->setTimezone($time_zone);
                $last_modified = $date->format('Y-m-d H:i:s');
            } catch (Exception $exception) {
                $last_modified = date('Y-m-d H:i:s');
            }

            $planConfig = $this->planConfig->getPlan($planConfigId);
            $monthPlan = $planConfig->qty_months;

            $this->debugEcho("[LAST_MODIFIED=$last_modified]", $request->input('debug'));
            $this->debugEcho("[PLAN_CONFIG=$planConfigId]", $request->input('debug'));
            $this->debugEcho("[MONTH=$monthPlan]", $request->input('debug'));

            // Pedido aprovado, liberar dias do plano.
            if (in_array($status, array('approved', 'authorized'))) {
                $this->debugEcho("payment ($code) and plan_id ($planId) is approved or authorized.", $request->input('debug'));
                // Pagamento já teve uma aprovação anteriormente, não deve adicionar mais dias no plano.
                if (!$this->planHistory->getStatusByPlan($planId, array('approved', 'authorized'))) {
                    $this->debugEcho("payment ($code) and plan_id ($planId) wasn't approved or authorized.", $request->input('debug'));
                    // Pagamento não tem indício de cancelamento, continuar com a aprovação e adicionar os dias.
                    if (!$this->planHistory->getStatusByPlan($planId, array('rejected', 'cancelled', 'refunded', 'charged_back'))) {
                        $this->debugEcho("payment ($code) and plan_id ($planId) wasn't rejected or cancelled or refunded or charged_back.", $request->input('debug'));
                        // Adicionar quantidade de meses conforme o plano e atualiza o plano da empresa.
                        $this->company->setDatePlanAndUpdatePlanCompany($companyId, $planId, $monthPlan);
                    }
                }
            }
            // Pedido perdeu sua aprovação, deve verificar se chegou a ocorrer alguma aprovação para reverter.
            elseif (in_array($status, array('rejected', 'cancelled', 'refunded', 'charged_back'))) {
                $this->debugEcho("payment ($code) and plan_id ($planId) is rejected or cancelled or refunded or charged_back.", $request->input('debug'));
                // Pagamento já teve uma aprovação anteriormente, deve reverter a aprovação.
                if ($this->planHistory->getStatusByPlan($planId, array('approved', 'authorized'))) {
                    $this->debugEcho("payment ($code) and plan_id ($planId) has already been approved or authorized.", $request->input('debug'));
                    // Pagamento já perdeu a aprovação anteriormente, não deve reverter a aprovação novamente.
                    if (!$this->planHistory->getStatusByPlan($planId, array('rejected', 'cancelled', 'refunded', 'charged_back'))) {
                        $this->debugEcho("payment ($code) and plan_id ($planId) wasn't rejected or cancelled or refunded or charged_back.", $request->input('debug'));
                        // identificar qual o plano anterior do que precisa ser cancelado.
                        $planIdOld = $this->planHistory->getPenultimatePlanConfirmedCompany($companyId, $planId);

                        // reverter os dias, pois ocorreu um cancelamento no pagamento.
                        $this->company->setDatePlanAndUpdatePlanCompany($companyId, $planIdOld, -$monthPlan);
                    }
                }
            }

            $this->planHistory->insert(array(
                'plan_id'       => $planId,
                'status_detail' => $statusDetail,
                'status'        => $status,
                'status_date'   => $last_modified
            ));

            $this->plan->edit(array(
                'status_detail' => $statusDetail,
                'status'        => $status
            ), $planId);

            return response()->json(array('success' => true));

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    private function debugEcho(string $text, ?bool $show)
    {
        if ($show) {
            echo $text . "\n";
        }
    }
}
