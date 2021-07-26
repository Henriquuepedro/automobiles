{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Administrar Complementares', 'no-active' => []]])
{{-- Título da página --}}
@section('title', 'Administrar Complementares')

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
                        <h3 class="card-title">Listagem de Complementares</h3><br/>
                        <small>Listagem de todos os complementares dos automóveis cadastrados</small>
                    </div>
                    <div class="col-md-2 pull-right text-right text-xs-center">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#newComplements">Novo Automovel</button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table class="table table-bordered table-striped dataTable">
                        <thead>
                            <tr>
                                <th>Complemento</th>
                                <th>Automóvel</th>
                                <th>Campo</th>
                                <th>Situação</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($complementsAuto as $complement)
                            <tr>
                                <td>{{ $complement['nome'] }}</td>
                                <td>{{ $complement['tipo_auto'] }}</td>
                                <td>{{ $complement['tipo_campo'] }}</td>
                                <td>{{ $complement['ativo'] ? 'ativo' : 'inativo' }}</td>
                                <td class="text-center">
                                    <button class="btn btn-primary editComplement" complement-id="{{ $complement['id'] }}"><i class="fa fa-edit"></i></button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Complemento</th>
                                <th>Automóvel</th>
                                <th>Campo</th>
                                <th>Situação</th>
                                <th>Ação</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="newComplements" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Cadastrar Complementar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row @if(count($stores) === 1) d-none @endif">
                        <div class="col-md-12 form-group">
                            <label for="autos">Loja</label>
                            <select class="form-control select2" id="stores" name="stores" required>
                                @if(count($stores) > 1)
                                    <option value="0">Selecione uma Loja</option>
                                @endif
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->store_fancy }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-10 form-group">
                            <label>Nome do Complementar</label>
                            <input type="text" class="form-control" name="new_name">
                        </div>
                        <div class="col-md-2 form-group">
                            <label>Ativo</label>
                            <br>
                            <input type="checkbox" name="new_active" checked>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Tipo de Automóvel</label>
                            <select class="form-control" name="new_tipo_auto">
                                <option value="carros">Carros</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Tipo Complementar</label>
                            <select class="form-control" name="new_tipo_campo">
                                <option value="text">Texto</option>
                                <option value="number">Número</option>
                                <option value="bool">Caixa de Seleção</option>
                                <option value="select">Seleção</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 form-group" id="new_values_select_content">
                            <label>Valores para seleção</label>
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control">
                                    <span class="input-group-append">
                                        <button type="button" class="btn btn-success" id="btnNewValueSelect_new">Adicionar Novo Valor</button>
                                    </span>
                                </div>
                            </div>
                            <ol id="new_values_select"></ol>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary col-md-3" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary col-md-3" id="registerNewComplement">Cadastrar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="updateComplements" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Atualizar Complementar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row @if(count($stores) === 1) d-none @endif">
                        <div class="col-md-12 form-group">
                            <label for="autos">Loja</label>
                            <select class="form-control select2" id="stores" name="stores" required>
                                @if(count($stores) > 1)
                                    <option value="0">Selecione uma Loja</option>
                                @endif
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->store_fancy }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-10 form-group">
                            <label>Nome do Complementar</label>
                            <input type="text" class="form-control" name="update_name">
                        </div>
                        <div class="col-md-2 form-group">
                            <label>Ativo</label>
                            <br>
                            <input type="checkbox" name="update_active" checked>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Tipo de Automóvel</label>
                            <select class="form-control" name="update_tipo_auto">
                                <option value="carros">Carros</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Tipo Complementar</label>
                            <select class="form-control" name="update_tipo_campo">
                                <option value="text">Texto</option>
                                <option value="number">Número</option>
                                <option value="bool">Caixa de Seleção</option>
                                <option value="select">Seleção</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 form-group" id="update_values_select_content">
                            <label>Valores para seleção</label>
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control">
                                    <span class="input-group-append">
                                        <button type="button" class="btn btn-success" id="btnNewValueSelect_update">Adicionar Novo Valor</button>
                                    </span>
                                </div>
                            </div>
                            <ol id="update_values_select"></ol>
                        </div>
                    </div>
                    <input type="hidden" name="complement_id">
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary col-md-3" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary col-md-3" id="btnUpdateComplement">Atualizar</button>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"></script>
    <script>
        $(function(){
            $('.select2').select2();
        });

        $('select[name="new_tipo_campo"]').change(function (){
            const value = $(this).val();

            if (value === 'select') {
                $('#new_values_select_content').slideDown('slow');
            } else {
                $('#new_values_select_content').slideUp('slow');
                $('#new_values_select').empty();
            }
        });

        $('select[name="update_tipo_campo"]').change(function (){
            const value = $(this).val();

            if (value === 'select') {
                $('#update_values_select_content').slideDown('slow');
            } else {
                $('#update_values_select_content').slideUp('slow');
                $('#update_values_select').empty();
            }
        });

        $('#btnNewValueSelect_new').click(function (){
            const value = $(this).closest('div').find('input');
            $('#new_values_select').append(`
                <li class="d-flex mb-2">
                    <button class="btn btn-danger btn-sm btn-flat mr-3 pr-3 pl-3"><i class="fa fa-trash"></i></button>
                    <h4>${value.val()}</h4>
                </li>
            `);
            value.val('');
        })
        .closest('div').find('input').keypress(function (e){
            const code = e.keyCode || e.which;
            if(code === 13) {
                $('#btnNewValueSelect_new').trigger('click');
            }
        });

        $('#btnNewValueSelect_update').click(function (){
            const value = $(this).closest('div').find('input');
            $('#update_values_select').append(`
                <li class="d-flex mb-2">
                    <button class="btn btn-danger btn-sm btn-flat mr-3 pr-3 pl-3"><i class="fa fa-trash"></i></button>
                    <h4>${value.val()}</h4>
                </li>
            `);
            value.val('');
        })
        .closest('div').find('input').keypress(function (e){
            const code = e.keyCode || e.which;
            if(code === 13) {
                $('#btnNewValueSelect_update').trigger('click');
            }
        });

        $('#new_values_select, #update_values_select').on('click', 'button', function(){
            $(this).closest('li').remove();
        });

        $('#registerNewComplement').click(function () {
            const form = $('#newComplements .modal-body');

            const name      = form.find('[name="new_name"]').val();
            const typeAuto  = form.find('[name="new_tipo_auto"]').val();
            const typeField = form.find('[name="new_tipo_campo"]').val();
            const active    = form.find('[name="new_active"]').is(':checked');
            const stores    = form.find('[name="stores"]').val();
            let valuesDefault = [];

            if (typeField === 'select') {
                $('#new_values_select_content ol li').each(function (){
                    valuesDefault.push($('h4', this).text());
                });
            }

            if (name === '') {
                Toast.fire({
                    icon: 'warning',
                    title: 'Preencha o nome do complementar'
                });
                return false;
            }

            if (typeField === 'select' && !valuesDefault.length) {
                Toast.fire({
                    icon: 'warning',
                    title: 'Adicione pelo menos um valor para selação'
                });
                return false;
            }

            $.ajax({
                url: "{{ route('ajax.complementar.insert') }}",
                type: 'post',
                data: {
                    name,
                    typeAuto,
                    typeField,
                    valuesDefault,
                    active,
                    stores
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
                        $('#newComplements').modal('hide');
                        const row = dataTable.row.add([
                            name,
                            typeAuto,
                            typeField,
                            active ? 'ativo' : 'inativo',
                            '<button class="btn btn-primary editComplement" complement-id="'+response.complement_id+'"><i class="fa fa-edit"></i></button>',
                        ]).draw().node();

                        $(row).find('td').eq(4).addClass('text-center');

                        form.find('[name="new_name"]').val('');
                        form.find('[name="new_tipo_auto"]').val('carros');
                        form.find('[name="new_tipo_campo"]').val('text');
                        form.find('[name="new_active"]').prop('checked');
                        $('#new_values_select').empty();
                    }
                }, error: e => {
                    console.log(e)
                }
            });
        });

        $('#btnUpdateComplement').click(function () {
            const form = $('#updateComplements .modal-body');

            const name      = form.find('[name="update_name"]').val();
            const typeAuto  = form.find('[name="update_tipo_auto"]').val();
            const typeField = form.find('[name="update_tipo_campo"]').val();
            const complementId = form.find('[name="complement_id"]').val();
            const active    = form.find('[name="update_active"]').is(':checked');
            const stores    = form.find('[name="stores"]').val();
            let valuesDefault = [];

            if (typeField === 'select') {
                $('#update_values_select_content ol li').each(function (){
                    valuesDefault.push($('h4', this).text());
                });
            }

            if (name === '') {
                Toast.fire({
                    icon: 'warning',
                    title: 'Preencha o nome do complementar'
                });
                return false;
            }

            if (typeField === 'select' && !valuesDefault.length) {
                Toast.fire({
                    icon: 'warning',
                    title: 'Adicione pelo menos um valor para selação'
                });
                return false;
            }

            $.ajax({
                url: "{{ route('ajax.complementar.update') }}",
                type: 'put',
                data: {
                    name,
                    typeAuto,
                    typeField,
                    valuesDefault,
                    complementId,
                    active,
                    stores
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
                        $('#updateComplements').modal('hide');

                        const tableRow = dataTable.row($(`button[complement-id="${complementId}"]`).closest('tr'));
                        const rData = [
                            name,
                            typeAuto,
                            typeField,
                            active ? 'ativo' : 'inativo',
                            '<td class="text-center"><button class="btn btn-primary editComplement" complement-id="'+complementId+'"><i class="fa fa-edit"></i></button></td>'
                        ];
                        dataTable.row( tableRow ).data(rData).draw();

                        $('#update_values_select').empty();
                    }
                }, error: e => {
                    console.log(e)
                }
            });
        });

        $(document).on('click', '.editComplement', function (){
            const complement = $(this).attr('complement-id');
            $.ajax({
                url: window.location.origin+"/admin/ajax/complementar/buscar_complementar/"+complement,
                type: 'get',
                success: response => {

                    if (typeof response.nome === "undefined") return [];

                    $('#updateComplements').find('[name="update_name"]').val(response.nome);
                    $('#updateComplements').find('[name="update_tipo_auto"]').val(response.tipo_auto);
                    $('#updateComplements').find('[name="update_tipo_campo"]').val(response.tipo_campo);
                    $('#updateComplements').find('[name="complement_id"]').val(response.id);
                    $('#updateComplements').find('[name="update_active"]').prop('checked', response.ativo == 1);
                    $('#updateComplements').find('[name="stores"]').select2('destroy').val(response.store_id).select2();
                    $('#updateComplements').modal();

                    if (response.tipo_campo === 'select') {
                        $(JSON.parse(response.valores_padrao)).each(function (key, value) {
                            $('#update_values_select_content').show();
                            $('#update_values_select').append(`
                                <li class="d-flex mb-2">
                                    <button class="btn btn-danger btn-sm btn-flat mr-3 pr-3 pl-3"><i class="fa fa-trash"></i></button>
                                    <h4>${value}</h4>
                                </li>
                            `);
                        });
                    } else {
                        $('#update_values_select_content').hide();
                        $('#update_values_select').empty();
                    }

                }, error: e => {
                    console.log(e)
                }
            });
        })
    </script>
@endsection
@section('css')
    <style>
        #new_values_select_content,
        #update_values_select_content {
            display: none;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}"/>
@endsection
@section('css_pre')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
