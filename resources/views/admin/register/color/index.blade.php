{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Administrar Cores', 'no-active' => []]])
{{-- Título da página --}}
@section('title', 'Administrar Cores')

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
                        <h3 class="card-title">Listagem de Cores</h3><br/>
                        <small>Listagem de todos os cores dos automóveis cadastrados</small>
                    </div>
                    <div class="col-md-2 pull-right text-right text-xs-center">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#newColors">Nova Cor</button>
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
                                <th>Cor</th>
                                <th>Criado Em</th>
                                <th>Situação</th>
                                <th>Loja</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th>Cor</th>
                                <th>Criado Em</th>
                                <th>Situação</th>
                                <th>Loja</th>
                                <th>Ação</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="newColors" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Cadastrar Cores</h5>
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
                            <label>Nome da Cor</label>
                            <input type="text" class="form-control" name="new_name">
                        </div>
                        <div class="col-md-2 form-group">
                            <label>Ativo</label>
                            <br>
                            <input type="checkbox" name="new_active" checked>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary col-md-3" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary col-md-3" id="registerNewColor">Cadastrar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="updateColors" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Atualizar Cor</h5>
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
                            <label>Nome da Cor</label>
                            <input type="text" class="form-control" name="update_name">
                        </div>
                        <div class="col-md-2 form-group">
                            <label>Ativo</label>
                            <br>
                            <input type="checkbox" name="update_active" checked>
                        </div>
                    </div>
                    <input type="hidden" name="color_id">
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary col-md-3" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary col-md-3" id="btnUpdateColor">Atualizar</button>
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
    <script>
        let dataTableList;

        $(function() {
            setTimeout(() => {
                $('#storesFilter').trigger('change');
            }, 500);
        });

        $('#storesFilter').on('change', function() {
            const store_id = parseInt($(this).val()) === 0 ? null : parseInt($(this).val());
            dataTableList = getTableList('ajax/cores-automoveis/buscar', { store_id });
        });

        $('#registerNewColor').click(function () {
            const form = $('#newColors .modal-body');

            const name      = form.find('[name="new_name"]').val();
            const active    = form.find('[name="new_active"]').is(':checked');
            const stores    = form.find('[name="stores"]').val();

            if (name === '') {
                Toast.fire({
                    icon: 'warning',
                    title: 'Preencha o nome da cor'
                });
                return false;
            }

            $.ajax({
                url: "{{ route('admin.ajax.colorAuto.insert') }}",
                type: 'post',
                data: {
                    name,
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
                        $('#newColors').modal('hide');
                        $('#storesFilter').trigger('change');

                        form.find('[name="new_name"]').val('');
                        form.find('[name="new_active"]').prop('checked');
                    }
                }, error: e => {
                    console.log(e)
                }
            });
        });

        $('#btnUpdateColor').click(function () {
            const form = $('#updateColors .modal-body');

            const name      = form.find('[name="update_name"]').val();
            const colorId   = form.find('[name="color_id"]').val();
            const active    = form.find('[name="update_active"]').is(':checked');
            const stores    = form.find('[name="stores"]').val();

            if (name === '') {
                Toast.fire({
                    icon: 'warning',
                    title: 'Preencha o nome da cor'
                });
                return false;
            }

            $.ajax({
                url: "{{ route('admin.ajax.colorAuto.update') }}",
                type: 'put',
                data: {
                    name,
                    colorId,
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
                        $('#updateColors').modal('hide');
                        $('#storesFilter').trigger('change');
                    }
                }, error: e => {
                    console.log(e)
                }
            });
        });

        $(document).on('click', '.editColor', function () {
            const color = $(this).attr('color-id');
            $.ajax({
                url: window.location.origin+"/admin/ajax/cores-automoveis/buscar_cor/"+color,
                type: 'get',
                success: response => {
                    if (typeof response.nome === "undefined") {
                        return [];
                    }

                    $('#updateColors').find('[name="update_name"]').val(response.nome);
                    $('#updateColors').find('[name="color_id"]').val(response.id);
                    $('#updateColors').find('[name="update_active"]').prop('checked', response.active === 1);
                    $('#updateColors').find('[name="stores"]').select2('destroy').val(response.store_id).select2();
                    $('#updateColors').modal();
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
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
