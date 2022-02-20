{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false, 'active' => 'Relatório de Variação da FIPE', 'no-active' => []]])
{{-- Título da página --}}
@section('title', 'Listagem de Depoimentos')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if (session('success'))
                <div class="alert alert-success mt-2">{{session('success')}}</div>
            @endif
            @if (session('warning'))
                <div class="alert alert-danger mt-2">{{session('warning')}}</div>
            @endif
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Variação da FIPE</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <h4 class="text-center">Selecione o veículo para visualizar a variação de preço</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="autos">Tipo Automóvel</label>
                                <select class="form-control select2" id="autos" name="autos">
                                    <option value="" disabled selected>SELECIONE</option>
                                    @foreach ($autoFipe->controlAutos as $control)
                                        <option value="{{ $control->code_str }}">{{ $control->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="marcas">Marca do Automóvel</label>
                                <select class="form-control select2" id="marcas" name="marcas">
                                    <option value="">Selecione um tipo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Modelo do Automóvel</label>
                                <select class="form-control select2" id="modelos" name="modelos">
                                    <option value="">Selecione a marca</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="anos">Ano do Automóvel</label>
                                <select class="form-control select2" id="anos" name="anos">
                                    <option value="">Selecione o modelo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 display-none justify-content-center" id="loadGraph">
                            <h4>Carregando variações, aguarde <i class="fa fa-spin fa-spinner"></i></h4>
                        </div>
                        <div class="col-md-12">
                            <canvas id="graph" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@stop

@section('js')
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/dist/js/pages/fipe/load-brand-model-year.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>
    <script>
        var chartGraph;
        $(function () {
            // Mostrar Marcas
            $('#autos').change(function () {
                loadBrands($(this).val(), false, () => { $('#marcas').select2('open') });
                destroyChart();
            });

            // Mostrar Modelo
            $('#marcas').change(function () {
                loadModels($(this).val(), () => { $('#modelos').select2('open') });
                destroyChart();
            });

            // Mostrar Anos
            $('#modelos').change(function () {
                loadYears($(this).val(), () => { $('#anos').select2('open') });
                destroyChart();
            });

            // Mostrar Fipe
            $('#anos').change(function () {
                destroyChart();

                const autos     = $('#autos').val();
                const marcas    = $('#marcas').val();
                const modelos   = $('#modelos').val();
                const anos      = $(this).val();

                $('#loadGraph').removeClass('display-none').addClass('d-flex');
                $('select').prop('disabled', true);

                $.get(`${window.location.origin}/admin/ajax/fipe/${autos}/marcas/${marcas}/modelos/${modelos}/anos/${anos}`, function (fipe) {
                    loadChartFipe(fipe.id, fipe.model_name);
                });
            });
        });

        const loadChartFipe = (id_fipe, label) => {

            $.get(`${window.location.origin}/admin/ajax/fipe-variacao/${id_fipe}`, async function (updates) {

                let labels = [], data = [], borderColor = [];

                $(updates).each(function(key, value){
                    labels.push(value.date);
                    data.push(value.value);
                    borderColor.push('#1FB939');
                });

                const ctx = document.getElementById('graph');

                if (chartGraph) {
                    chartGraph.destroy();
                }

                chartGraph = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [
                            {
                                label,
                                data,
                                borderColor,
                                fill: false,
                                stepped: true,
                                tension: 0
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        interaction: {
                            intersect: false,
                            axis: 'x'
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: (ctx) => 'Variação nos últimos 12 meses',
                            }
                        }
                    }
                });
            });

            setTimeout(() => {
                $('#loadGraph').removeClass('d-flex').addClass('display-none');
                $('select').prop('disabled', false);
            }, 1000);
        }

        const destroyChart = () => {
            if (chartGraph) {
                chartGraph.destroy();
            }
        }

    </script>
@stop
