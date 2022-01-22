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
        SDK::setAccessToken("TEST-6116112164560242-060501-17bc947d5ca4151ee6581659b826ade9-158980362");

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

                $payment->save();

                $this->validate_payment_result($payment);

                $response = array(
                    'status'        => $payment->status,
                    'status_detail' => $payment->status_detail,
                    'id'            => $payment->id,
                    'response_complete' => $payment
                );
            }

        } catch (Exception $exception) {
            $response_fields = array('error_message' => $exception->getMessage(), 'form' => $request->all());
            return response()->json($response_fields);
        }

        if (property_exists($payment, 'id')) {
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível localizar o identificador de pagamento. Tente novamente mais tarde!'
            ));
        }

        $this->plan->insert(array(
            'id_transaction'    => $payment->id,
            'link_billet'       => $payment->transaction_details->external_resource_url ?? null,
            'payment_method_id' => $payment->payment_method_id,
            'payment_type_id'   => $payment->payment_type_id,
            'plan'              => $request->plan,
            'type_payment'      => $request->type_payment,
            'status_detail'     => $request->type_payment,
            'company_id'        => 1,
            'store_id'          => 1,
            'user_created'      => 1
        ));

        // pagamento foi criado. Validar a situação. Ele poder ter sido rejeitado diretamente
        $this->exceptionMercadoPagoController->setPayment($payment);
        $verify = $this->exceptionMercadoPagoController->verifyTransaction();
        if($verify['class'] == 'error'){
            return response()->json(array(
                'success' => false,
                'message' => $verify['message']
            ));
        }

        /*
        {
            "status": "approved",
            "status_detail": "accredited",
            "id": 3055677,
            "date_approved": "2019-02-23T00:01:10.000-04:00",
            "payer": {
                    ...
                },
            "payment_method_id": "visa",
            "payment_type_id": "credit_card",
            "refunds": [],
            ...
        }
        */

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
