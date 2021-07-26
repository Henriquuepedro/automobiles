{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => true, 'active' => 'Configurar Página Inicial', 'no-active' => []]])
{{-- Título da página --}}
@section('title', 'Início')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Parametrização</h4>
                    <br/>
                    <small>Arraste e solte para priorizar a ordem de como serão mostrados as informações. Arraste para inativo ou ativo para realizar a ação.</small>
                </div>
                <div class="card-body">
                    <div class="row @if(count($stores) === 1) d-none @endif">
                        <div class="col-md-12 form-group">
                            <label for="autos">Loja</label>
                            <select class="form-control select2" id="stores" name="stores" required>
                                @if(count($stores) > 1)
                                    <option value="0">Selecione uma Loja</option>
                                @endif
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->store_fancy }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 d-flex no-padding">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-danger">
                                        <h4 class="card-title"><i class="fa fa-times"></i> Inativos</h4>
                                    </div>
                                    <div class="column card-body order-inactived">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-success">
                                        <h4 class="card-title"><i class="fa fa-check"></i> Ativos</h4>
                                    </div>
                                    <div class="column card-body order-actived">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between flex-wrap">
                    <a href="" class="btn btn-danger"><i class="fa fa-times"></i> Descartar Alterações</a>
                    <button class="btn btn-success" id="btnSaveOrderPages"><i class="fa fa-save"></i> Salvar Alterações</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .portlet {
            margin: 5px 5px 5px 0;
            cursor: move;
        }
        .portlet-header {
            padding: 5px 0 5px 15px;
            border: 1px solid #ccc;
            background-color: #fff;
            color: #000;
        }
        .portlet-placeholder {
            border: 1px dotted black;
            margin: 5px 5px 5px 0;
            height: 30px;
        }
    </style>
@stop

@section('js')
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(function() {
            $( ".column" ).sortable({
                connectWith: ".column",
                handle: ".portlet-header",
                placeholder: "portlet-placeholder ui-corner-all"
            });

            $( ".portlet" )
                .addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
                .find( ".portlet-header" )
                .addClass( "ui-widget-header ui-corner-all" );

            $('.select2').select2();

            $('#stores').trigger('change');
        });


        $('#stores').on('change', function (){
            const store = $(this).val();
            $.ajax({
                url: `${window.location.origin}/admin/ajax/paginaInicial/buscar/${store}`,
                type: 'get',
                success: response => {

                    console.log(response);

                    const bodyInactive = $('.order-inactived');
                    const bodyActive = $('.order-actived');

                    bodyInactive.empty();
                    bodyActive.empty();

                    $(response.inactives).each(function (key, value) {
                        bodyInactive.append(`
                            <div class="portlet">
                                <div class="portlet-header" order-id="${value.order}">${value.name}</div>
                            </div>
                        `);
                    });

                    $(response.actives).each(function (key, value) {
                        bodyActive.append(`
                            <div class="portlet" order-id="${value.order}">
                                <div class="portlet-header">${value.name}</div>
                            </div>
                        `);
                    });

                }, error: e => {
                    console.log(e)
                }
            });
        })

        $('#btnSaveOrderPages').click(async function (){
            const btn = $(this);
            btn.attr('disabled', true);
            let orderIds = [];
            await $('.order-actived [order-id]').each(function (){
                orderIds.push(parseInt($(this).attr('order-id')));
            });
            $.ajax({
                url: "{{ route('ajax.homePage.updateOrder') }}",
                type: 'put',
                data: { orderIds, stores: $('#stores').val() },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: response => {

                    console.log(response);

                    Toast.fire({
                        icon: response.success ? 'success' : 'error',
                        title: response.message
                    });
                }, error: e => {
                    console.log(e)
                }, complete: () => {
                    btn.attr('disabled', false);
                }
            });
        });

    </script>
@stop
@section('css_pre')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
