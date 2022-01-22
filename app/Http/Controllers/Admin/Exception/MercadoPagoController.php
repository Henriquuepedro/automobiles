<?php

namespace App\Http\Controllers\Admin\Exception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MercadoPagoController extends Controller
{
    private object $payment;

    #Setters and Getters
    public function setPayment($payment)
    {
        $this->payment = $payment;
    }

    public function getPayment(): object
    {
        return $this->payment;
    }

    #Verify transaction
    public function verifyTransaction(): array
    {
        if ($this->getPayment()->status == 'approved' || $this->getPayment()->status == 'in_process' || $this->getPayment()->status == 'pending') {
            return [
                'class' => 'success',
                'message'=>$this->getStatus()
            ];
        }
        elseif ($this->getPayment()->status == "rejected") {
            return [
                'class' => 'error',
                'message'=>$this->getStatus()
            ];
        } else {
            return [
                'class' => 'error',
                'message'=>$this->getStatus()
            ];
        }
    }

    #Get Status
    public function getStatus(): string
    {
        $status=[
            'accredited'                            => 'Seu pagamento foi aprovado! Você verá o nome '.$this->getPayment()->statement_descriptor.' na sua fatura de cartão de crédito.',
            'pending_contingency'                   => 'Estamos processando o pagamento. Em até 2 dias úteis informaremos por e-mail o resultado.',
            'pending_waiting_payment'               => 'Boleto gerado com sucesso. Assim que que o boleto for compensado iremos avisar por e-mial.',
            'pending_review_manual'                 => 'Estamos processando o pagamento. Em até 2 dias úteis informaremos por e-mail se foi aprovado ou se precisamos de mais informações. Fique atento no e-mail cadastrado',
            'cc_rejected_bad_filled_card_number'    => 'Confira o número do cartão.',
            'cc_rejected_bad_filled_date'           => 'Confira a data de validade.',
            'cc_rejected_bad_filled_other'          => 'Confira os dados.',
            'cc_rejected_bad_filled_security_code'  => 'Confira o código de segurança.',
            'cc_rejected_blacklist'                 => 'Não conseguimos processar seu pagamento.',
            'cc_rejected_call_for_authorize'        => 'Você deve autorizar o pagamento do valor ao Mercado Pago.',
            'cc_rejected_card_error'                => 'Não conseguimos processar seu pagamento.',
            'cc_rejected_duplicated_payment'        => 'Você já efetuou um pagamento com esse valor. Caso precise pagar novamente, utilize outro cartão ou outra forma de pagamento.',
            'cc_rejected_high_risk'                 => 'Seu pagamento foi recusado. Escolha outra forma de pagamento. Recomendamos meios de pagamento em dinheiro.',
            'cc_rejected_insufficient_amount'       => 'O cartão possui saldo insuficiente.',
            'cc_rejected_invalid_installments'      => 'O cartão não processa pagamentos parcelados.',
            'cc_rejected_max_attempts'              => 'Você atingiu o limite de tentativas permitido. Escolha outro cartão ou outra forma de pagamento.',
            'cc_rejected_other_reason'              => 'O cartão não processou seu pagamento'
        ];

        if (array_key_exists($this->getPayment()->status_detail,$status)) {
            return $status[$this->getPayment()->status_detail];
        } else {
            return "Houve um problema na sua requisição. Tente novamente!";
        }
    }

    #Get Error
    public function getErrors(): string
    {
        $error = array(
            '205'   => 'Digite o número do seu cartão.',
            '208'   => 'Escolha um mês.',
            '209'   => 'Escolha um ano.',
            '212'   => 'Informe seu documento.',
            '213'   => 'Informe seu documento.',
            '214'   => 'Informe seu documento.',
            '220'   => 'Informe seu banco emissor.',
            '221'   => 'Informe seu sobrenome.',
            '224'   => 'Digite o código de segurança.',
            'E301'  => 'Há algo de errado com o número do cartão. Digite novamente.',
            'E302'  => 'Confira o código de segurança.',
            '316'   => 'Por favor, digite um nome válido.',
            '322'   => 'Confira seu documento.',
            '323'   => 'Confira seu documento.',
            '324'   => 'Confira seu documento.',
            '325'   => 'Confira a data.',
            '326'   => 'Confira a data.'
        );

        if (array_key_exists($this->getPayment()->error->causes[0]->code,$error)) {
            return $error[$this->getPayment()->error->causes[0]->code];
        } else {
            return "Houve um problema na sua requisição. Tente novamente!";
        }
    }
}
