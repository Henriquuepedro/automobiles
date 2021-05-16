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
                    <div class="row">
                        <div class="col-md-12 d-flex no-padding">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-danger">
                                        <h4 class="card-title"><i class="fa fa-times"></i> Inativos</h4>
                                    </div>
                                    <div class="column card-body">
                                        @foreach($controlPages as $control)
                                            @if($control['order'] === null)
                                                <div class="portlet">
                                                    <div class="portlet-header" order-id="{{ $control['id'] }}">{{ $control['nome'] }}</div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-success">
                                        <h4 class="card-title"><i class="fa fa-check"></i> Ativos</h4>
                                    </div>
                                    <div class="column card-body order-actived">
                                        @foreach($controlPages as $control)
                                            @if($control['order'] !== null)
                                                <div class="portlet" order-id="{{ $control['id'] }}">
                                                    <div class="portlet-header">{{ $control['nome'] }}</div>
                                                </div>
                                            @endif
                                        @endforeach
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
                .addClass( "ui-widget-header ui-corner-all" )

        });

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
                data: { orderIds },
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
        })

    </script>
@stop
