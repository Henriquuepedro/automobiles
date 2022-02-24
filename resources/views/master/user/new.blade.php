{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Cadastrar Usuário', 'no-active' => [['route' => 'admin.master.company.index', 'name' => 'Listagem de Empresas'], ['url' => route('admin.master.company.edit', ['id' => $company]), 'name' => 'Atualizar Empresa']]]])
{{-- Título da página --}}
@section('title', 'Cadastrar Usuário')

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
                    <h3 class="card-title">Cadastrar Usuário</h3>
                </div>
                <form action="{{ route('admin.master.company.user.insert', ['company' => $company]) }}" enctype="multipart/form-data" id="formUpdateStore" method="POST">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Nome do Usuário</label>
                                <input type="text" class="form-control" name="name_user" value="{{ old('name_user') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Endereço de Email</label>
                                <input type="email" class="form-control" name="email_user" value="{{ old('email_user') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-8">
                                <label>Loja</label>
                                <select class="select2 form-control" multiple name="store_user[]">
                                    @foreach ($arrStores as $store)
                                        <option value="{{ $store['id'] }}" {{in_array($store['id'], old('store_user') ?? array()) ? 'selected' : ''}}>{{ $store['store_fancy'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Permissão</label><br>
                                <label><input type="radio" name="permission" value="admin" {{ old('permission') == 'admin' ? 'checked' : '' }}> Admin</label>
                                <label class="ml-4"><input type="radio" name="permission" value="user" {{ old('permission') == 'user' ? 'checked' : '' }}> Usuário</label>
                                <label class="ml-4"><input type="radio" name="permission" value="master" {{ old('permission') == 'master' ? 'checked' : '' }}> Master</label>
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
                                <a href="{{ route('admin.master.company.edit', ['id' => $company]) }}" class="btn btn-danger col-md-3"><i class="fas fa-arrow-left"></i> Voltar</a>
                                <button type="submit" class="btn btn-success col-md-3"><i class="fa fa-save"></i> Salvar</button>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="company_id" value="{{ $company }}">
                    {!! csrf_field() !!}
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
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
            $('[name="permission"]:checked').trigger('change')
        });

        $('[name="permission"]').on('change', function(){
            console.log($(this).val());
            if ($(this).val() === 'master') {
                $('[name="store_user[]"] option').prop('selected', false).parent().select2('destroy').select2().attr('disabled', true);
            }
            else if ($('[name="store_user[]"]').is(':disabled')) {
                $('[name="store_user[]"]').attr('disabled', false);
            }
        })
    </script>
@stop
