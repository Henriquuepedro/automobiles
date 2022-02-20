{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Listagem Automóveis', 'no-active' => []]])
{{-- Título da página --}}
@section('title', 'Listagem Automóveis')

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
                                <label for="autos">Loja</label>
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
                        <div class="col-md-7">
                            <div class="form-group">
                                <label for="filter_brand">Valor do Automóvel</label>
                                <div class="col-md-12">
                                    <input id="rangePrice" type="text" name="filter_price" value="">
                                </div>
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
                        <a href="{{ route('admin.automobiles.cadastro') }}" class="btn btn-primary">Novo Automóvel</a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover" id="dataTableList">
                        <thead>
                            <tr>
                                <th style="width: 10%">Imagem</th>
                                <th>Marca / Modelo</th>
                                <th style="width: 13%">Cor / Ano</th>
                                <th style="width: 15%">Valor / Kms</th>
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
                                <th>Valor / Kms</th>
                                @if (count($storesUser) > 1)<th>Loja</th>@endif
                                <th>Ação</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
    <input type="hidden" value="{{ $filter['price']['max_price'] }}" id="filter_max_price">
    <input type="hidden" value="{{ $filter['price']['min_price'] }}" id="filter_min_price">
@stop
@section('js')
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
    <script src="//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/ion-rangeslider/js/ion.rangeSlider.min.js') }}"></script>
    <script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js"></script>
    <script>
        let instanceRange;
        let dataTableList;

        $(function () {
            instanceRange = $('#rangePrice').ionRangeSlider({
                min     : $('#filter_min_price').val(),
                max     : $('#filter_max_price').val(),
                type    : 'double',
                step    : 250,
                prefix  : 'R$',
                prettify: true,
                hasGrid : true,
                grid    : true,
                skin    : 'round'
            }).data("ionRangeSlider");

            $('#filter_license').mask('SSS-0AA0');

            setTimeout(() => { loadTableList(); }, 500);
        });

        $('#filter_autos').on('expanded.lte.cardwidget', function(){
            $('#filter_autos .select2').select2();
        });

        $('#clean-filter').click(function (){
            $('#filter_ref, #filter_license, #filter_active, #filter_feature, #filter_brand').val('');
            $('#filter_autos .select2').select2();
            instanceRange.update({
                from: $('#filter_min_price').val(),
                to: $('#filter_max_price').val()
            });
            setTimeout(() => {
                disabledBtnFilter();
                loadTableList();
            }, 500);
        });

        $('#send-filter').click(function (){
            disabledBtnFilter();
            loadTableList();
        });

        const loadTableList = () => {
            const filter_store      = parseInt($('#stores').val()) === 0 ? null : parseInt($('#stores').val());
            const filter_ref        = $('#filter_ref').val();
            const filter_license    = $('#filter_license').val();
            const filter_active     = $('#filter_active').val();
            const filter_feature    = $('#filter_feature').val();
            const filter_brand      = $('#filter_brand').val();
            const filter_price      = {
                min: instanceRange.result.from,
                max: instanceRange.result.to
            };

            dataTableList = getTableList(
                'ajax/automoveis/buscar',
                {
                    filter_store,
                    filter_ref,
                    filter_license,
                    filter_active,
                    filter_feature,
                    filter_brand,
                    filter_price
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
                    $(row).find(`td:eq(${pos}) a:last`).addClass('ml-1');
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
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/ion-rangeslider/css/ion.rangeSlider.min.css') }}">
@endsection
