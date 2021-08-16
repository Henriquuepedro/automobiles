{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Visualizar Contato', 'no-active' => [['route' => 'admin.contactForm.index', 'name' => 'Listagem de Contatos']]]])
{{-- Título da página --}}
@section('title', 'Visualizar Contato')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header d-flex justify-content-between flex-wrap">
                    <h3 class="card-title">Visualizar Contato</h3>
                    <i class="fas fa-circle {{ $dataContact->sended ? 'text-green' : 'text-red' }}" style="font-size: 20px;cursor: pointer" data-toggle="tooltip" title="{{ $dataContact->sended ? 'Enviado para a Caixa de Entrada' : 'Não enviado para a Caixa de Entrada' }}"></i>
                </div>
                <div class="card-body">
                    <div class="row @if(count($stores) === 1) d-none @endif">
                        <div class="col-md-12 form-group">
                            <label for="autos">Loja</label>
                            <select class="form-control select2 col-md-12" id="stores" name="stores" disabled>
                                @if(count($stores) > 1)
                                    <option value="0">Selecione uma Loja</option>
                                @endif
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" {{ $dataContact->store_id == $store->id ? 'selected' : '' }}>{{ $store->store_fancy }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Nome</label>
                            <input type="text" class="form-control" value="{{ $dataContact->name }}" disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label>E-mail</label>
                            <input type="text" class="form-control" value="{{ $dataContact->email }}" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Telefone</label>
                            <input type="text" class="form-control" value="{{ $dataContact->phone }}" disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Assunto</label>
                            <input type="text" class="form-control" value="{{ $dataContact->subject }}" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="description">Mensagem</label>
                            <div class="mb-3">
                                <textarea class="form-control" rows="5" disabled>{{ $dataContact->message }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-between">
                            <a href="{{ route('admin.contactForm.index') }}" class="btn btn-danger col-md-3"><i class="fas fa-arrow-left"></i> Voltar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@stop

@section('js')
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(function(){
            $('[data-toggle="tooltip"]').tooltip();
        })
    </script>
@stop
