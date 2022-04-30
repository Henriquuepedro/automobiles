{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Listagem de Páginas', 'no-active' => []]])
{{-- Título da página --}}
@section('title', 'Listagem de Páginas')

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
                        <h3 class="card-title">Listagem de Páginas</h3><br/>
                        <small>Listagem de todas as páginas dinâmicas</small>
                    </div>
                    <div class="col-md-2 pull-right text-right text-xs-center">
                        <a href="{{ route('admin.config.pageDynamic.new') }}" class="btn btn-primary">Nova Página</a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row @if (count($stores) === 1) d-none @endif">
                        <div class="col-md-12 form-group">
                            <label for="autos">Loja</label>
                            <select class="form-control select2" id="stores" name="stores" required>
                                @if (count($stores) > 1)
                                    <option value="0">Selecione uma Loja</option>
                                @endif
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->store_fancy }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <table id="dataTableList" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nome da Página</th>
                                <th>Situação</th>
                                <th>Criado Em</th>
                                @if(count($stores) > 1)<th>Loja</th>@endif
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th>Nome da Página</th>
                                <th>Situação</th>
                                <th>Criado Em</th>
                                @if(count($stores) > 1)<th>Loja</th>@endif
                                <th>Ação</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-delete" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="post" enctype="multipart/form-data" id="formRemovePage">
                    <div class="modal-header">
                        <h4 class="modal-title">Excluir Página</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <h5 class="text-danger font-weight-bold">Você tem certeza que deseja remover a página: </h5>
                        <h5 class="text-danger name-page"></h5>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-primary col-md-5" data-dismiss="modal">Cancelar operação</button>
                        <button type="submit" class="btn btn-danger btnDeletePage col-md-5">Excluir permanentemente</button>
                    </div>

                    <input type="hidden" name="page_id" value="">
                </form>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script>
        let dataTableList;

        $(function () {
            setTimeout(() => {
                $('#stores').trigger('change');
            }, 500);
        });

        $('#stores').on('change', function() {
            const store_id = parseInt($(this).val()) === 0 ? null : parseInt($(this).val());
            dataTableList = getTableList('ajax/paginaDinamica/buscar', { store_id });
        });

        $('#formRemovePage').submit(function () {

            const page_id = $('[name="page_id"]', this).val();
            console.log(page_id);

            if (page_id === '' || page_id === 0) {
                Toast.fire({
                    icon: 'warning',
                    title: 'Não foi possível identificar a página'
                });
                return false;
            }

            $.ajax({
                url: `${window.location.origin}/admin/ajax/paginaDinamica/excluir/${page_id}`,
                type: 'delete',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: response => {

                    console.log(response);

                    Toast.fire({
                        icon: response.success ? 'success' : 'error',
                        title: response.message
                    });

                    if (response.success) {
                        $('#modal-delete').modal('hide');
                        $('#stores').trigger('change');
                    }
                }, error: e => {
                    console.log(e)
                }
            });
            return false;
        });

        $(document).on('click', '.btnRequestDeletePage', function () {
            const page_id    = $(this).attr('page-id');
            const page_name  = $(this).closest('tr').find('td:eq(0)').text();

            $('#modal-delete h5.name-page').text(page_name);
            $('#modal-delete [name="page_id"]').val(page_id);
            $('#modal-delete').modal();
        });
    </script>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
