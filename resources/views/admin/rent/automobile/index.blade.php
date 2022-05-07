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
@stop
@section('js')
    <script src="{{ asset('assets/admin/plugins/ion-rangeslider/js/ion.rangeSlider.min.js') }}"></script>
    <script>
        let dataTableList;

        $(function () {
            $('#filter_license').mask('SSS-0AA0');

            setTimeout(() => { loadTableList(); }, 500);
        });

        $('#filter_autos').on('expanded.lte.cardwidget', function(){
            $('#filter_autos .select2').select2();
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
@endsection
