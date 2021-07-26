{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Listagem de Páginas', 'no-active' => []]])
{{-- Título da página --}}
@section('title', 'Listagem de Páginas')

@section('content')
    @if(session('message'))
        <div class="alert {{ session('typeMessage') === 'success' ? 'alert-success' : 'alert-warning' }}">
            <p>{{ session('message') }}</p>
        </div>
    @endif
    <div class="box">
        <div class="box-body">
            <div class="card">
                <div class="card-header">
                    <div class="col-md-10 pull-left">
                        <h3 class="card-title">Listagem de Páginas</h3><br/>
                        <small>Listagem de todas as páginas dinâmicas</small>
                    </div>
                    <div class="col-md-2 pull-right text-right text-xs-center">
                        <a href="{{ route('config.pageDyncamic.new') }}" class="btn btn-primary">Nova Página</a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table class="table table-bordered table-striped dataTable" id="tablePages">
                        <thead>
                            <tr>
                                <th>Nome da Página</th>
                                <th>Situação</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($pagesDynamics as $pageDynamic)
                            <tr>
                                <td>{{ $pageDynamic['nome'] }}</td>
                                <td>{{ $pageDynamic['ativo'] ? 'ativo' : 'inativo' }}</td>
                                <td><a href="{{ route('config.pageDyncamic.edit', ['id' => $pageDynamic['id']]) }}" class="btn btn-primary"><i class="fa fa-pencil-alt"></i></a></td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Nome da Página</th>
                                <th>Situação</th>
                                <th>Ação</th>
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
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}"/>
@endsection
