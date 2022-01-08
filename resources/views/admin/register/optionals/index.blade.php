{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Administrar Opcionais', 'no-active' => []]])
{{-- Título da página --}}
@section('title', 'Administrar Opcionais')

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
                        <h3 class="card-title">Listagem de Opcionais</h3><br/>
                        <small>Listagem de todos os opcionais dos automóveis cadastrados</small>
                    </div>
                    <div class="col-md-2 pull-right text-right text-xs-center">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#newOptionals">Novo Automovel</button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row @if (count($stores) === 1) d-none @endif">
                        <div class="col-md-12 form-group">
                            <label for="autos">Loja</label>
                            <select class="form-control select2" id="storesFilter" name="stores" required>
                                @if (count($stores) > 1)
                                    <option value="0">Todas as Loja</option>
                                @endif
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->store_fancy }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped" id="dataTableList">
                        <thead>
                            <tr>
                                <th>Opcional</th>
                                <th>Automóvel</th>
                                <th>Situação</th>
                                @if (count($stores) > 1)<th>Loja</th>@endif
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th>Opcional</th>
                                <th>Automóvel</th>
                                <th>Situação</th>
                                @if (count($stores) > 1)<th>Loja</th>@endif
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
                    <div class="row @if (count($stores) === 1) d-none @endif">
                        <div class="col-md-12 form-group">
                            <label for="autos">Loja</label>
                            <select class="form-control select2" id="storesNew" name="stores" required>
                                @if (count($stores) > 1)
                                    <option value="0">Selecione uma Loja</option>
                                @endif
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->store_fancy }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
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
                                <option value="all">Todos</option>
                                @foreach ($controlAutos as $control)
                                    <option value="{{ $control->code_str }}">{{ $control->name }}</option>
                                @endforeach
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
                    <div class="row @if (count($stores) === 1) d-none @endif">
                        <div class="col-md-12 form-group">
                            <label for="autos">Loja</label>
                            <select class="form-control select2" id="storesEdit" name="stores" required>
                                @if (count($stores) > 1)
                                    <option value="0">Selecione uma Loja</option>
                                @endif
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->store_fancy }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
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
                                <option value="all">Todos</option>
                                @foreach ($controlAutos as $control)
                                    <option value="{{ $control->code_str }}">{{ $control->name }}</option>
                                @endforeach
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
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        let dataTableList;

        $(function() {
            setTimeout(() => {
                $('#storesFilter').trigger('change');
            }, 500);
        });

        $('#storesFilter').on('change', function() {
            const store_id = parseInt($(this).val()) === 0 ? null : parseInt($(this).val());
            dataTableList = getTableList('ajax/opcional/buscar', { store_id });
        });

        $('#registerNewOptional').click(function () {
            const form = $('#newOptionals .modal-body');

            const name      = form.find('[name="new_name"]').val();
            const typeAuto  = form.find('[name="new_tipo_auto"]').val();
            const active    = form.find('[name="new_active"]').is(':checked');
            const stores    = form.find('[name="stores"]').val();

            if (name === '') {
                Toast.fire({
                    icon: 'warning',
                    title: 'Preencha o nome do opcional'
                });
                return false;
            }

            $.ajax({
                url: "{{ route('admin.ajax.optional.insert') }}",
                type: 'post',
                data: {
                    name,
                    typeAuto,
                    active,
                    stores
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
                        $('#storesFilter').trigger('change');
                        $('#new_values_select').empty();

                        form.find('[name="new_name"]').val('');
                        form.find('[name="new_tipo_auto"]').val(form.find('[name="new_tipo_auto"] option:eq(0)').val());
                        form.find('[name="new_active"]').prop('checked');
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
            const stores        = form.find('[name="stores"]').val();

            if (name === '') {
                Toast.fire({
                    icon: 'warning',
                    title: 'Preencha o nome do opcional'
                });
                return false;
            }

            $.ajax({
                url: "{{ route('admin.ajax.optional.update') }}",
                type: 'put',
                data: {
                    name,
                    typeAuto,
                    optionalId,
                    active,
                    stores
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
                        $('#updateOptionals').modal('hide');
                        $('#storesFilter').trigger('change');
                        $('#update_values_select').empty();
                    }
                }, error: e => {
                    console.log(e)
                }
            });
        });

        $(document).on('click', '.editOptional', function () {
            const optional = $(this).attr('optional-id');
            $.ajax({
                url: window.location.origin+"/admin/ajax/opcional/buscar_opcional/"+optional,
                type: 'get',
                success: response => {

                    if (typeof response.nome === "undefined") return [];

                    $('#updateOptionals').find('[name="update_name"]').val(response.nome);
                    $('#updateOptionals').find('[name="update_tipo_auto"]').val(response.tipo_auto);
                    $('#updateOptionals').find('[name="optional_id"]').val(response.id);
                    $('#updateOptionals').find('[name="update_active"]').prop('checked', response.ativo == 1);
                    $('#updateOptionals').find('[name="stores"]').select2('destroy').val(response.store_id).select2();
                    $('#updateOptionals').modal();

                }, error: e => {
                    console.log(e)
                }
            });
        })
    </script>
@endsection
@section('css')
@endsection
@section('css_pre')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}"/>
@endsection
