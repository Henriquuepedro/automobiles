<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MercadoPago\Item;
use MercadoPago\Payer;
use MercadoPago\Payment;
use MercadoPago\Preference;
use MercadoPago\SDK;
use StdClass;


class PlanController extends Controller
{
    private Plan $plan;

    public function __construct(Plan $plan)
    {
        $this->plan = $plan;
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

        if ($request->type_payment == 'credit_card') {
            $payment = new Payment();

            $payment->transaction_amount    = (float)$request->transactionAmount;
            $payment->token                 = $request->token;
            $payment->description           = $request->description;
            $payment->installments          = (int)$request->installments;
            $payment->payment_method_id     = $request->paymentMethodId;
            $payment->issuer_id             = (int)$request->issuer;

            $payer = new Payer();
            $payer->email           = $request->cardholderEmail;
            $payer->identification  = array(
                "type"      => $request->identificationType,
                "number"    => $request->identificationNumber
            );
            $payer->first_name      = $request->cardholderName;
            $payment->payer         = $payer;

            $payment->save();
        }

        $response = array(
            'status'        => $payment->status,
            'status_detail' => $payment->status_detail,
            'id'            => $payment->id,
            'response_complete' => $payment
        );

        $this->plan->insert(array(
            'id_transaction'    => $payment->id,
            'link_billet'       => $payment->transaction_details->external_resource_url ?? null,
            'payment_method_id' => $payment->payment_method_id,
            'payment_type_id'   => $payment->payment_type_id,
            'plan'              => $request->plan,
            'type_payment'      => $request->type_payment,
            'company_id'        => 1,
            'user_id'           => 1,
            'user_insert'       => 1
        ));

//        {
//            "status": "approved",
//            "status_detail": "accredited",
//            "id": 3055677,
//            "date_approved": "2019-02-23T00:01:10.000-04:00",
//            "payer": {
//                    ...
//                },
//            "payment_method_id": "visa",
//            "payment_type_id": "credit_card",
//            "refunds": [],
//            ...
//        }

        return response()->json($response);
    }
}
