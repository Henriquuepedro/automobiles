<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MercadoPago\Item;
use MercadoPago\Payer;
use MercadoPago\Payment;
use MercadoPago\Preference;
use MercadoPago\SDK;
use StdClass;
use App\Http\Controllers\Admin\Exception\MercadoPagoController;


class PlanController extends Controller
{
    private Plan $plan;
    private MercadoPagoController $exceptionMercadoPagoController;

    public function __construct(Plan $plan, MercadoPagoController $exceptionMercadoPagoController)
    {
        $this->plan = $plan;
        $this->exceptionMercadoPagoController = $exceptionMercadoPagoController;
        //SDK::setAccessToken("YOUR_ACCESS_TOKEN");
    }

    public function index()
    {
        $plans = array(
            'basic'         => 'basic',
            'intermediary'  => 'intermediary',
            'advanced'      => 'advanced'
        );

        return view('admin.plan.index', compact('plans'));
    }

    public function confirm($id)
    {
        $checkout = new StdClass();
        $checkout->plan = $id;

        switch ($id) {
            case 'basic':
                $checkout->amount = 10;
                break;
            case 'intermediary':
                $checkout->amount = 15;
                break;
            case 'advanced':
                $checkout->amount = 20;
                break;
            default:
                return redirect()->route('admin.plan.index');
        }

        return view('admin.plan.confirm', compact('checkout'));
    }

    public function checkout(Request $request): JsonResponse
    {
        SDK::setAccessToken(env('MP_ACCESSTOKEN'));

        try {
            if ($request->type_payment == 'credit_card') {
                $payment = new Payment();

                $payment->transaction_amount    = (float)$request->transaction_amount;
                $payment->token                 = $request->token;
                $payment->description           = $request->description;
                $payment->installments          = (int)$request->installments;
                $payment->payment_method_id     = $request->payment_method_id;
                $payment->issuer_id             = (int)$request->issuer_id;

                $payer = new Payer();
                $payer->email           = $request->payer['email'];
                $payer->first_name      = $request->payer['name'];
                $payer->identification  = array(
                    "type"      => $request->payer['identification']['type'],
                    "number"    => $request->payer['identification']['number']
                );

                $payment->payer         = $payer;

            } elseif ($request->type_payment == 'billet') {
                $payment = new Payment();
                $payment->transaction_amount    = (float)$request->transaction_amount;
                $payment->description           = $request->description;
                $payment->payment_method_id     = "bolbradesco";
                $payment->payer = array(
                    "email"         => $request->payer['email'],
                    "first_name"    => $request->payer['firstName'],
                    "last_name"     => $request->payer['lastName'],
                    "identification" => array(
                        "type"      => $request->payer['identification']['type'],
                        "number"    => $request->payer['identification']['number']
                    ),
                    "address"=>  array(
                        "zip_code"      => $request->payer['address']['zipcode'],
                        "street_name"   => $request->payer['address']['street'],
                        "street_number" => $request->payer['address']['number'],
                        "neighborhood"  => $request->payer['address']['neigh'],
                        "city"          => $request->payer['address']['city'],
                        "federal_unit"  => $request->payer['address']['state']
                    )
                );
                //$payment->date_of_expiration = "2020-05-30T23:59:59.000-03:00"; // A data configurada deve estar entre 1 e 30 dias a partir da data de emissão do boleto. Por padrão para pagamentos com boleto é de 3 dias

            } elseif ($request->type_payment == 'pix') {
                $payment = new Payment();
                $payment->transaction_amount    = (float)$request->transaction_amount;
                $payment->description           = $request->description;
                $payment->payment_method_id     = "pix";
                $payment->payer = array(
                    "email"         => $request->payer['email'],
                    "first_name"    => $request->payer['firstName'],
                    "last_name"     => $request->payer['lastName'],
                    "identification" => array(
                        "type"      => $request->payer['identification']['type'],
                        "number"    => $request->payer['identification']['number']
                    ),
                    "address"=>  array(
                        "zip_code"      => $request->payer['address']['zipcode'],
                        "street_name"   => $request->payer['address']['street'],
                        "street_number" => $request->payer['address']['number'],
                        "neighborhood"  => $request->payer['address']['neigh'],
                        "city"          => $request->payer['address']['city'],
                        "federal_unit"  => $request->payer['address']['state']
                    )
                );
                // $payment->date_of_expiration = "2020-05-30T23:59:59.000-03:00"; // A data configurada deve ser entre 30 minutos e até 30 dias a partir da data de emissão. Por padrão, a data de vencimento para pagamentos com Pix é de 24 horas
            }

            $payment->save();
            $this->validate_payment_result($payment);
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
            'plan'              => $request->plan,
            'type_payment'      => $request->type_payment,
            'status_detail'     => $payment->status_detail,
            'installments'      => $request->installments ?? null,
            'status'            => $payment->status,
            'gross_amount'      => $payment->transaction_amount,
            'net_amount'        => $netAmount,
            'client_amount'     => $payment->transaction_details->total_paid_amount,
            'company_id'        => 1,
            'store_id'          => 1,
            'user_created'      => 1
        ));

        // pagamento foi criado. Validar a situação. Ele poder ter sido rejeitado diretamente
        try {
            $this->exceptionMercadoPagoController->setPayment($payment);
            $verify = $this->exceptionMercadoPagoController->verifyTransaction();
        }  catch (Exception $exception) {
            return response()->json(array(
                'success' => false,
                'message' => $exception->getMessage() . json_encode($payment)
            ));
        }
        if($verify['class'] == 'error'){
            return response()->json(array(
                'success' => false,
                'message' => $verify['message']
            ));
        }

        return response()->json(array('success' => true, 'message' => $verify['message']));
    }

    private function validate_payment_result($payment) {
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
