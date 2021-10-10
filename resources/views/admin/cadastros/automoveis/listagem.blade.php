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
            <div class="card">
                <div class="card-header">
                    <div class="col-md-10 pull-left">
                        <h3 class="card-title">Listagem de Automóveis</h3><br/>
                        <small>Listagem de todos os automóveis cadastrados</small>
                    </div>
                    <div class="col-md-2 pull-right text-right text-xs-center">
                        <a href="{{ route('admin.automoveis.cadastro') }}" class="btn btn-primary">Novo Automovel</a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table class="table table-bordered table-striped dataTable" id="tableAutos">
                        <thead>
                            <tr>
                                <th>Imagem</th>
                                <th>Marca / Modelo</th>
                                <th>Cor / Ano</th>
                                <th>Valor / Kms</th>
                                @if (count($storesUser) > 1)<th>Loja</th>@endif
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($dataAutos as $automovel)
                            <tr data-url="{{ route('admin.automoveis.edit', ['codAuto' => $automovel['codauto']]) }}">
                                <td class="text-center"><img height="60" src="{{ asset($automovel['path']) }}" /></td>
                                <td>
                                    <span class="badge badge-pill badge-lg badge-{{ $automovel['active'] ? 'success' : "danger" }}">{{ $automovel['active'] ? 'Ativo' : "Inativo" }}</span>
                                    @if ($automovel['destaque'])
                                        <b class="text-yellow"><i class="fa fa-star"></i> DESTAQUE </b><br/>
                                    @else
                                        <br/>
                                    @endif{{ $automovel['marca'] }}
                                    <br/>
                                    {{ $automovel['modelo'] }}
                                </td>
                                <td>{{ $automovel['cor'] }} <br/> {{ $automovel['ano'] }}</td>
                                <td>{{ $automovel['valor'] }} <br/> {{ $automovel['kms'] }}</td>
                                @if (count($storesUser) > 1)<td>{{ $automovel['store'] }}</td>@endif
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Imagem</th>
                                <th>Marca / Modelo</th>
                                <th>Cor / Ano</th>
                                <th>Valor / Kms</th>
                                @if (count($storesUser) > 1)<th>Loja</th>@endif
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
@stop
@section('js')
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
    <script src="//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"></script>
    <script>
        $('table tr').click(function() {
            window.location = $(this).data('url');
            return false;
        });
    </script>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}"/>
@endsection
