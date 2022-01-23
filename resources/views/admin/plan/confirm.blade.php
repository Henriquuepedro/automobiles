{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Confirmar Pagamento', 'no-active' =>  [['route' => 'admin.plan.index', 'name' => 'Planos']], 'route_back_page' => 'admin.plan.index']])
{{-- Título da página --}}
@section('title', 'Planos')

@section('content')
    <div class="box">
        <div class="box-body">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Selecione uma forma de pagamento</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-5 d-flex justify-content-center">
                        <div class="col-md-4 text-center">
                            <label class="cursor-pointer">
                                <img src="{{ asset('assets/admin/dist/images/system/credit_card.png') }}" width="60">
                                <h5 class="font-weight-bold">Cartão de Crédito</h5>
                                <input type="radio" name="type_payment" value="credit_card" class="icheck d-none"/>
                            </label>
                        </div>
                        <div class="col-md-4 text-center">
                            <label class="cursor-pointer">
                                <img src="{{ asset('assets/admin/dist/images/system/pix.png') }}" width="60">
                                <h5 class="font-weight-bold">Pix</h5>
                                <input type="radio" name="type_payment" value="pix" class="icheck d-none"/>
                            </label>
                        </div>
                        <div class="col-md-4 text-center">
                            <label class="cursor-pointer">
                                <img src="{{ asset('assets/admin/dist/images/system/billet.png') }}" width="60">
                                <h5 class="font-weight-bold">Boleto</h5>
                                <input type="radio" name="type_payment" value="billet" class="icheck d-none"/>
                            </label>
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
                                        <input type="text" maxlength="3" name="securityCode" id="form-checkout__securityCode" class="form-control" />
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
                                    <div class="form-group d-flex justify-content-between">
                                        <a href="{{ route('admin.plan.index') }}" class="btn btn-danger">Voltar Para Planos</a>
                                        <button type="submit" id="form-checkout__submit" class="btn btn-success">Realizar Pagamento</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="row" id="pix" style="display: none">
                        <form enctype="multipart/form-data" id="pix-form-checkout" method="POST" class="col-md-12">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label for="form-checkout__pixFirstname">Nome</label>
                                        <input type="text" name="pixFirstname" id="form-checkout__pixFirstname" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="form-checkout__pixLastname">Sobrenome</label>
                                        <input type="text" name="pixLastname" id="form-checkout__pixLastname" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="form-checkout__pixEmail">Email do Titular</label>
                                        <input type="email" name="pixEmail" id="form-checkout__pixEmail" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form-checkout__pixIdentificationType">Tipo de Documento</label>
                                        <select name="pixIdentificationType" id="form-checkout__pixIdentificationType" class="form-control">
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
                                        <input type="text" name="pixAddressNumber" id="form-checkout__pixAddressNumber" class="form-control" />
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
                                        <input type="text" name="pixAddressState" id="form-checkout__pixAddressState" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group d-flex justify-content-between">
                                        <a href="{{ route('admin.plan.index') }}" class="btn btn-danger">Voltar Para Planos</a>
                                        <button type="submit" id="form-checkout__pixSubmit" class="btn btn-success">Realizar Pagamento</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="row" id="billet" style="display: none">
                        <form enctype="multipart/form-data" id="billet-form-checkout" method="POST" class="col-md-12">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label for="form-checkout__billetFirstname">Nome</label>
                                        <input type="text" name="billetFirstname" id="form-checkout__billetFirstname" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="form-checkout__billetLastname">Sobrenome</label>
                                        <input type="text" name="billetLastname" id="form-checkout__billetLastname" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="form-checkout__billetEmail">Email do Titular</label>
                                        <input type="email" name="billetEmail" id="form-checkout__billetEmail" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form-checkout__billetIdentificationType">Tipo de Documento</label>
                                        <select name="billetIdentificationType" id="form-checkout__billetIdentificationType" class="form-control">
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
                                        <input type="text" name="billetAddressNumber" id="form-checkout__billetAddressNumber" class="form-control" />
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
                                        <input type="text" name="billetAddressState" id="form-checkout__billetAddressState" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group d-flex justify-content-between">
                                        <a href="{{ route('admin.plan.index') }}" class="btn btn-danger">Voltar Para Planos</a>
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
    <script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js"></script>
    <script>
        $(function (){
            $('form-checkout__securityCode').mask('0000');
            $('input[name*="AddressZipcode"]').mask('00000-000');
            $('#form-checkout__cardNumber').mask('0000 0000 0000 0000');
            $('#form-checkout__cardExpirationDate').mask('00/0000');
            $('#form-checkout__identificationType, #form-checkout__billetIdentificationNumber, #form-checkout__pixIdentificationNumber').trigger('change');
        });

        $('#form-checkout__identificationType, #form-checkout__billetIdentificationNumber, #form-checkout__pixIdentificationNumber').change(function (){
            const form = $(this).parents('form');
            if ($(this).val() === 'CNPJ') {
                $('input[id*=dentificationNumber]', form).mask('00.000.000/0000-00');
            } else {
                $('input[id*=dentificationNumber]', form).mask('000.000.000-00');
            }
        })

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
                    if (error) {
                        return console.warn("Form Mounted handling error: ", error);
                    }
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
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            token,
                            issuer_id,
                            payment_method_id,
                            transaction_amount: Number(amount),
                            installments: Number(installments),
                            description: "Plano Mensal: " + $('[name="plan"]').val(),
                            plan: $('[name="plan"]').val(),
                            type_payment: 'credit_card',
                            payer: {
                                name: $('#form-checkout__cardholderName').val(),
                                email,
                                identification: {
                                    type: identificationType,
                                    number: identificationNumber,
                                },
                            },
                        }),
                    })
                    .then(res => res.json())
                    .then(function(response) {

                        $('#form-checkout__cardNumber').mask('0000 0000 0000 0000');
                        $('#form-checkout__identificationType').trigger('change');

                        if (response.success) {
                            Swal.fire({
                                title: 'Pagamento enviado com sucesso!',
                                text: response.message,
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonColor: '#28a745',
                                confirmButtonText: 'Continuar',
                            }).then(result => {
                                window.location.replace(window.location.origin + '/admin/planos')
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Não foi possível enivar o pagamento!',
                                footer: response.message ?? 'Entre em contato com um de nossos operadores, para um atendimento.'
                            });
                            $('#form-checkout__submit').prop('disabled', false);
                        }
                    })
                    .catch(function(err) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Não foi possível enivar o pagamento!',
                            footer: err.message ?? 'Entre em contato com um de nossos operadores, para um atendimento.'
                        });
                        $('#form-checkout__submit').prop('disabled', false);
                        $('#form-checkout__cardNumber').mask('0000 0000 0000 0000');
                        $('#form-checkout__identificationType').trigger('change');
                    })
                },
                onFetching: (resource) => {
                    if (resource === 'cardToken') {
                        $('#form-checkout__cardNumber').unmask().val(onlyNumbers($('#form-checkout__cardNumber').val()));
                        $('#form-checkout__identificationNumber').unmask().val(onlyNumbers($('#form-checkout__identificationNumber').val()));
                        $('#form-checkout__submit').prop('disabled', true);
                    }
                }
            },
        });

        $('#billet-form-checkout').on('submit', function(e) {
            e.preventDefault();

            $('#form-checkout__billetSubmit').prop('disabled', true);

            const data = {
                transaction_amount: Number($('[name="amount"]').val()),
                description: "Plano Mensal: " + $('[name="plan"]').val(),
                plan: $('[name="plan"]').val(),
                type_payment: 'billet',
                payer: {
                    firstName: $('#form-checkout__billetFirstname').val(),
                    lastName: $('#form-checkout__billetLastname').val(),
                    email: $('#form-checkout__billetEmail').val(),
                    identification: {
                        type: $('#form-checkout__billetIdentificationType').val(),
                        number: onlyNumbers($('#form-checkout__billetIdentificationNumber').val()),
                    },
                    address: {
                        zipcode: onlyNumbers($('#form-checkout__billetAddressZipcode').val()),
                        street: $('#form-checkout__billetAddressStreet').val(),
                        number: $('#form-checkout__billetAddressNumber').val(),
                        neigh: $('#form-checkout__billetAddressNeighborhood').val(),
                        city: $('#form-checkout__billetAddressCity').val(),
                        state: $('#form-checkout__billetAddressState').val(),
                    },
                },
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: window.location.origin+"/admin/planos/confirmar",
                data,
                dataType: 'json',
                success: response => {
                    if (response.success) {
                        Swal.fire({
                            title: 'Pagamento enviado com sucesso!',
                            html: response.message,
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'Continuar',
                        }).then(result => {
                            window.location.replace(window.location.origin + '/admin/planos')
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Não foi possível enivar o pagamento!',
                            footer: response.message ?? 'Entre em contato com um de nossos operadores, para um atendimento.'
                        });
                        $('#form-checkout__billetSubmit').prop('disabled', false);
                    }
                }, error: e => {
                    console.log(e)
                }
            });
        });

        $('#pix-form-checkout').on('submit', function(e) {
            e.preventDefault();

            $('#form-checkout__pixSubmit').prop('disabled', true);

            const data = {
                transaction_amount: Number($('[name="amount"]').val()),
                description: "Plano Mensal: " + $('[name="plan"]').val(),
                plan: $('[name="plan"]').val(),
                type_payment: 'pix',
                payer: {
                    firstName: $('#form-checkout__pixFirstname').val(),
                    lastName: $('#form-checkout__pixLastname').val(),
                    email: $('#form-checkout__pixEmail').val(),
                    identification: {
                        type: $('#form-checkout__pixIdentificationType').val(),
                        number: onlyNumbers($('#form-checkout__pixIdentificationNumber').val()),
                    },
                    address: {
                        zipcode: onlyNumbers($('#form-checkout__pixAddressZipcode').val()),
                        street: $('#form-checkout__pixAddressStreet').val(),
                        number: $('#form-checkout__pixAddressNumber').val(),
                        neigh: $('#form-checkout__pixAddressNeighborhood').val(),
                        city: $('#form-checkout__pixAddressCity').val(),
                        state: $('#form-checkout__pixAddressState').val(),
                    },
                },
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: window.location.origin+"/admin/planos/confirmar",
                data,
                dataType: 'json',
                success: response => {
                    if (response.success) {
                        Swal.fire({
                            title: 'Pagamento enviado com sucesso!',
                            html: response.message,
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'Continuar',
                        }).then(result => {
                            window.location.replace(window.location.origin + '/admin/planos')
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Não foi possível enivar o pagamento!',
                            footer: response.message ?? 'Entre em contato com um de nossos operadores, para um atendimento.'
                        });
                        $('#form-checkout__pixSubmit').prop('disabled', false);
                    }
                }, error: e => {
                    console.log(e)
                }
            });
        });

        $('[name="type_payment"]').on('change', async function(){
            await $('[name="type_payment"]').each(function (){
                $(`#${$(this).val()}`).slideUp('slow');
                let srcImage = $(this).parents('label').find('img').attr('src');
                srcImage = srcImage.replace(`${$(this).val()}_active`, $(this).val());
                $(this).parents('label').removeClass('text-primary').find('img').prop('src', srcImage);
            });

            let srcImage = $(this).parents('label').find('img').attr('src');
            srcImage = srcImage.replace($(this).val(), `${$(this).val()}_active`);
            $(this).parents('label').addClass('text-primary').find('img').prop('src', srcImage);

            $(`#${$(this).val()}`).slideDown('slow');
        });

        $('input[name*="AddressZipcode"]').blur(function () {
            const cep = $(this).val().replace(/\D/g, '');
            const elForm = $(this).closest('form');

            if(cep.length !== 8) {
                return false;
            }

            $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, resultado => {
                if(!resultado.erro){
                    const endereco = resultado.logradouro;
                    const bairro = resultado.bairro;
                    const estado = resultado.uf;
                    const cidade = resultado.localidade;

                    $('input[name*="AddressStreet"]', elForm).val(endereco);
                    $('input[name*="AddressNeighborhood"]', elForm).val(bairro);
                    $('input[name*="AddressCity"]', elForm).val(cidade);
                    $('input[name*="AddressState"]', elForm).val(estado);
                }
                if (resultado.erro) {
                    Toast.fire({
                        icon: 'error',
                        title: 'CEP inválido, corrija e tente novamente!'
                    });
                    return false;
                }
            });
        });

        function createSelectOptions(elem, options, labelsAndKeys = { label : "name", value : "id"}){
            const {label, value} = labelsAndKeys;

            elem.options.length = 0;

            const tempOptions = document.createDocumentFragment();

            options.forEach( option => {
                const optValue = option[value];
                const optLabel = option[label];

                const opt = document.createElement('option');
                opt.value = optValue;
                opt.textContent = optLabel;

                tempOptions.appendChild(opt);
            });

            elem.appendChild(tempOptions);
        }

        // Get Identification Types
        (async function getIdentificationTypes () {
            try {
                const identificationTypes = await mp.getIdentificationTypes();

                const billterDocTypeElement = document.getElementById('form-checkout__billetIdentificationType');
                createSelectOptions(billterDocTypeElement, identificationTypes);
                const pixDocTypeElement = document.getElementById('form-checkout__pixIdentificationType');
                createSelectOptions(pixDocTypeElement, identificationTypes);
            }catch(e) {
                return console.error('Error getting identificationTypes: ', e);
            }
        })();

        $(document).on('click', '.copy-input', function() {
            // Seleciona o conteúdo do input
            $(this).closest('.input-group').find('input').select();
            // Copia o conteudo selecionado
            const copy = document.execCommand('copy');
            if (copy) {
                $('.status_copy').addClass('text-success font-weight-bold').html("Código copiado com sucesso!")
            } else {
                $('.status_copy').addClass('text-success font-weight-bold').html("Não foi possível copiar o conteúdo!")
            }
        });

    </script>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@endsection
