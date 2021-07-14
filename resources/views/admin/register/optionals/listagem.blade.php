{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Administrar Opcionais', 'no-active' => []]])
{{-- Título da página --}}
@section('title', 'Administrar Opcionais')

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
                        <h3 class="card-title">Listagem de Opcionais</h3><br/>
                        <small>Listagem de todos os opcionais dos automóveis cadastrados</small>
                    </div>
                    <div class="col-md-2 pull-right text-right text-xs-center">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#newOptionals">Novo Automovel</button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table class="table table-bordered table-striped dataTable">
                        <thead>
                            <tr>
                                <th>Opcional</th>
                                <th>Automóvel</th>
                                <th>Situação</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($optionalsAuto as $optional)
                            <tr>
                                <td>{{ $optional['nome'] }}</td>
                                <td>{{ $optional['tipo_auto'] }}</td>
                                <td>{{ $optional['ativo'] ? 'ativo' : 'inativo' }}</td>
                                <td class="text-center">
                                    <button class="btn btn-primary editOptional" optional-id="{{ $optional['id'] }}"><i class="fa fa-edit"></i></button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Opcional</th>
                                <th>Automóvel</th>
                                <th>Situação</th>
                                <th>Ação</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="newOptionals" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Cadastrar Opcional</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-10 form-group">
                            <label>Nome do Opcional</label>
                            <input type="text" class="form-control" name="new_name">
                        </div>
                        <div class="col-md-2 form-group">
                            <label>Ativo</label>
                            <br>
                            <input type="checkbox" name="new_active" checked>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label>Tipo de Automóvel</label>
                            <select class="form-control" name="new_tipo_auto">
                                <option value="carros">Carros</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary col-md-3" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary col-md-3" id="registerNewOptional">Cadastrar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="updateOptionals" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Atualizar Opcional</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-10 form-group">
                            <label>Nome do Opcional</label>
                            <input type="text" class="form-control" name="update_name">
                        </div>
                        <div class="col-md-2 form-group">
                            <label>Ativo</label>
                            <br>
                            <input type="checkbox" name="update_active" checked>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label>Tipo de Automóvel</label>
                            <select class="form-control" name="update_tipo_auto">
                                <option value="carros">Carros</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="optional_id">
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary col-md-3" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary col-md-3" id="btnUpdateOptional">Atualizar</button>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script src="{{ asset('admin/plugins/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('admin/plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
    <script src="//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"></script>
    <script>
        $('#registerNewOptional').click(function () {
            const form = $('#newOptionals .modal-body');

            const name      = form.find('[name="new_name"]').val();
            const typeAuto  = form.find('[name="new_tipo_auto"]').val();
            const active    = form.find('[name="new_active"]').is(':checked');

            if (name === '') {
                Toast.fire({
                    icon: 'warning',
                    title: 'Preencha o nome do opcional'
                });
                return false;
            }

            $.ajax({
                url: "{{ route('ajax.optional.insert') }}",
                type: 'post',
                data: {
                    name,
                    typeAuto,
                    active
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: response => {

                    Toast.fire({
                        icon: response.success ? 'success' : 'error',
                        title: response.message
                    });

                    if (response.success) {
                        $('#newOptionals').modal('hide');
                        const row = dataTable.row.add([
                            name,
                            typeAuto,
                            active ? 'ativo' : 'inativo',
                            '<button class="btn btn-primary editOptional" optional-id="'+response.optional_id+'"><i class="fa fa-edit"></i></button>',
                        ]).draw().node();

                        $(row).find('td').eq(3).addClass('text-center');

                        form.find('[name="new_name"]').val('');
                        form.find('[name="new_tipo_auto"]').val('carros');
                        form.find('[name="new_active"]').prop('checked');
                        $('#new_values_select').empty();
                    }
                }, error: e => {
                    console.log(e)
                }
            });
        });

        $('#btnUpdateOptional').click(function () {
            const form = $('#updateOptionals .modal-body');

            const name          = form.find('[name="update_name"]').val();
            const typeAuto      = form.find('[name="update_tipo_auto"]').val();
            const optionalId    = form.find('[name="optional_id"]').val();
            const active        = form.find('[name="update_active"]').is(':checked');

            if (name === '') {
                Toast.fire({
                    icon: 'warning',
                    title: 'Preencha o nome do opcional'
                });
                return false;
            }

            $.ajax({
                url: "{{ route('ajax.optional.update') }}",
                type: 'put',
                data: {
                    name,
                    typeAuto,
                    optionalId,
                    active
                },
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
                        $('#updateOptionals').modal('hide');

                        const tableRow = dataTable.row($(`button[optional-id="${optionalId}"]`).closest('tr'));
                        const rData = [
                            name,
                            typeAuto,
                            active ? 'ativo' : 'inativo',
                            '<td class="text-center"><button class="btn btn-primary editOptional" optional-id="'+optionalId+'"><i class="fa fa-edit"></i></button></td>'
                        ];
                        dataTable.row( tableRow ).data(rData).draw();

                        $('#update_values_select').empty();
                    }
                }, error: e => {
                    console.log(e)
                }
            });
        });

        $(document).on('click', '.editOptional', function (){
            const optional = $(this).attr('optional-id');
            $.ajax({
                url: window.location.origin+"/admin/ajax/opcional/buscar_opcional/"+optional,
                type: 'get',
                success: response => {

                    $('#updateOptionals').find('[name="update_name"]').val(response.nome);
                    $('#updateOptionals').find('[name="update_tipo_auto"]').val(response.tipo_auto);
                    $('#updateOptionals').find('[name="optional_id"]').val(response.id);
                    $('#updateOptionals').find('[name="update_active"]').prop('checked', response.ativo == 1);
                    $('#updateOptionals').modal();

                }, error: e => {
                    console.log(e)
                }
            });
        })
    </script>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('admin/plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}"/>
@endsection
