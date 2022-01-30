<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanConfig;
use DateTimeZone;
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

class PlanController extends Controller
{
    private Plan $plan;
    private MercadoPagoController $exceptionMercadoPagoController;
    private PlanConfig $planConfig;

    public function __construct(Plan $plan, MercadoPagoController $exceptionMercadoPagoController, PlanConfig $planConfig)
    {
        $this->plan = $plan;
        $this->exceptionMercadoPagoController = $exceptionMercadoPagoController;
        $this->planConfig = $planConfig;
    }

    public function index()
    {
        $type = 'monthly';
        $plans = $this->planConfig->getByType($type);

        return view('admin.plan.index', compact('plans'));
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

        SDK::setAccessToken(env('MP_ACCESSTOKEN'));

        try {
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
        } catch (Exception $exception) {
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

        $this->plan->insert(array(
            'id_transaction'    => $payment->id,
            'link_billet'       => $payment->transaction_details->external_resource_url ?? null,
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
            'company_id'        => 1,
            'store_id'          => 1,
            'user_created'      => 1
        ));

        // Pagamento foi criado. Validar a situação. Ele poder ter sido rejeitado diretamente.
        try {
            $this->exceptionMercadoPagoController->setPayment($payment);
            $verify = $this->exceptionMercadoPagoController->verifyTransaction();
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
}
