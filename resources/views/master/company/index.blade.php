{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false, 'active' => 'Listagem de Empresas', 'no-active' => []]])
{{-- Título da página --}}
@section('title', 'Listagem de Empresas')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if (session('success'))
                <div class="alert alert-success mt-2">{{session('success')}}</div>
            @endif
            @if (session('warning'))
                <div class="alert alert-danger mt-2">{{session('warning')}}</div>
            @endif
            @if (session('message'))
                <div class="alert {{ session('typeMessage') === 'success' ? 'alert-success' : 'alert-warning' }}">
                    <p>{{ session('message') }}</p>
                </div>
            @endif
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Empresas Cadastradas</h3>
                    <a href="{{ route('admin.master.company.new') }}" class="btn btn-primary col-md-3"><i class="fa fa-plus"></i> Nova Empresa</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="dataTableList" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Fantasia</th>
                            <th>Documento Primário</th>
                            <th>Data de Expiração</th>
                            <th>Criado Em</th>
                            <th>Ação</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                        <tr>
                            <th>#</th>
                            <th>Fantasia</th>
                            <th>Documento Primário</th>
                            <th>Data de Expiração</th>
                            <th>Criado Em</th>
                            <th>Ação</th>
                        </tr>
                        </tfoot>
                    </table>
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
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script>
        let dataTableList;

        $(function () {
            setTimeout(() => {
                getTableList('master/ajax/empresa/buscar')
            }, 500);
        });
    </script>
@stop
