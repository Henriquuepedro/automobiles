<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanConfig;
use App\Models\Store;
use App\Models\User;
use DateTime;
use DateTimeZone;
use Error;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use MercadoPago\Payer;
use MercadoPago\Payment;
use MercadoPago\SDK;
use StdClass;
use App\Http\Controllers\Admin\Exception\MercadoPagoController;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Configuration;
use DateTimeImmutable;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use App\Models\PlanHistory;
use App\Models\Company;

class PlanController extends Controller
{
    private Plan $plan;
    private MercadoPagoController $exceptionMP;
    private PlanConfig $planConfig;
    private PlanHistory $planHistory;

    public function __construct(Plan $plan, MercadoPagoController $exceptionMP, PlanConfig $planConfig, PlanHistory $planHistory)
    {
        $this->plan         = $plan;
        $this->exceptionMP  = $exceptionMP;
        $this->planConfig   = $planConfig;
        $this->planHistory  = $planHistory;
    }

    public function index()
    {
        $type       = 'monthly';
        $plans      = $this->planConfig->getByType($type);
        $histories  = $this->plan->getRequestByCompany(auth()->user()->company_id);

        return view('admin.plan.index', compact('plans', 'histories'));
    }

    public function confirm($type, $id)
    {
        $plan = $this->planConfig->getByTypeCode($type, $id);

        if (!$plan) {
            return redirect()->route('admin.plan.index');
        }

        $now = new DateTimeImmutable("now");
        $config = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText(config('app.key')));

        $token = $config->builder()
            ->issuedBy(url()->current())
            ->withHeader('iss', url()->current())
            ->permittedFor(url()->current())
            ->issuedAt($now)
            ->expiresAt($now->modify('+12 hours'))
            ->withClaim('uid', 1)
            ->withClaim('amount', $plan->amount)
            ->withClaim('plan', $id)
            ->withClaim('type', $type)
            ->withClaim('code', $plan->id)
            ->getToken($config->signer(), $config->signingKey());

        $tokenStr = $token->toString();

        $checkout = new StdClass();
        $checkout->plan     = $tokenStr;
        $checkout->idPlan   = $id;
        $checkout->typePlan = $type;
        $checkout->amount   = $plan->amount;
        $checkout->namePlan = $plan->name;

        return view('admin.plan.confirm', compact('checkout'));
    }

    public function checkout(Request $request): JsonResponse
    {
        try {
            $config = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText(config('app.key')));
            $clock = new SystemClock(new DateTimeZone('America/Sao_Paulo'));
            assert($config instanceof Configuration);

            $token  = $config->parser()->parse($request->input('plan'));
            $claims = $token->claims();
            assert($token instanceof UnencryptedToken);

            $config->setValidationConstraints(
                new LooseValidAt($clock),
                new PermittedFor(url()->current())
            );

            $constraints = $config->validationConstraints();

            if (! $config->validator()->validate($token, ...$constraints)) {
                return response()->json(array(
                    'success' => false,
                    'message' => 'Nao foi possível identificar o plano de pagamento. Recarregue a página!'
                ));
            }

            $namePlan   = $claims->get('plan');
            $typePlan   = $claims->get('type');
            $idPlan     = $claims->get('code');
            $amount     = $claims->get('amount');

            if ($request->input('transaction_amount') != $amount) {
                return response()->json(array(
                    'success' => false,
                    'message' => 'Nao foi possível identificar os valores para pagamento. Recarregue a página!'
                ));
            }
        } catch (Exception $exception) {
            return response()->json(array(
                'success' => false,
                'message' => 'Nao foi possível identificar o plano de pagamento. Recarregue a página!'
            ));
        }


        try {
            SDK::setAccessToken(env('MP_ACCESSTOKEN'));
            if ($request->input('type_payment') == 'credit_card') {
                $payment = new Payment();

                $payment->transaction_amount    = (float)$amount;
                $payment->token                 = $request->input('token');
                $payment->description           = $request->input('description');
                $payment->installments          = (int)$request->input('installments');
                $payment->payment_method_id     = $request->input('payment_method_id');
                $payment->issuer_id             = (int)$request->input('issuer_id');

                $payer = new Payer();
                $payer->email           = $request->input('payer')['email'];
                $payer->first_name      = $request->input('payer')['name'];
                $payer->identification  = array(
                    "type"      => $request->input('payer')['identification']['type'],
                    "number"    => $request->input('payer')['identification']['number']
                );

                $payment->payer         = $payer;

            } elseif ($request->input('type_payment') == 'billet' || $request->input('type_payment') == 'pix') {
                $payment = new Payment();
                $payment->transaction_amount    = (float)$amount;
                $payment->description           = $request->input('description');
                $payment->payment_method_id     = $request->input('type_payment') == 'billet' ? 'bolbradesco' : 'pix';
                $payment->payer = array(
                    "email"         => $request->input('payer')['email'],
                    "first_name"    => $request->input('payer')['firstName'],
                    "last_name"     => $request->input('payer')['lastName'],
                    "identification" => array(
                        "type"      => $request->input('payer')['identification']['type'],
                        "number"    => $request->input('payer')['identification']['number']
                    ),
                    "address"=>  array(
                        "zip_code"      => $request->input('payer')['address']['zipcode'],
                        "street_name"   => $request->input('payer')['address']['street'],
                        "street_number" => $request->input('payer')['address']['number'],
                        "neighborhood"  => $request->input('payer')['address']['neigh'],
                        "city"          => $request->input('payer')['address']['city'],
                        "federal_unit"  => $request->input('payer')['address']['state']
                    )
                );

//                if ($request->input('type_payment') == 'billet') {
//                    $payment->date_of_expiration = "2020-05-30T23:59:59.000-03:00"; // A data configurada deve estar entre 1 e 30 dias a partir da data de emissão do boleto. Por padrão para pagamentos com boleto é de 3 dias
//                } elseif ($request->input('type_payment') == 'pix') {
//                    $payment->date_of_expiration = "2020-05-30T23:59:59.000-03:00"; // A data configurada deve ser entre 30 minutos e até 30 dias a partir da data de emissão. Por padrão, a data de vencimento para pagamentos com Pix é de 24 horas
//                }
            }

            $payment->save();
            $this->validatePaymentResult($payment);
        } catch (Exception | Error $exception) {
            return response()->json(array(
                'success' => false,
                'message' => $exception->getMessage()
            ));
        }

        if (!property_exists($payment, 'id')) {
            return response()->json(array(
                'success' => false,
                'message' => "Não foi possível localizar o identificador de pagamento. Tente novamente mais tarde!\n" . json_encode($payment)
            ));
        }

        if ($payment->payment_type_id === 'bank_transfer') {
            $netAmount = $payment->transaction_amount * 0.99; // taxa de 0.99% co pix (https://www.mercadopago.com.br/ajuda/custo-receber-pagamentos_220)
        } elseif ($payment->payment_type_id === 'credit_card') {
            $netAmount = $payment->transaction_amount - 3.49; // taxa de R$ 3.49 no boleto (https://www.mercadopago.com.br/ajuda/custo-receber-pagamentos_220)
        } else {
            $netAmount = $payment->transaction_details->net_received_amount;
        }


        $dateOfExpiration = $payment->date_of_expiration ?? null;

        if ($dateOfExpiration) {
            try {
                $time_zone = new DateTimeZone('America/Fortaleza');
                $date = new DateTime($dateOfExpiration);
                $date->setTimezone($time_zone);
                $dateOfExpiration = $date->format('Y-m-d H:i:s');
            } catch (Exception $exception) {
                $dateOfExpiration = null;
            }
        }

        try {
            $time_zone = new DateTimeZone('America/Fortaleza');
            $date = new DateTime($payment->date_created);
            $date->setTimezone($time_zone);
            $dateCreated = $date->format('Y-m-d H:i:s');
        } catch (Exception $exception) {
            $dateCreated = date('Y-m-d H:i:s');
        }

        $paymentPlan = $this->plan->insert(array(
            'id_transaction'    => $payment->id,
            'link_billet'       => $payment->transaction_details->external_resource_url ?? null,
            'barcode_billet'    => $payment->barcode->content ?? null,
            'date_of_expiration'=> $dateOfExpiration,
            'key_pix'           => $payment->point_of_interaction->transaction_data->qr_code ?? null,
            'base64_key_pix'    => $payment->point_of_interaction->transaction_data->qr_code_base64 ?? null,
            'payment_method_id' => $payment->payment_method_id,
            'payment_type_id'   => $payment->payment_type_id,
            'name_plan'         => $namePlan,
            'type_plan'         => $typePlan,
            'id_plan'           => $idPlan,
            'type_payment'      => $request->input('type_payment'),
            'status_detail'     => $payment->status_detail,
            'installments'      => $request->input('installments'),
            'status'            => $payment->status,
            'gross_amount'      => $payment->transaction_amount,
            'net_amount'        => $netAmount,
            'client_amount'     => $payment->transaction_details->total_paid_amount,
            'company_id'        => $request->user()->company_id,
            'user_created'      => $request->user()->id
        ));

        $this->planHistory->insert(array(
            'plan_id'       => $paymentPlan->id,
            'status_detail' => $payment->status_detail,
            'status'        => $payment->status,
            'status_date'   => $dateCreated
        ));

        // Pagamento foi criado. Validar a situação. Ele poder ter sido rejeitado diretamente.
        try {
            $this->exceptionMP->setPayment($payment);
            $verify = $this->exceptionMP->verifyTransaction();
        }  catch (Exception $exception) {
            return response()->json(array(
                'success' => false,
                'message' => $exception->getMessage() . json_encode($payment)
            ));
        }
        if ($verify['class'] == 'error') {
            return response()->json(array(
                'success' => false,
                'message' => $verify['message']
            ));
        }

        return response()->json(array(
            'success' => true,
            'message' => $verify['message']
        ));
    }

    /**
     * @param   object      $payment
     * @throws  Exception
     */
    private function validatePaymentResult(object $payment) {
        if ($payment->id === null) {
            $error_message = 'Unknown error cause';

            if($payment->error !== null) {
                $sdk_error_message = $payment->error->message;
                $error_message = $sdk_error_message !== null ? $sdk_error_message : $error_message;
            }

            throw new Exception($error_message);
        }
    }

    public function getHistoryPayment(int $payment): JsonResponse
    {
        $histories = $this->planHistory->getHistoryPayment($payment);
        $dataPaymentOrder = $this->plan->getPayment($payment);
        $dataHistory = array();

        foreach ($histories as $history) {
            $dataHistory[] = array(
                'status_detail' => $history['status_detail'],
                'status_date'   => date('d/M H:i', strtotime($history['status_date'])),
                'status'        => $history['status']
            );
        }

        $dataPayment = array(
            "link_billet"           => $dataPaymentOrder->link_billet,
            "barcode_billet"        => $dataPaymentOrder->barcode_billet,
            "date_of_expiration"    => date('d/m/Y H:i', strtotime($dataPaymentOrder->date_of_expiration)),
            "key_pix"               => $dataPaymentOrder->key_pix,
            "payment_method_id"     => $dataPaymentOrder->payment_method_id,
            "name_plan"             => Plan::getNamePlan($dataPaymentOrder->id_plan),
            "type_payment"          => $dataPaymentOrder->type_payment,
            "status"                => $dataPaymentOrder->status,
            "status_detail"         => $dataPaymentOrder->status_detail,
            "installments"          => $dataPaymentOrder->installments,
            "gross_amount"          => 'R$ ' . number_format($dataPaymentOrder->gross_amount, 2, ',', '.'),
            "client_amount"         => 'R$ ' . number_format($dataPaymentOrder->client_amount, 2, ',', '.'),
            "company"               => Company::getFancyCompany($dataPaymentOrder->company_id),
            "user_created"          => User::getNameUser($dataPaymentOrder->user_created),
            "created_at"            => date('d/m/Y H:i', strtotime($dataPaymentOrder->created_at)),
            "base64_key_pix"        => $dataPaymentOrder->base64_key_pix,
            "waiting_payment"       => $dataPaymentOrder->date_of_expiration === null || strtotime($dataPaymentOrder->date_of_expiration) > now(new DateTimeZone('America/Sao_Paulo'))->getTimestamp()
        );

        return response()->json(array('history' => $dataHistory, 'payment' => $dataPayment));
    }
}
