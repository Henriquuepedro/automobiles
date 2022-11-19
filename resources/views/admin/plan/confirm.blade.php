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
                        <h3 class="font-weight-bold text-primary">Plano {{ $checkout->namePlan }}</h3>
                    </div>
                    <div class="row mb-5 d-flex justify-content-center">
                        <div class="col-md-4 text-center">
                            <label class="cursor-pointer">
                                <img src="{{ asset('assets/admin/dist/images/system/credit_card.png') }}" alt="Cartão de Crédito" width="60">
                                <h5 class="font-weight-bold">Cartão de Crédito</h5>
                                <input type="radio" name="type_payment" value="credit_card" class="icheck d-none"/>
                            </label>
                        </div>
                        <div class="col-md-4 text-center">
                            <label class="cursor-pointer">
                                <img src="{{ asset('assets/admin/dist/images/system/pix.png') }}" alt="PIX" width="60">
                                <h5 class="font-weight-bold">Pix</h5>
                                <input type="radio" name="type_payment" value="pix" class="icheck d-none"/>
                            </label>
                        </div>
                        <div class="col-md-4 text-center">
                            <label class="cursor-pointer">
                                <img src="{{ asset('assets/admin/dist/images/system/billet.png') }}" alt="Boleto" width="60">
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
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="form-checkout__cardBand">
                                                    <i class="fas fa-money-check"></i>
                                                </span>
                                            </div>
                                            <input type="text" name="cardNumber" id="form-checkout__cardNumber" class="form-control" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form-checkout__cardExpirationDate">Data de Expiração (DD/AAAA)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </span>
                                            </div>
                                            <input type="text" name="cardExpirationDate" id="form-checkout__cardExpirationDate" class="form-control" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form-checkout__cardSecurityCode">Código de Segurança</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-lock"></i>
                                                </span>
                                            </div>
                                            <input type="password" maxlength="3" name="securityCode" id="form-checkout__cardSecurityCode" class="form-control" required />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="form-checkout__cardholderName">Nome do Titular</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-user-tag"></i>
                                                </span>
                                            </div>
                                            <input type="text" name="cardholderName" id="form-checkout__cardholderName" class="form-control" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="form-checkout__cardholderEmail">Email do Titular</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-envelope"></i>
                                                </span>
                                            </div>
                                            <input type="email" name="cardholderEmail" id="form-checkout__cardholderEmail" class="form-control" required />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 d-none">
                                    <div class="form-group">
                                        <label for="form-checkout__issuer">Emissora</label>
                                        <select name="issuer" id="form-checkout__issuer" class="form-control" readonly=""></select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="form-checkout__cardIdentificationType">Tipo de Documento</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-id-card-alt"></i>
                                                </span>
                                            </div>
                                            <select name="identificationType" id="form-checkout__cardIdentificationType" class="form-control" required></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="form-checkout__cardIdentificationNumber">Documento</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-id-card"></i>
                                                </span>
                                            </div>
                                            <input type="text" name="identificationNumber" id="form-checkout__cardIdentificationNumber" class="form-control" required />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="form-checkout__cardIstallments">Parcelas</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-money-check-alt"></i>
                                                </span>
                                            </div>
                                            <select name="installments" id="form-checkout__cardIstallments" class="form-control" required></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group d-flex justify-content-between">
                                        <a href="{{ route('admin.plan.index') }}" class="btn btn-danger col-md-3">Voltar Para Planos</a>
                                        <button type="submit" id="form-checkout__cardSubmit" class="btn btn-success col-md-3">Realizar Pagamento</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col-md-12 d-flex justify-content-center">
                            <img src="https://imgmp.mlstatic.com/org-img/MLB/MP/BANNERS/tipo2_735X40.jpg?v=1"
                                 alt="Mercado Pago - Meios de pagamento" title="Mercado Pago - Meios de pagamento"
                                 width="735" height="40"/>
                        </div>
                        <div class="col-md-12 d-flex justify-content-center">
                            <a href="https://www.mercadopago.com.br/ajuda/Custos-de-parcelamento_322" target="_blank">Veja os juros de parcelamentos!</a>
                        </div>
                    </div>
                    <div class="row" id="pix" style="display: none">
                        <form enctype="multipart/form-data" id="pix-form-checkout" method="POST" class="col-md-12">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label for="form-checkout__pixFirstname">Nome</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="form-checkout__cardBand">
                                                    <i class="fas fa-user-tag"></i>
                                                </span>
                                            </div>
                                            <input type="text" name="pixFirstname" id="form-checkout__pixFirstname" class="form-control" placeholder="Nome do Titular" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="form-checkout__pixLastname">Sobrenome</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="form-checkout__cardBand">
                                                    <i class="fas fa-user-plus"></i>
                                                </span>
                                            </div>
                                            <input type="text" name="pixLastname" id="form-checkout__pixLastname" class="form-control" placeholder="Sobrenome do Titular" required />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="form-checkout__pixEmail">Email do Titular</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-envelope"></i>
                                                </span>
                                            </div>
                                            <input type="email" name="pixEmail" id="form-checkout__pixEmail" class="form-control" placeholder="E-mail do Titular" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form-checkout__pixIdentificationType">Tipo de Documento</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-id-card-alt"></i>
                                                </span>
                                            </div>
                                            <select name="pixIdentificationType" id="form-checkout__pixIdentificationType" class="form-control" required></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="form-checkout__pixIdentificationNumber">Documento</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-id-card"></i>
                                                </span>
                                            </div>
                                            <input type="text" name="pixIdentificationNumber" id="form-checkout__pixIdentificationNumber" placeholder="Documento CPF/CNPJ" class="form-control" required />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form-checkout__pixAddressZipcode">CEP</label>
                                        <input type="text" name="pixAddressZipcode" id="form-checkout__pixAddressZipcode" class="form-control" placeholder="CEP do Endereço" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="form-checkout__pixAddressStreet">Endereço</label>
                                        <input type="text" name="pixAddressStreet" id="form-checkout__pixAddressStreet" class="form-control" placeholder="Nome do Endereço" required />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form-checkout__pixAddressNumber">Número</label>
                                        <input type="text" name="pixAddressNumber" id="form-checkout__pixAddressNumber" class="form-control" placeholder="Número do Endereço" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form-checkout__pixAddressNeighborhood">Bairro</label>
                                        <input type="text" name="pixAddressNeighborhood" id="form-checkout__pixAddressNeighborhood" placeholder="Bairro do Endereço" class="form-control" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="form-checkout__pixAddressCity">Cidade</label>
                                        <input type="text" name="pixAddressCity" id="form-checkout__pixAddressCity" class="form-control" placeholder="Cidade do Endereço" required />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form-checkout__pixAddressState">UF</label>
                                        <input type="text" name="pixAddressState" id="form-checkout__pixAddressState" class="form-control" placeholder="UF do Endereço" required maxlength="2" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group d-flex justify-content-between">
                                        <a href="{{ route('admin.plan.index') }}" class="btn btn-danger col-md-3">Voltar Para Planos</a>
                                        <button type="submit" id="form-checkout__pixSubmit" class="btn btn-success col-md-3">Realizar Pagamento</button>
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
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="form-checkout__cardBand">
                                                    <i class="fas fa-user-tag"></i>
                                                </span>
                                            </div>
                                            <input type="text" name="billetFirstname" id="form-checkout__billetFirstname" class="form-control" placeholder="Nome do titular" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="form-checkout__billetLastname">Sobrenome</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="form-checkout__cardBand">
                                                    <i class="fas fa-user-plus"></i>
                                                </span>
                                            </div>
                                            <input type="text" name="billetLastname" id="form-checkout__billetLastname" class="form-control" placeholder="Sobrenome do titular" required />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="form-checkout__billetEmail">Email do Titular</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-envelope"></i>
                                                </span>
                                            </div>
                                            <input type="email" name="billetEmail" id="form-checkout__billetEmail" class="form-control" placeholder="E-mail do titular" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form-checkout__billetIdentificationType">Tipo de Documento</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-id-card-alt"></i>
                                                </span>
                                            </div>
                                            <select name="billetIdentificationType" id="form-checkout__billetIdentificationType" class="form-control" required></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="form-checkout__billetIdentificationNumber">Documento</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-id-card"></i>
                                                </span>
                                            </div>
                                            <input type="text" name="billetIdentificationNumber" id="form-checkout__billetIdentificationNumber" placeholder="Documento CPF/CNPJ" class="form-control" required />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form-checkout__billetAddressZipcode">CEP</label>
                                        <input type="text" name="billetAddressZipcode" id="form-checkout__billetAddressZipcode" class="form-control" placeholder="CEP do Endereço" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="form-checkout__billetAddressStreet">Endereço</label>
                                        <input type="text" name="billetAddressStreet" id="form-checkout__billetAddressStreet" class="form-control" placeholder="Nome do Endereço" required />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form-checkout__billetAddressNumber">Número</label>
                                        <input type="text" name="billetAddressNumber" id="form-checkout__billetAddressNumber" class="form-control" placeholder="Número do Endereço" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form-checkout__billetAddressNeighborhood">Bairro</label>
                                        <input type="text" name="billetAddressNeighborhood" id="form-checkout__billetAddressNeighborhood" class="form-control" placeholder="Bairro do Endereço" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="form-checkout__billetAddressCity">Cidade</label>
                                        <input type="text" name="billetAddressCity" id="form-checkout__billetAddressCity" class="form-control" placeholder="Cidade do Endereço" required />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form-checkout__billetAddressState">UF</label>
                                        <input type="text" name="billetAddressState" id="form-checkout__billetAddressState" class="form-control" placeholder="UF do Endereço" required maxlength="2" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group d-flex justify-content-between">
                                        <a href="{{ route('admin.plan.index') }}" class="btn btn-danger col-md-3">Voltar Para Planos</a>
                                        <button type="submit" id="form-checkout__billetSubmit" class="btn btn-success col-md-3">Realizar Pagamento</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <input type="hidden" name="amount" value="{{ $checkout->amount }}">
                    <input type="hidden" name="plan" value="{{ $checkout->plan }}">
                    <input type="hidden" name="idPlan" value="{{ $checkout->idPlan }}">
                    <input type="hidden" name="typePlan" value="{{ $checkout->typePlan }}">
                    <input type="hidden" name="namePlan" value="{{ $checkout->namePlan }}">
                    <input type="hidden" name="mp_public_key" value="{{ $checkout->public_key }}">
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script src="https://sdk.mercadopago.com/js/v2"></script>
    <script>
        $(function (){
            $('form-checkout__cardSecurityCode').mask('0000');
            $('input[name*="AddressZipcode"]').mask('00000-000');
            $('#form-checkout__cardNumber').mask('0000 0000 0000 0000');
            $('#form-checkout__cardExpirationDate').mask('00/0000');
            $('select[id*="IdentificationType"]').mask('000.000.000-00');
        });

        $('select[id*="IdentificationType"]').change(function (){
            const form = $(this).parents('form');
            if ($(this).val() === 'CNPJ') {
                $('input[id*=IdentificationNumber]', form).mask('00.000.000/0000-00');
            } else {
                $('input[id*=IdentificationNumber]', form).mask('000.000.000-00');
            }
        });

        $('#billet-form-checkout').on('submit', function(e) {
            e.preventDefault();

            loadForm(true, 'billet');

            if (!checkPayment('billet')) {
                loadForm(false, 'billet');
                return false;
            }

            const data = {
                transaction_amount: Number($('[name="amount"]').val()),
                description: "Plano Mensal: " + $('[name="namePlan"]').val(),
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
                url: window.location.origin+"/admin/planos/confirmar/"+$('[name="typePlan"]').val()+"/"+$('[name="idPlan"]').val(),
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
                            html: '<h4>Pagamento não enviado</h4>' + response.message
                        });
                        loadForm(false, 'billet');
                    }
                }, error: e => {
                    getErrorMessage(e.responseJSON);
                    loadForm(false, 'billet');
                }
            });
        });

        $('#pix-form-checkout').on('submit', function(e) {
            e.preventDefault();

            loadForm(true, 'pix');

            if (!checkPayment('pix')) {
                loadForm(false, 'pix');
                return false;
            }

            const data = {
                transaction_amount: Number($('[name="amount"]').val()),
                description: "Plano Mensal: " + $('[name="namePlan"]').val(),
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
                url: window.location.origin+"/admin/planos/confirmar/"+$('[name="typePlan"]').val()+"/"+$('[name="idPlan"]').val(),
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
                            html: '<h4>Pagamento não enviado</h4>' + response.message
                        });
                        loadForm(false, 'pix');
                    }
                }, error: e => {
                    getErrorMessage(e.responseJSON);
                    loadForm(false, 'pix');
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

        $('#form-checkout__cardNumber').on('change', async function(){
            const response = await mp.getPaymentMethods({ bin: onlyNumbers($('#form-checkout__cardNumber').val()) });
            const paymentMethods = response.results;

            console.log(paymentMethods);

            if (paymentMethods.length && paymentMethods.length === 1) {
                const dataCard = paymentMethods[0];
                $('#form-checkout__cardBand').empty().append(`<img src="${dataCard.thumbnail}" alt="${dataCard.name}">`);
                $('#MPHiddenInputPaymentMethod').val(dataCard.id);
            } else {
                $('#form-checkout__cardBand').empty().append('<i class="fas fa-money-check"></i>');
                $('#MPHiddenInputPaymentMethod').val('');
            }

        });

        const mp = new MercadoPago($('[name="mp_public_key"]').val(), {
            locale: 'pt-BR'
        });

        const cardForm = mp.cardForm({
            amount: $('[name="amount"]').val(),
            autoMount: true,
            form: {
                id: "card-form-checkout",
                cardholderName: {
                    id: "form-checkout__cardholderName",
                    placeholder: "Titular do Cartão",
                },
                cardholderEmail: {
                    id: "form-checkout__cardholderEmail",
                    placeholder: "E-mail do Titular",
                },
                cardNumber: {
                    id: "form-checkout__cardNumber",
                    placeholder: "Número do Cartão",
                },
                cardExpirationDate: {
                    id: "form-checkout__cardExpirationDate",
                    placeholder: "Vencimento (MM/AAAA)",
                },
                securityCode: {
                    id: "form-checkout__cardSecurityCode",
                    placeholder: "Código de Segurança",
                },
                installments: {
                    id: "form-checkout__cardIstallments",
                    placeholder: "Informe o número do cartão",
                },
                identificationType: {
                    id: "form-checkout__cardIdentificationType",
                    placeholder: "Tipo de Documento",
                },
                identificationNumber: {
                    id: "form-checkout__cardIdentificationNumber",
                    placeholder: "Número do documento do titular",
                },
                issuer: {
                    id: "form-checkout__issuer",
                    placeholder: "Banco emissor",
                },
            },
            callbacks: {
                onFormMounted: error => {
                    if (error) {
                        getErrorMessage(error);
                        return console.warn("Form Mounted handling error: ", error);
                    }
                },
                onSubmit: event => {
                    event.preventDefault();

                    loadForm(true, 'card');

                    if (!checkPayment('card')) {
                        loadForm(false, 'card');
                        return false;
                    }

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

                    fetch(window.location.origin+"/admin/planos/confirmar/"+$('[name="typePlan"]').val()+"/"+$('[name="idPlan"]').val(), {
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
                            description: "Plano Mensal: " + $('[name="namePlan"]').val(),
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
                            console.log(response);

                            loadForm(false, 'card');

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
                                    html: '<h4>Pagamento não enviado</h4>' + response.message
                                });
                            }
                        })
                        .catch(err => {
                            console.log(err);
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                html: '<h4>Pagamento não enviado</h4>' + err.message
                            });
                            loadForm(false, 'card');
                        });
                },
                onFetching: (resource) => {
                    console.log(resource);
                    if (resource === 'cardToken') {
                        loadForm(true, 'card');
                    }
                },
                onFormUnmounted: error => {
                    if (error) {
                        getErrorMessage(error);
                        loadForm(false, 'card');
                        return console.warn('Form Unmounted handling error: ', error)
                    }
                    console.log('Form unmounted')
                },
                onIdentificationTypesReceived: (error, identificationTypes) => {
                    if (error) {
                        getErrorMessage(error);
                        loadForm(false, 'card');
                        return console.warn('identificationTypes handling error: ', error)
                    }

                    console.log('Identification types available: ', identificationTypes)
                },
                onPaymentMethodsReceived: (error, paymentMethods) => {
                    if (error) {
                        // getErrorMessage(error);
                        loadForm(false, 'card');
                        return console.warn('paymentMethods handling error: ', error)
                    }

                    if (paymentMethods.length && paymentMethods.length === 1) {
                        const dataCard = paymentMethods[0];
                        $('#form-checkout__cardBand').empty().append(`<img src="${dataCard.thumbnail}" alt="${dataCard.name}">`);
                        $('#MPHiddenInputPaymentMethod').val(dataCard.id);
                    } else {
                        $('#form-checkout__cardBand').empty().append('<i class="fas fa-money-check"></i>');
                        $('#MPHiddenInputPaymentMethod').val('');
                    }

                    console.log('Payment Methods available: ', paymentMethods)
                },
                onIssuersReceived: (error, issuers) => {
                    if (error) {
                        getErrorMessage(error);
                        loadForm(false, 'card');
                        return console.warn('issuers handling error: ', error)
                    }

                    console.log('Issuers available: ', issuers)
                },
                onInstallmentsReceived: (error, installments) => {
                    if (error) {
                        getErrorMessage(error);
                        loadForm(false, 'card');
                        return console.warn('installments handling error: ', error)
                    }

                    console.log('Installments available: ', installments)
                },
                onCardTokenReceived: (error, token) => {
                    if (error) {
                        getErrorMessage(error);
                        loadForm(false, 'card');
                        return console.warn('Token handling error: ', error)
                    }

                    console.log('Token available: ', token);
                }
            },
        });

        (async function getIdentificationTypes () {
            try {
                const identificationTypes = await mp.getIdentificationTypes();

                const billterDocTypeElement = document.getElementById('form-checkout__billetIdentificationType');
                createSelectOptions(billterDocTypeElement, identificationTypes);
                const pixDocTypeElement = document.getElementById('form-checkout__pixIdentificationType');
                createSelectOptions(pixDocTypeElement, identificationTypes);
            } catch(e) {
                Toast.fire({
                    icon: 'error',
                    title: e
                });
                //return console.error('Error getting identificationTypes: ', e);
            }
        })();

        const createSelectOptions = (elem, options, labelsAndKeys = { label : "name", value : "id"}) => {
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

        /**
         * Bloqueia e desbloqueia os campos.
         *
         * @param   {boolean}   block  Precisa bloquear os campos?
         * @param   {string}    type   Tipo de pagamento.
         */
        const loadForm = (block, type) => {
            if (block) {
                $('#form-checkout__cardNumber, #form-checkout__cardIdentificationNumber').unmask();
            } else {
                $('#form-checkout__cardNumber').mask('0000 0000 0000 0000');
                $('#form-checkout__cardIdentificationType').trigger('change');
            }

            $(`
                input[id*="form-checkout__${type}"],
                select[id*="form-checkout__${type}"],
                button[id*="form-checkout__${type}"]
            `).prop('disabled', block);
        }

        /**
         * Returns erro message from MP.
         *
         * @param   {object|array}  error   Data with error.
         * @return  {string}                Message error.
         */
        const getErrorMessage = error => {
            let message = [];

            if (Array.isArray(error)) {
                $(error).each(function(k,v ) {
                    message.push(v.message ?? '');
                });
            } else {
                message.push(error.message ?? '');
            }

            Swal.fire({
                icon: 'error',
                title: 'Pagamento não enviado',
                html: message.join('<br/>')
            });
        }

        /**
         * Valida campos de pagamento.
         *
         * @param   {string}    type    Tipo de pagamento.
         * @return  {bool}              Situação da validação. TRUE = Válido para seguir.
         */
        const checkPayment = type => {
            let error = [];
            if (type === 'card') {
                if (!checkDocument($('#form-checkout__cardIdentificationNumber').val())) {
                    error.push($('#form-checkout__cardIdentificationType').val() + ' inválido.');
                }
                if (onlyNumbers($('#form-checkout__cardNumber').val()).length !== 16) {
                    error.push('Número do Cartão informado está inválido.');
                }
                if ($('#form-checkout__cardExpirationDate').val().length !== 7) {
                    error.push('Data de valida informada está inválido. Informe DD/YYYY.');
                }
                if ($('#form-checkout__cardSecurityCode').val() === '') {
                    error.push('E-mail informado está inválido.');
                }
                if ($('#form-checkout__cardholderEmail').val() === '') {
                    error.push('E-mail informado está inválido.');
                }
            } else if (type === 'billet') {
                if (!checkDocument($('#form-checkout__billetIdentificationNumber').val())) {
                    error.push($('#form-checkout__billetIdentificationType').val() + ' inválido.');
                }
                if (onlyNumbers($('#form-checkout__billetAddressZipcode').val()).length !== 8) {
                    error.push('CEP informado está inválido.');
                }
                if ($('#form-checkout__billetFirstname').val() === '') {
                    error.push('Nome informado está inválido.');
                }
                if ($('#form-checkout__billetLastname').val() === '') {
                    error.push('Sobrenome informado está inválido.');
                }
                if ($('#form-checkout__billetEmail').val() === '') {
                    error.push('E-mail informado está inválido.');
                }
                if ($('#form-checkout__billetAddressStreet').val() === '') {
                    error.push('Endereço informado está inválido.');
                }
                if ($('#form-checkout__billetAddressNumber').val() === '') {
                    error.push('Número do endereço informado está inválido.');
                }
                if ($('#form-checkout__billetAddressNeighborhood').val() === '') {
                    error.push('Bairro informado está inválido.');
                }
                if ($('#form-checkout__billetAddressCity').val() === '') {
                    error.push('Cidade informada está inválido.');
                }
                if ($('#form-checkout__billetAddressState').val() === '') {
                    error.push('UF informado está inválido.');
                }
            } else if (type === 'pix') {
                if (!checkDocument($('#form-checkout__pixIdentificationNumber').val())) {
                    error.push($('#form-checkout__pixIdentificationType').val() + ' inválido.');
                }
                if (onlyNumbers($('#form-checkout__pixAddressZipcode').val()).length !== 8) {
                    error.push('CEP informado está inválido.');
                }
                if ($('#form-checkout__pixFirstname').val() === '') {
                    error.push('Nome informado está inválido.');
                }
                if ($('#form-checkout__pixLastname').val() === '') {
                    error.push('Sobrenome informado está inválido.');
                }
                if ($('#form-checkout__pixEmail').val() === '') {
                    error.push('E-mail informado está inválido.');
                }
                if ($('#form-checkout__pixAddressStreet').val() === '') {
                    error.push('Endereço informado está inválido.');
                }
                if ($('#form-checkout__pixAddressNumber').val() === '') {
                    error.push('Número do endereço informado está inválido.');
                }
                if ($('#form-checkout__pixAddressNeighborhood').val() === '') {
                    error.push('Bairro informado está inválido.');
                }
                if ($('#form-checkout__pixAddressCity').val() === '') {
                    error.push('Cidade informada está inválido.');
                }
                if ($('#form-checkout__pixAddressState').val() === '') {
                    error.push('UF informado está inválido.');
                }
            }

            if (error.length) {
                Swal.fire({
                    icon: 'error',
                    title: 'Pagamento não enviado',
                    html: error.join('<br/>')
                });
                return false;
            }

            return true;
        }

    </script>
@endsection
@section('css')
@endsection
