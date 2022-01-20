{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Planos', 'no-active' => []]])
{{-- Título da página --}}
@section('title', 'Planos')

@section('content')
    <div class="box">
        <div class="box-body">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Pagamento</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-5 d-flex justify-content-center">
                        <div class="col-md-4 text-center">
                            <h4><label>Cartão <input type="radio" name="type_payment" value="credit_card"/></label></h4>
                        </div>
                        <div class="col-md-4 text-center">
                            <h4><label>Pix <input type="radio" name="type_payment" value="pix"/></label></h4>
                        </div>
                        <div class="col-md-4 text-center">
                            <h4><label>Boleto <input type="radio" name="type_payment" value="billet"/></label></h4>
                        </div>
                    </div>
                    <div class="row" id="credit_card" style="display: none">
                        <form enctype="multipart/form-data" id="card-form-checkout" method="POST" class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="form-checkout__cardNumber">Número do Cartão</label>
                                        <input type="text" name="cardNumber" id="form-checkout__cardNumber" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form-checkout__cardExpirationDate">Data de Expiração</label>
                                        <input type="text" name="cardExpirationDate" id="form-checkout__cardExpirationDate" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form-checkout__securityCode">Código de Segurança</label>
                                        <input type="text" name="securityCode" id="form-checkout__securityCode" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="form-checkout__cardholderName">Nome do Titular</label>
                                        <input type="text" name="cardholderName" id="form-checkout__cardholderName" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="form-checkout__cardholderEmail">Email do Titular</label>
                                        <input type="email" name="cardholderEmail" id="form-checkout__cardholderEmail" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="form-checkout__issuer">Emissora</label>
                                        <select name="issuer" id="form-checkout__issuer" class="form-control" readonly=""></select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="form-checkout__identificationType">Tipo de Documento</label>
                                        <select name="identificationType" id="form-checkout__identificationType" class="form-control"></select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="form-checkout__identificationNumber">Documento</label>
                                        <input type="text" name="identificationNumber" id="form-checkout__identificationNumber" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="form-checkout__installments">Parcelas</label>
                                        <select name="installments" id="form-checkout__installments" class="form-control"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group d-flex justify-content-end">
                                        <button type="submit" id="form-checkout__submit" class="btn btn-success">Realizar Pagamento</button>
                                    </div>
                                </div>
                            </div>
                            <progress value="0" class="progress-bar">Carregando...</progress>
                        </form>
                    </div>
                    <div class="row" id="pix" style="display: none">
                        <form enctype="multipart/form-data" id="pix-form-checkout" method="POST" class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="form-checkout__pixFirstname">Nome</label>
                                        <input type="text" name="pixFirstname" id="form-checkout__pixFirstname" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="form-checkout__pixLastname">Sobrenome</label>
                                        <input type="text" name="pixLastname" id="form-checkout__pixLastname" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="form-checkout__pixEmail">Email do Titular</label>
                                        <input type="email" name="pixEmail" id="form-checkout__pixEmail" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="form-checkout__pixIdentificationType">Tipo de Documento</label>
                                        <select name="pixIdentificationType" id="form-checkout__pixIdentificationType" class="form-control">
                                            <option value="CPF">CPF</option>
                                            <option value="CNPJ">CNPJ</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="form-checkout__pixIdentificationNumber">Documento</label>
                                        <input type="text" name="pixIdentificationNumber" id="form-checkout__pixIdentificationNumber" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form-checkout__pixAddressZipcode">CEP</label>
                                        <input type="text" name="pixAddressZipcode" id="form-checkout__pixAddressZipcode" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="form-checkout__pixAddressStreet">Endereço</label>
                                        <input type="text" name="pixAddressStreet" id="form-checkout__pixAddressStreet" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form-checkout__pixAddressNumber">Número</label>
                                        <input type="email" name="pixAddressNumber" id="form-checkout__pixAddressNumber" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form-checkout__pixAddressNeighborhood">Bairro</label>
                                        <input type="text" name="pixAddressNeighborhood" id="form-checkout__pixAddressNeighborhood" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="form-checkout__pixAddressCity">Cidade</label>
                                        <input type="text" name="pixAddressCity" id="form-checkout__pixAddressCity" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form-checkout__pixAddressState">UF</label>
                                        <input type="email" name="pixAddressState" id="form-checkout__pixAddressState" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group d-flex justify-content-end">
                                        <button type="submit" id="form-checkout__pixSubmit" class="btn btn-success">Realizar Pagamento</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="row" id="billet" style="display: none">
                        <form enctype="multipart/form-data" id="billet-form-checkout" method="POST" class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="form-checkout__billetFirstname">Nome</label>
                                        <input type="text" name="billetFirstname" id="form-checkout__billetFirstname" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="form-checkout__billetLastname">Sobrenome</label>
                                        <input type="text" name="billetLastname" id="form-checkout__billetLastname" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="form-checkout__billetEmail">Email do Titular</label>
                                        <input type="email" name="billetEmail" id="form-checkout__billetEmail" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="form-checkout__billetIdentificationType">Tipo de Documento</label>
                                        <select name="billetIdentificationType" id="form-checkout__billetIdentificationType" class="form-control">
                                            <option value="CPF">CPF</option>
                                            <option value="CNPJ">CNPJ</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="form-checkout__billetIdentificationNumber">Documento</label>
                                        <input type="text" name="billetIdentificationNumber" id="form-checkout__billetIdentificationNumber" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form-checkout__billetAddressZipcode">CEP</label>
                                        <input type="text" name="billetAddressZipcode" id="form-checkout__billetAddressZipcode" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="form-checkout__billetAddressStreet">Endereço</label>
                                        <input type="text" name="billetAddressStreet" id="form-checkout__billetAddressStreet" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form-checkout__billetAddressNumber">Número</label>
                                        <input type="email" name="billetAddressNumber" id="form-checkout__billetAddressNumber" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form-checkout__billetAddressNeighborhood">Bairro</label>
                                        <input type="text" name="billetAddressNeighborhood" id="form-checkout__billetAddressNeighborhood" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="form-checkout__billetAddressCity">Cidade</label>
                                        <input type="text" name="billetAddressCity" id="form-checkout__billetAddressCity" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form-checkout__billetAddressState">UF</label>
                                        <input type="email" name="billetAddressState" id="form-checkout__billetAddressState" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group d-flex justify-content-end">
                                        <button type="submit" id="form-checkout__billetSubmit" class="btn btn-success">Realizar Pagamento</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <input type="hidden" name="amount" value="{{ $checkout->amount }}">
                    <input type="hidden" name="plan" value="{{ $checkout->plan }}">
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script src="https://sdk.mercadopago.com/js/v2"></script>
    <script>
        const mp = new MercadoPago('TEST-2786b4d7-dfd9-4382-899f-b5f057f80b82');

        // Step #3
        const cardForm = mp.cardForm({
            amount: $('[name="amount"]').val(),
            autoMount: true,
            form: {
                id: "card-form-checkout",
                cardholderName: {
                    id: "form-checkout__cardholderName",
                    placeholder: "Titular do cartão",
                },
                cardholderEmail: {
                    id: "form-checkout__cardholderEmail",
                    placeholder: "E-mail",
                },
                cardNumber: {
                    id: "form-checkout__cardNumber",
                    placeholder: "Número do cartão",
                },
                cardExpirationDate: {
                    id: "form-checkout__cardExpirationDate",
                    placeholder: "Data de vencimento (MM/YYYY)",
                },
                securityCode: {
                    id: "form-checkout__securityCode",
                    placeholder: "Código de segurança",
                },
                installments: {
                    id: "form-checkout__installments",
                    placeholder: "Parcelas",
                },
                identificationType: {
                    id: "form-checkout__identificationType",
                    placeholder: "Tipo de documento",
                },
                identificationNumber: {
                    id: "form-checkout__identificationNumber",
                    placeholder: "Número do documento",
                },
                issuer: {
                    id: "form-checkout__issuer",
                    placeholder: "Banco emissor",
                },
            },
            callbacks: {
                onFormMounted: error => {
                    if (error) return console.warn("Form Mounted handling error: ", error);
                    console.log("Form mounted");
                },
                onSubmit: event => {
                    event.preventDefault();

                    const {
                        paymentMethodId: payment_method_id,
                        issuerId: issuer_id,
                        cardholderEmail: email,
                        amount,
                        token,
                        installments,
                        identificationNumber,
                        identificationType,
                    } = cardForm.getCardFormData();

                    fetch(window.location.origin+"/admin/planos/confirmar", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({
                            token,
                            issuer_id,
                            payment_method_id,
                            transaction_amount: Number(amount),
                            installments: Number(installments),
                            description: "Descrição do produto",
                            plan: $('[name="plan"]').val(),
                            type_payment: 'credit_card',
                            payer: {
                                email,
                                identification: {
                                    type: identificationType,
                                    number: identificationNumber,
                                },
                            },
                        }),
                    });
                },
                onFetching: (resource) => {
                    console.log("Fetching resource: ", resource);

                    // Animate progress bar
                    const progressBar = document.querySelector(".progress-bar");
                    progressBar.removeAttribute("value");

                    return () => {
                        progressBar.setAttribute("value", "0");
                    };
                }
            },
        });

        $('[name="type_payment"]').on('change', async function(){
            await $('[name="type_payment"]').each(function (){
                $(`#${$(this).val()}`).slideUp('slow');
            });

            $(`#${$(this).val()}`).slideDown('slow');
        });
    </script>
@endsection
@section('css')
@endsection
