{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Grupo de Automóvel', 'no-active' => []]])
{{-- Título da página --}}
@section('title', 'Grupo de Automóvel')

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
                    <div class="col-md-9 pull-left">
                        <h3 class="card-title">Listagem dos locais dos automóveis</h3><br/>
                        <small>Listagem de todos os locais dos automóveis cadastrados, para retirada e devolução.</small>
                    </div>
                    <div class="col-md-3 pull-right text-right text-xs-center">
                        <a href="{{ route('admin.rent.group.new') }}" class="btn btn-primary w-100"><i class="fa fa-plus"></i> Novo Grupo</a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row @if (count($stores) === 1) d-none @endif">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="stores">Loja</label>
                                <select class="form-control select2" id="stores" name="stores" required>
                                    @if (count($stores) > 1)
                                        <option value="0">Todas as Loja</option>
                                    @endif
                                    @foreach ($stores as $store)
                                        <option value="{{ $store->id }}" {{ old('stores') == $store->id ? 'selected' : ''}}>{{ $store->store_fancy }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped table-hover" id="dataTableList">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Descrição</th>
                                <th>Situação</th>
                                <th>Criado Em</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Nome</th>
                                <th>Descrição</th>
                                <th>Situação</th>
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
@section('js')
    <script>
        let dataTableList;

        $(function () {
            setTimeout(() => {
                $('#stores').trigger('change')
            }, 500);
        });

        $('#stores').on('change', function(){
            const stores = parseInt($(this).val());

            dataTableList = getTableList(
                'ajax/aluguel/grupo/buscar',
                { stores }
            );
        });
    </script>
@endsection
@section('css')
@endsection
