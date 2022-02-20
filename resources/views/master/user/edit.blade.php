{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Atualizar Usuário', 'no-active' => [['route' => 'admin.master.company.index', 'name' => 'Listagem de Empresas'], ['url' => route('admin.master.company.edit', ['id' => $user->company_id]), 'name' => 'Atualizar Empresa']]]])
{{-- Título da página --}}
@section('title', 'Atualizar Usuário')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if (isset($errors) && count($errors) > 0)
                <div class="alert alert-warning col-md-12">
                    <h5>Existem erros no envio do formulário, veja abaixo para corrigi-los.</h5>
                    <ol>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ol>
                </div>
            @endif
            @if (session('message'))
                <div class="alert {{ session('typeMessage') === 'success' ? 'alert-success' : 'alert-warning' }}">
                    <p>{{ session('message') }}</p>
                </div>
            @endif

            <div class="card card-default" id="stores">
                <div class="card-header">
                    <h3 class="card-title">Atualizar Usuário</h3>
                </div>
                <form action="{{ route('admin.master.company.user.update', ['company' => $user->company_id]) }}" enctype="multipart/form-data" id="formUpdateUser" method="POST">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-5">
                                <label>Nome do Usuário</label>
                                <input type="text" class="form-control" name="name_user" value="{{ old('name_user', $user->user_name) }}">
                            </div>
                            <div class="form-group col-md-5">
                                <label>Endereço de Email</label>
                                <input type="email" class="form-control" name="email_user" value="{{ old('email_user', $user->user_email) }}">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="active">Ativo</label><br/>
                                <input type="checkbox" data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="SIM"  data-off-text="NÃO" name="active" id="active" value="1" {{ old('active', $user->user_active) == 1 ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-8">
                                <label>Loja</label>
                                <select class="select2 form-control" multiple name="store_user[]">
                                    @foreach ($arrStores as $store)
                                        <option value="{{ $store['id'] }}" {{in_array($store['id'], old('store_user',$arrStoresByUser)) ? 'selected' : ''}}>{{ $store['store_fancy'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Permissão</label><br>
                                <label><input type="radio" name="permission" value="admin" {{ old('permission', $user->permission) == 'admin' ? 'checked' : '' }}> Admin</label>
                                <label class="ml-4"><input type="radio" name="permission" value="user" {{ old('permission', $user->permission) == 'user' ? 'checked' : '' }}> Usuário</label>
                                <label class="ml-4"><input type="radio" name="permission" value="master" {{ old('permission', $user->permission) == 'master' ? 'checked' : '' }}> Master</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12 alert alert-info">
                                <p class="cursor-pointer" data-toggle="collapse" data-target="#collapseUpdatePassword" aria-expanded="false" aria-controls="collapseUpdatePassword"><i class="fa fa-key"></i> Alterar senha do usuário</p>
                            </div>
                        </div>
                        <div class="row collapse" id="collapseUpdatePassword">
                            <div class="form-group col-md-6">
                                <label>Senha de Acesso</label>
                                <input type="password" class="form-control" name="password_user">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Confirme a Senha</label>
                                <input type="password" class="form-control" name="password_user_confirmation">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-between">
                                <a href="{{ route('admin.master.company.edit', ['id' => $user->company_id]) }}" class="btn btn-danger col-md-3"><i class="fas fa-arrow-left"></i> Voltar</a>
                                <button type="submit" class="btn btn-success col-md-3"><i class="fa fa-save"></i> Salvar</button>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="company_id" value="{{ $user->company_id }}">
                    <input type="hidden" name="user_id" value="{{ $user->user_id }}">
                    {!! csrf_field() !!}
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <style>
        .select2.select2-container {
            width: unset !important;
        }
    </style>
@stop

@section('css_pre')
@endsection

@section('js')
    <script>
        $(function(){
            $("input[data-bootstrap-switch]").each(function(){
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            });
            $('[name="permission"]:checked').trigger('change')
        });

        $('[name="permission"]').on('change', function(){
            if ($(this).val() === 'master') {
                $('[name="store_user[]"] option').prop('selected', false).parent().select2('destroy').select2().attr('disabled', true);
            }
            else if ($('[name="store_user[]"]').is(':disabled')) {
                $('[name="store_user[]"]').attr('disabled', false);
            }
        })
    </script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
@stop
