{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Alterar Grupo', 'no-active' => [['route' => 'admin.rent.group.index', 'name' => 'Grupo do Automóvel']], 'route_back_page' => 'admin.rent.group.index']])
{{-- Título da página --}}
@section('title', 'Alterar Grupo')

@section('content')
    @if (session('message'))
        <div class="alert {{ session('typeMessage') === 'success' ? 'alert-success' : 'alert-warning' }}">
            <p>{{ session('message') }}</p>
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Alterar Grupo</h3><br/>
                    <small>Altere um grupo já cadastrado no sistema</small>
                </div>
                <form action="{{ route('admin.rent.group.update') }}" enctype="multipart/form-data" id="formGroup" method="POST">
                    <div class="card-body">
                        @if (isset($errors) && count($errors) > 0)
                            <div class="alert alert-warning">
                                <h4>Existem erros no envio do formulário, veja abaixo para corrigi-los.</h4>
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
                        <div class="row @if (count($stores) === 1) d-none @endif">
                            <h4 class="text-primary">Loja para atualização</h4>
                        </div>
                        <div class="row @if (count($stores) === 1) d-none @endif">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="stores">Loja</label>
                                    <select class="form-control select2" id="stores" name="stores" title="Por favor, selecione uma loja." required>
                                        @foreach ($stores as $store)
                                            <option value="{{ $store->id }}" @if ($store->id == old('stores', $group->store_id)) selected @endif>{{ $store->store_fancy }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-10">
                                <label for="name">Nome do Grupo</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $group->name) }}" required>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="active">Ativo</label></br>
                                <input type="checkbox" data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="SIM"  data-off-text="NÃO" name="active" id="active" value="1" {{ old('active', $group->active) == 1 ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="description">Descrição do Grupo</label>
                                <textarea class="form-control" id="description" name="description" required>{{ old('description', $group->description) }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="description">Associar Automóveis ao Grupo</label>
                                <br>
                                <select name="autos[]" id="autos" style="border-color: #ccc" class="from-control selectpicker show-tick w-100" data-live-search="true" data-actions-box="true" multiple="multiple" data-style="btn-blue" data-selected-text-format="count > 1" title="Selecione">
                                    <option value="1">Auto 1</option>
                                    <option value="2">Auto 2</option>
                                    <option value="3">Auto 3</option>
                                    <option value="4">Auto 4</option>
                                    <option value="5">Auto 5</option>
                                    <option value="6">Auto 6</option>
                                    <option value="7">Auto 7</option>
                                    <option value="8">Auto 8</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" class="form-control" name="group_id" value="{{ $group->id }}">
                    </div>
                    <div class="card-footer d-flex justify-content-between flex-wrap">
                        <a href="{{ route('admin.rent.group.index') }}" class="btn btn-primary col-md-3"><i class="fa fa-arrow-left"></i> Voltar</a>
                        <button class="btn btn-success col-md-3" id="btnCadastrar"><i class="fa fa-save"></i> Atualizar</button>
                    </div>
                    {!! csrf_field() !!}
                </form>
            </div>
        </div>
    </div>
@stop
@section('js_head')
@endsection
@section('js')
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

    <!-- (Optional) Latest compiled and minified JavaScript translation files -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/i18n/defaults-*.min.js"></script>
    <script>
        $(function(){
            $("input[data-bootstrap-switch]").each(function(){
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            });
        });
    </script>
@endsection
@section('css_pre')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    <style>
        .show-tick button.dropdown-toggle {
            border-color: #ccc
        }
        .show-tick .inner.show ul li.selected{
            background-color: #ddd;
        }
    </style>
@endsection
