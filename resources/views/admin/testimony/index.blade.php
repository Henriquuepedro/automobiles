{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false, 'active' => 'Listagem de Depoimentos', 'no-active' => []]])
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
                    <h3 class="card-title">Depoimentos Cadastrados</h3>
                    <a href="{{ route('admin.testimony.new') }}" class="btn btn-primary col-md-3"><i class="fa fa-plus"></i> Novo Depoimento</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row @if (count($stores) === 1) d-none @endif">
                        <div class="col-md-12 form-group">
                            <label for="stores">Loja</label>
                            <select class="form-control select2" id="stores" name="stores" required>
                                @if (count($stores) > 1)
                                    <option value="0">Todas as Loja</option>
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
                            <th>Imagem</th>
                            <th>Nome</th>
                            <th>Avaliação</th>
                            <th>Ativo</th>
                            <th>Primário</th>
                            <th>Criado Em</th>
                            @if (count($stores) > 1)<th>Loja</th>@endif
                            <th>Ação</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                        <tr>
                            <th>Imagem</th>
                            <th>Nome</th>
                            <th>Avaliação</th>
                            <th>Ativo</th>
                            <th>Primário</th>
                            <th>Criado Em</th>
                            @if (count($stores) > 1)<th>Loja</th>@endif
                            <th>Ação</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-delete" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="post" enctype="multipart/form-data" id="formRemoveTestimony">
                    <div class="modal-header">
                        <h4 class="modal-title">Excluir Depoimento</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <h5 class="text-danger font-weight-bold">Você tem certeza que deseja remover o depoimento: </h5>
                        <h5 class="text-danger name-testimony"></h5>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-primary col-md-5" data-dismiss="modal">Cancelar operação</button>
                        <button type="submit" class="btn btn-danger btnDeleteTestimony col-md-5">Excluir permanentemente</button>
                    </div>

                    <input type="hidden" name="testimony_id" value="">
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
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
            dataTableList = getTableList('ajax/depoimento/buscar', { store_id });
        });

        $(document).on('click', '.btnRequestDeleteTestimony', function () {
            const testimony_id    = $(this).attr('testimony-id');
            const testimony_name  = $(this).closest('tr').find('td:eq(1)').text();

            $('#modal-delete h5.name-testimony').text(testimony_name);
            $('#modal-delete [name="testimony_id"]').val(testimony_id);
            $('#modal-delete').modal();
        });

        $('#formRemoveTestimony').submit(function () {

            const testimony_id = $('[name="testimony_id"]', this).val();
            console.log(testimony_id);

            if (testimony_id === '' || testimony_id === 0) {
                Toast.fire({
                    icon: 'warning',
                    title: 'Não foi possível identificar o depoimento'
                });
                return false;
            }

            $.ajax({
                url: `${window.location.origin}/admin/ajax/depoimento/excluir/${testimony_id}`,
                type: 'delete',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: response => {

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

    </script>
@stop
