{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Cadastro Local', 'no-active' => [['route' => 'admin.rent.place.index', 'name' => 'Local do Automóvel']], 'route_back_page' => 'admin.rent.place.index']])
{{-- Título da página --}}
@section('title', 'Cadastro Local')

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
                    <h3 class="card-title">Cadastro Local</h3><br/>
                    <small>Cadastro d um local já cadastrado no sistema</small>
                </div>
                <form action="{{ route('admin.rent.place.insert') }}" enctype="multipart/form-data" id="formPlace" method="POST">
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
                            <h4 class="text-primary">Loja para cadastrar</h4>
                        </div>
                        <div class="row @if (count($stores) === 1) d-none @endif">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="stores">Loja</label>
                                    <select class="form-control select2" id="stores" name="stores" title="Por favor, selecione uma loja." required>
                                        @if (count($stores) > 1)
                                            <option value="">Selecione uma loja</option>
                                        @endif
                                        @foreach ($stores as $store)
                                            <option value="{{ $store->id }}" @if ($store->id == old('stores')) selected @endif>{{ $store->store_fancy }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <h4 class="text-primary">Informações de Contato</h4>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="contact_email">E-mail de Contato</label>
                                <input type="email" class="form-control" id="contact_email" name="contact_email" value="{{ old('contact_email') }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="contact_primary_phone">Telefone Primário</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text pb-0 pt-0 pl-1 pr-1">
                                            <input type="checkbox" value="1" name="contact_primary_phone_whatsapp" id="contact_primary_phone_whatsapp" {{ old('contact_primary_phone_whatsapp') == 1 ? 'checked' : '' }}>
                                            <label for="contact_primary_phone_whatsapp" class="no-margin">
                                                <img src="{{ asset('assets/admin/dist/images/system/whatsapp-icone.png') }}" width="33">
                                            </label>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" id="contact_primary_phone" name="contact_primary_phone" value="{{ old('contact_primary_phone') }}">
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="contact_secondary_phone">Telefone Secundário</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text pb-0 pt-0 pl-1 pr-1">
                                            <input type="checkbox" value="whatsapp" name="contact_secondary_phone_whatsapp" id="contact_secondary_phone_whatsapp" {{ old('contact_secondary_phone_whatsapp') == 1 ? 'checked' : '' }}>
                                            <label for="contact_secondary_phone_whatsapp" class="no-margin">
                                                <img src="{{ asset('assets/admin/dist/images/system/whatsapp-icone.png') }}" width="33">
                                            </label>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" id="contact_secondary_phone" name="contact_secondary_phone" value="{{ old('contact_secondary_phone') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row d-flex justify-content-between flex-wrap mt-4">
                            <h4 class="text-primary">Informações do Endereço</h4>
                            <button type="button" class="btn btn-primary" id="confirm-map"><i class="fa fa-map-marked-alt"></i> Confirmar Endereço do local</button>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="withdrawal"><input type="checkbox" id="withdrawal" name="withdrawal" {{ old('withdrawal') == 1 ? 'checked' : '' }}> Endereço de Retirada</label>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="devolution"><input type="checkbox" id="devolution" name="devolution" {{ old('devolution') == 1 ? 'checked' : '' }}> Endereço de Devolução</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="address_zipcode">CEP</label>
                                <input type="text" class="form-control search-data-cep" id="address_zipcode" name="address_zipcode" value="{{ old('address_zipcode') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="address_public_place">Endereço</label>
                                <input type="text" class="form-control" id="address_public_place" name="address_public_place" value="{{ old('address_public_place') }}" address-search-cep>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="address_number">Número do Endereço</label>
                                <input type="text" class="form-control" id="address_number" name="address_number" value="{{ old('address_number') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="address_complement">Complemento</label>
                                <input type="text" class="form-control" id="address_complement" name="address_complement" value="{{ old('address_complement') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="address_reference">Referência</label>
                                <input type="text" class="form-control" id="address_reference" name="address_reference" value="{{ old('address_reference') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="address_neighborhoods">Bairro</label>
                                <input type="text" class="form-control" id="address_neighborhoods" name="address_neighborhoods" value="{{ old('address_neighborhoods') }}" neigh-search-cep>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="address_city">Cidade</label>
                                <input type="text" class="form-control" id="address_city" name="address_city" value="{{ old('address_city') }}" city-search-cep>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="address_state">Estado</label>
                                <input type="text" class="form-control" id="address_state" name="address_state" value="{{ old('address_state') }}" state-search-cep>
                            </div>
                        </div>
                        <input type="hidden" name="address_lat" value="{{ old('address_lat') }}">
                        <input type="hidden" name="address_lng" value="{{ old('address_lng') }}">
                    </div>
                    <div class="card-footer d-flex justify-content-between flex-wrap">
                        <a href="{{ route('admin.rent.place.index') }}" class="btn btn-primary col-md-3"><i class="fa fa-arrow-left"></i> Voltar</a>
                        <button class="btn btn-success col-md-3" id="btnCadastrar"><i class="fa fa-save"></i> Cadastrar</button>
                    </div>
                    {!! csrf_field() !!}
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmAddress" tabindex="-1" role="dialog" aria-labelledby="confirmAddressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content card">
                <form action="{{ route('admin.ajax.user.insert') }}" method="post" enctype="multipart/form-data" id="formUser">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmAddressModalLabel">Cadastrar Usuário</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 form-group text-center mb-2">
                                <button type="button" class="btn btn-primary" id="updateLocationMap"><i class="fas fa-sync-alt"></i> Atualizar Localização</button>
                            </div>
                        </div>
                        <div class="row">
                            <div id="mapPlace" style="height: 400px"></div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-end">
                        <button type="button" class="btn btn-primary col-md-3" data-dismiss="modal"><i class="fa fa-save"></i> Salvar</button>
                    </div>
                </form>
                <div class="overlay dark d-none screen-user-new">
                    <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js_head')
@endsection
@section('js')
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
    <script type="text/javascript" src="{{ asset('assets/admin/dist/js/pages/rent/place.js') }}"></script>
@endsection
@section('css_pre')
    <style>
        #mapPlace {
            width: 100%;
            height: 200px;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
@endsection
