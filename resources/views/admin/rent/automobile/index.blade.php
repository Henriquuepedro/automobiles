{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Listagem Automóveis para Aluguel', 'no-active' => []]])
{{-- Título da página --}}
@section('title', 'Listagem Automóveis para Aluguel')

@section('content')
    @if (session('message'))
    <div class="alert {{ session('typeMessage') === 'success' ? 'alert-success' : 'alert-warning' }}">
        <p>{{ session('message') }}</p>
    </div>
    @endif
    <div class="box">
        <div class="box-body">
            <div class="card card-default collapsed-card" id="filter_autos">
                <div class="card-header btn-title-card cursor-pointer" data-card-widget="collapse">
                    <h3 class="card-title"><i class="fa fa-search"></i> Filtre sua consulta</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row @if (count($filter['stores']) === 1) d-none @endif">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="stores">Loja</label>
                                <select class="form-control select2" id="stores" name="stores" required>
                                    @if (count($filter['stores']) > 1)
                                        <option value="0">Todas as Loja</option>
                                    @endif
                                    @foreach ($filter['stores'] as $store)
                                        <option value="{{ $store->id }}" {{ old('stores') == $store->id ? 'selected' : ''}}>{{ $store->store_fancy }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="filter_ref">Referência</label>
                            <input type="text" class="form-control" id="filter_ref"/>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="filter_license">Placa</label>
                            <input type="text" class="form-control" id="filter_license"/>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="filter_active">Ativo</label>
                            <select class="form-control select2" id="filter_active">
                                <option value="">Todos</option>
                                <option value="1" selected>Sim</option>
                                <option value="0">Não</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="filter_feature">Destaque</label>
                            <select class="form-control select2" id="filter_feature">
                                <option value="">Todos</option>
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="filter_brand">Marca do Automóvel</label>
                                <select class="form-control select2" id="filter_brand" multiple>
                                    @foreach($filter['brand'] as $brand)
                                        <option value="{{ $brand->brand_id }}">{{ $brand->brand_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-between flex-wrap">
                            <button type="button" id="clean-filter" class="btn btn-danger col-md-3"><i class="fa fa-trash"></i> Limpar Filtro</button>
                            <button type="button" id="send-filter" class="btn btn-success col-md-3"><i class="fa fa-search"></i> Aplicar Filtro</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="col-md-10 pull-left">
                        <h3 class="card-title">Listagem de Automóveis</h3><br/>
                        <small>Listagem de todos os automóveis cadastrados</small>
                    </div>
                    <div class="col-md-2 pull-right text-right text-xs-center">
                        <a href="{{ route('admin.rent.automobile.new') }}" class="btn btn-primary w-100"><i class="fa fa-plus"></i> Novo Automóvel</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover" id="dataTableList">
                        <thead>
                            <tr>
                                <th style="width: 10%">Imagem</th>
                                <th>Marca / Modelo</th>
                                <th style="width: 13%">Cor / Ano</th>
                                <th style="width: 15%">Kms</th>
                                @if (count($storesUser) > 1)<th>Loja</th>@endif
                                <th style="width: 5%">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Imagem</th>
                                <th>Marca / Modelo</th>
                                <th>Cor / Ano</th>
                                <th>Kms</th>
                                @if (count($storesUser) > 1)<th>Loja</th>@endif
                                <th>Ação</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-update-prices" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.ajax.rent.wallet.update') }}" method="POST" id="formWallet">
                    <div class="modal-header">
                        <h4 class="modal-title">Atualizar Valores</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body text-center col-md-12">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <p class="card-description"> Preencha o formulário abaixo com as informações de valores, defindo por intervalos</p>
                            </div>
                        </div>
                        <div id="new-periods" class="mt-2"></div>
                        <div class="col-md-12 text-center mt-2">
                            <button type="button" class="btn btn-primary" id="add-new-period">Adicionar Novo Período</button>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-primary col-md-5" data-dismiss="modal">Cancelar operação</button>
                        <button type="submit" class="btn btn-success col-md-5">Confirmar</button>
                    </div>
                    <input type="hidden" name="auto_id">
                </form>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script src="{{ asset('assets/admin/plugins/ion-rangeslider/js/ion.rangeSlider.min.js') }}"></script>
    <script src="https://cdn.rawgit.com/plentz/jquery-maskmoney/master/dist/jquery.maskMoney.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/additional-methods.min.js"></script>
    <script>
        let dataTableList;

        $(function () {
            $('#filter_license').mask('SSS-0AA0');
            $('#formWallet [name="day_start[]"], #formWallet [name="day_end[]"]').mask('0#');
            $('#formWallet [name="value_period[]"]').maskMoney({thousands: '.', decimal: ',', allowZero: true});

            setTimeout(() => { loadTableList(); }, 500);
        });

        $('#formWallet #add-new-period').on('click', function () {

            const verifyPeriod = verifyPeriodComplet();
            if (!verifyPeriod[0]) {
                if (verifyPeriod[2] !== undefined) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atenção',
                        html: '<ol><li>'+verifyPeriod[2].join('</li><li>')+'</li></ol>'
                    })
                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: `Finalize o cadastro do ${verifyPeriod[1]}º período, para adicionar um novo.`
                    });
                }
                return false;
            }

            let countPeriod = 0;
            countPeriod = $('.period').length + 1;

            createFormPeriod(countPeriod);
        });

        const createFormPeriod = (countPeriod, values = {}) => {
            const day_start = values.day_start ?? '';
            const day_end   = values.day_end ?? '';
            const value     = values.value ?? '';

            $('#formWallet #new-periods').append(`
                <div class="period display-none">
                    <div class="row">
                        <div class="form-group col-md-2">
                            <label>${countPeriod}º Período</label>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Alugar do dia:</label>
                            <input type="number" class="form-control" name="day_start[]" value="${day_start}" autocomplete="nope">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Até o dia:</label>
                            <input type="number" class="form-control" name="day_end[]" value="${day_end}" autocomplete="nope">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Ficará por:</label>
                            <input type="text" class="form-control" name="value_period[]" value="${value}" autocomplete="nope">
                        </div>
                        <div class="col-md-1">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-danger remove-period col-md-12 btn-flat"><i class="fa fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            `).find('.period').slideDown('slow');

            $('#formWallet [name="day_start[]"], #formWallet [name="day_end[]"]').mask('0#');
            $('#formWallet [name="value_period[]"]').maskMoney({thousands: '.', decimal: ',', allowZero: true});
        }

        $(document).on('click', '#formWallet .remove-period', function (){
            $(this).closest('.period').slideUp(500);
            setTimeout(() => { $(this).closest('.period').remove() }, 500);
        });

        $('#clean-filter').click(function (){
            $('#filter_ref, #filter_license, #filter_active, #filter_feature, #filter_brand').val('');
            $('#filter_autos .select2').select2();
            setTimeout(() => {
                disabledBtnFilter();
                loadTableList();
            }, 500);
        });

        $('#send-filter').click(function (){
            disabledBtnFilter();
            loadTableList();
        });

        $('#filter_autos').on('expanded.lte.cardwidget', function(){
            $('#filter_autos .select2').select2();
        });

        $(document).on('click', '.updatePrices', function(){
            const auto_id = $(this).data('auto-id');

            $('#modal-update-prices').modal().find('input[name="auto_id"]').val(auto_id);
            $('#formWallet #new-periods').empty().html(`<h3><i class="fa fa-spin fa-spinner"></i> Carregando</h3>`);
            $('#add-new-period, #formWallet button[type="submit"]').attr('disabled', true);

            $.get(`${window.location.origin}/admin/ajax/aluguel/valores/${auto_id}`, response => {
                $('#formWallet #new-periods').empty();

                $.each(response, function (key, val) {
                    createFormPeriod((key + 1), val);
                });

                $('#add-new-period, #formWallet button[type="submit"]').attr('disabled', false);

            }, 'JSON').fail(function(e) {
                console.log(e);
            });
        });

        // validate the form when it is submitted
        $("#formWallet").validate({
            invalidHandler: function(event, validator) {
                let arrErrors = [];
                $.each(validator.errorMap, function (key, val) {
                    arrErrors.push(val);
                });
                setTimeout(() => {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atenção',
                        html: '<ol><li>'+arrErrors.join('</li><li>')+'</li></ol>'
                    });
                }, 500);
            },
            submitHandler: function(form) {
                let verifyPeriod = verifyPeriodComplet();
                if (!verifyPeriod[0]) {

                    if (verifyPeriod[2] !== undefined) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Atenção',
                            html: '<ol><li>'+verifyPeriod[2].join('</li><li>')+'</li></ol>'
                        })
                    } else {
                        Toast.fire({
                            icon: 'warning',
                            title: `Finalize o cadastro do ${verifyPeriod[1]}º período, para adicionar um novo.`
                        });
                    }
                    return false;
                }

                $('#formWallet [type="submit"]').attr('disabled', true);
                formWalletSubmit();
            }
        });

        const formWalletSubmit = () => {
            const getForm = $('#formWallet');
            const formData = new FormData(getForm[0]);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: getForm.attr('method'),
                url: getForm.attr('action'),
                data: formData,
                dataType: 'json',
                enctype: 'multipart/form-data',
                processData:false,
                contentType:false,
                success: response => {
                    console.log(response);

                    if (!response.success) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Atenção',
                            html: `<p>${response.message}</p>`
                        });
                        return false;
                    }

                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });
                }, error: e => {
                    console.log(e);

                    let arrErrors = []

                    $.each(e.responseJSON.errors, function( index, value ) {
                        arrErrors.push(value);
                    });

                    if (!arrErrors.length && e.responseJSON.message !== undefined) {
                        arrErrors.push('Não foi possível identificar o motivo do erro, recarregue a página e tente novamente!');
                    }

                    Swal.fire({
                        icon: 'warning',
                        title: 'Atenção',
                        html: '<ol><li>'+arrErrors.join('</li><li>')+'</li></ol>'
                    });
                }, complete: () => {
                    getForm.find('button[type="submit"]').attr('disabled', false);
                }
            });
        }

        const verifyPeriodComplet = () => {
            cleanBorderPeriod();

            const periodCount = $('.period').length;
            let arrErrors = [];
            let day_start = 0;
            let day_end = 0;
            let value = 0;
            let periodUser = 0;
            let _verifyValuesOutRange;
            let arrDaysVerify = [];
            for (let countPeriod = 0; countPeriod < periodCount; countPeriod++) {
                periodUser++;
                day_start   = $(`#formWallet [name="day_start[]"]:eq(${countPeriod})`);
                day_end     = $(`#formWallet [name="day_end[]"]:eq(${countPeriod})`);
                value       = $(`#formWallet [name="value_period[]"]:eq(${countPeriod})`);
                if (!day_start.val().length) {
                    day_start.css('border', '1px solid red');
                    arrErrors.push('Dia inicial do período precisa ser preenchido.');
                }
                if (!day_end.val().length) {
                    day_end.css('border', '1px solid red');
                    arrErrors.push('Dia final do período precisa ser preenchido.');
                }
                if (!value.val().length) {
                    value.css('border', '1px solid red');
                    arrErrors.push('Valor do período precisa ser preenchido.');
                }
                if (parseInt(day_start.val()) > parseInt(day_end.val())) {
                    day_start.css('border', '1px solid red');
                    day_end.css('border', '1px solid red');
                    arrErrors.push('Dia inicial do período não pode ser maior que a final.');
                }
                _verifyValuesOutRange = verifyValuesOutRange(parseInt(day_start.val()), parseInt(day_end.val()), arrDaysVerify);
                if (_verifyValuesOutRange[0]) {
                    day_start.css('border', '1px solid red');
                    day_end.css('border', '1px solid red');
                    arrErrors.push(`Existem erros no período. O ${periodUser}º período está inválido, já existe algum dia em outro perído.`);
                }
                arrDaysVerify = _verifyValuesOutRange[1];
                if (arrErrors.length) return [false, (countPeriod + 1), arrErrors];
            }
            return [true];
        }

        const verifyValuesOutRange = (day_start, day_end, arrDaysVerify) => {
            for (let countPer = day_start; countPer <= day_end; countPer++) {
                if (inArray(countPer, arrDaysVerify)) return [true, arrDaysVerify];
                arrDaysVerify.push(countPer);
            }
            return [false, arrDaysVerify];
        }

        const cleanBorderPeriod = () => {
            $('#formWallet [name="day_start[]"]').removeAttr('style');
            $('#formWallet [name="day_end[]"]').removeAttr('style');
            $('#formWallet [name="value_period[]"]').removeAttr('style');
        }

        const loadTableList = () => {
            const filter_store      = parseInt($('#stores').val()) === 0 ? null : parseInt($('#stores').val());
            const filter_ref        = $('#filter_ref').val();
            const filter_license    = $('#filter_license').val();
            const filter_active     = $('#filter_active').val();
            const filter_feature    = $('#filter_feature').val();
            const filter_brand      = $('#filter_brand').val();

            dataTableList = getTableList(
                'ajax/aluguel/automovel/buscar',
                {
                    filter_store,
                    filter_ref,
                    filter_license,
                    filter_active,
                    filter_feature,
                    filter_brand
                },
                'dataTableList',
                false,
                [0,'desc'],
                'POST',
                () => {
                    enabledBtnFilter(false);
                    $('[data-toggle="tooltip"]').tooltip();
                },
                function( settings, json ) {},
                row => {
                    const pos = $('#dataTableList thead th').length - 1;
                    $(row).find(`td:eq(${pos})`).addClass('flex-nowrap d-flex justify-content-between');
                    $(row).find(`td:eq(${pos}) button:last`).addClass('ml-1');
                }
            );
        }

        const enabledBtnFilter = () => {
            $('#filter_autos .card-footer button').prop('disabled', false)
        }

        const disabledBtnFilter = () => {
            $('#filter_autos .card-footer button').prop('disabled', true)
        }
    </script>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/ion-rangeslider/css/ion.rangeSlider.min.css') }}">
    <style>
        #new-periods .period .col-md-3,
        #new-periods .period .col-md-2,
        #new-periods .period .col-md-1 {
            padding-left: 0;
            padding-right: 0;
        }
        #new-periods .period .form-group input.form-control {
            border-radius: 0;
        }
    </style>
@endsection
