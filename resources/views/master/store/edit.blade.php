{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Atualizar Loja', 'no-active' => [['route' => 'admin.master.company.index', 'name' => 'Listagem de Empresas'], ['url' => route('admin.master.company.edit', ['id' => $store->company_id]), 'name' => 'Atualizar Empresa']]]])
{{-- Título da página --}}
@section('title', 'Atualizar Loja')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="error-form alert alert-warning {{ count($errors) == 0 ? 'display-none' : '' }}">
                <h5>Existem erros no envio do formulário, veja abaixo para corrigi-los.</h5>
                <ol>
                    @foreach($errors as $error)
                        <li><label id="name-error" class="error">{{ $error }}</label></li>
                    @endforeach
                </ol>
            </div>
            @if(session('message'))
                <div class="alert {{ session('typeMessage') === 'success' ? 'alert-success' : 'alert-warning' }}">
                    <p>{{ session('message') }}</p>
                </div>
            @endif

            <div class="card card-default" id="stores">
                <div class="card-header">
                    <h3 class="card-title">Atualizar Loja</h3>
                </div>
                <form action="{{ route('admin.master.company.store.update', ['company' => $store->company_id]) }}" enctype="multipart/form-data" id="formUpdateStore" method="POST">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6 no-padding">
                                <div class="form-group col-md-12">
                                    <label for="store_name">Razão Social</label>
                                    <input type="text" class="form-control" id="store_name" name="store_name" value="{{ old('store_name', $store->store_name) }}">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="store_fancy">Nome Fantasia (Visível ao Cliente)</label>
                                    <input type="text" class="form-control" id="store_fancy" name="store_fancy" value="{{ old('store_fancy', $store->store_fancy) }}">
                                </div>
                            </div>
                            <div class="form-group col-md-6 upload-image-logo d-flex flex-wrap justify-content-center no-padding">
                                <div class="img-preview-logo d-flex justify-content-center col-md-12">
                                    <img src="{{ old('store_logotipo') ?? asset("assets/admin/dist/images/stores/{$store->id}/{$store->store_logo}") }}"/>
                                </div>
                                <small class="col-md-12 text-center">Proporção 3:1 (300 x 100)</small>
                                <input type="file" accept="image/*" class="choose-file-logo" id="choose-file-logo-store" name="store_logotipo" />
                                <label for="choose-file-logo-store" class="btn btn-primary btn-lg col-md-6">Alterar Logotipo</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Tipo de Empresa</label>
                                <label class="col-md-12"><input type="radio" name="type_store" value="pf" {{ old('type_store', $store->type_store) === 'pf' ? 'checked' : '' }}> Pessoa Física</label>
                                <label class="col-md-12"><input type="radio" name="type_store" value="pj" {{ old('type_store', $store->type_store) === 'pj' ? 'checked' : '' }}> Pessoa Jurídica</label>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="document_primary_store">CNPJ</label>
                                <input type="text" class="form-control" id="document_primary_store" name="document_primary" value="{{ old('document_primary', $store->store_document_primary) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="document_secondary_store">IE</label>
                                <input type="text" class="form-control" id="document_secondary_store" name="document_secondary" value="{{ old('document_secondary', $store->store_document_secondary) }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Tipo de Domínio</label>
                                <label class="col-md-12"><input type="radio" name="domain" value="1" {{ old('domain', $store->type_domain) == 1 ? 'checked' : '' }}> Domínio Próprio</label>
                                <label class="col-md-12"><input type="radio" name="domain" value="0" {{ old('domain', $store->type_domain) == 0 ? 'checked' : '' }}> Domínio Compartilhado</label>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="with_domain_store">Domínio Próprio</label>
                                <input type="text" class="form-control" id="with_domain_store" name="with_domain" value="{{ old('with_domain', $store->store_domain) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="without_domain">Nome do Domínio</label>
                                <input type="text" class="form-control" id="without_domain" name="without_domain" value="{{ old('without_domain', $store->store_without_domain) }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12 border-top pt-2">
                                <h5 class="font-weight-bold text-uppercase">Contato de Disparo de Email</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="email_store">E-mail</label>
                                <input type="email" class="form-control" id="email_store" name="email_store" value="{{ old('email_store', $store->mail_contact_email) }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="password_store">Senha E-mail</label>
                                <input type="password" class="form-control" id="password_store" name="password_store" value="{{ old('password_store', $store->mail_contact_password) }}">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="mail_smtp">Endereço SMTP</label>
                                <input type="text" class="form-control" id="mail_smtp" name="mail_smtp" value="{{ old('mail_smtp', $store->mail_contact_smtp) }}">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="mail_port">Porta SMTP</label>
                                <input type="text" class="form-control" id="mail_port" name="mail_port" value="{{ old('mail_port', $store->mail_contact_port) }}">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="mail_security">Segurança SMTP</label>
                                <input type="text" class="form-control" id="mail_security" name="mail_security" value="{{ old('mail_security', $store->mail_contact_security) }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12 border-top pt-2">
                                <h5 class="font-weight-bold text-uppercase">Contato Para Clientes</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="contact_email_store">E-mail de Contato</label>
                                <input type="email" class="form-control" id="contact_email_store" name="contact_email_store" value="{{ old('contact_email_store', $store->contact_email) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="contact_primary_phone_store">Telefone Primário</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text pb-0 pt-0 pl-1 pr-1">
                                            <input type="checkbox" value="1" name="contact_primary_phone_store_whatsapp" id="contact_primary_phone_store_whatsapp" {{ old('contact_primary_phone_store_whatsapp', $store->contact_primary_phone_have_whatsapp) == 1 ? 'checked' : '' }}>
                                            <label for="contact_primary_phone_store_whatsapp" class="no-margin">
                                                <img src="https://imagepng.org/wp-content/uploads/2017/08/whatsapp-icone-4.png" width="33">
                                            </label>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" id="contact_primary_phone_store" name="contact_primary_phone_store" value="{{ old('contact_primary_phone_store', $store->contact_primary_phone) }}">
                                </div>

                            </div>
                            <div class="form-group col-md-4">
                                <label for="contact_secondary_phone_store">Telefone Secundário</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text pb-0 pt-0 pl-1 pr-1">
                                            <input type="checkbox" value="1" name="contact_secondary_phone_store_whatsapp" id="contact_secondary_phone_store_whatsapp" {{ old('contact_secondary_phone_store_whatsapp', $store->contact_secondary_phone_have_whatsapp) == 1 ? 'checked' : '' }}>
                                            <label for="contact_secondary_phone_store_whatsapp" class="no-margin">
                                                <img src="https://imagepng.org/wp-content/uploads/2017/08/whatsapp-icone-4.png" width="33">
                                            </label>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" id="contact_secondary_phone_store" name="contact_secondary_phone_store" value="{{ old('contact_secondary_phone_store', $store->contact_secondary_phone) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12 border-top pt-2">
                                <h5 class="font-weight-bold text-uppercase">Redes Sociais</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="social_networks">Rede Social</label>
                                <div class="input-group">
                                    <select class="select2 form-control" id="social_networks">
                                        <option value="facebook">Facebook</option>
                                        <option value="instagram">Instagram</option>
                                        <option value="youtube">YouTube</option>
                                        <option value="linkedin">LinkedIn</option>
                                        <option value="twitter">Twitter</option>
                                    </select>
                                    <span class="input-group-append">
                                        <button type="button" class="btn btn-success btn-flat" id="add_social_network_store">Adicionar</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="social_network_store">

                            @if (!empty($store->social_networks))
                                @foreach(\GuzzleHttp\json_decode($store->social_networks) as $network)

                                <div class="form-group col-md-12">
                                    <label>Link da Conta</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text pb-0 pt-0 pl-1 pr-1">
                                                <label for="" class="no-margin">
                                                    <img src="{{ asset("assets/admin/dist/images/redes-sociais/{$network->type}.png") }}" width="33">
                                                </label>
                                            </span>
                                        </div>
                                        <input type="url" class="form-control" name="social_networks_{{ $network->type }}" value="{{ $network->value }}">
                                        <span class="input-group-append">
                                            <button type="button" class="btn btn-danger btn-flat remove-network-store"><i class="fa fa-trash"></i></button>
                                        </span>
                                    </div>
                                </div>

                                @endforeach
                            @endif

                        </div>
                        <div class="row">
                            <div class="form-group col-md-12 border-top pt-2">
                                <h5 class="font-weight-bold text-uppercase">Configuração de Layout</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Cor Primária</label>
                                <div class="input-group colorpicker-primary">
                                    <input type="text" class="form-control" name="color-primary" autocomplete="off" value="{{ old('color-primary', $store->color_layout_primary) }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-square" style="color: {{ old('color-primary', $store->color_layout_primary) }}"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Cor Secundária</label>
                                <div class="input-group colorpicker-secundary">
                                    <input type="text" class="form-control" name="color-secundary" autocomplete="off" value="{{ old('color-secundary', $store->color_layout_secondary) }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-square" style="color: {{ old('color-secundary', $store->color_layout_secondary) }}"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12 border-top pt-2">
                                <h5 class="font-weight-bold text-uppercase">Horários de Atendimento</h5>
                            </div>
                        </div>
                        <div class="row d-flex justify-content-center">
                            <div class="col-md-6 col-xs-12">
                                <div class="form-group">
                                    <label for="descriptionService">Atendimento</label>
                                    <textarea name="descriptionService" id="descriptionService">{{ old('descriptionService', $store->description_service) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12 border-top pt-2 d-flex justify-content-between flex-wrap">
                                <h5 class="font-weight-bold text-uppercase">Endereço da Loja</h5>
                                <button type="button" class="btn btn-primary" id="confirm-map">Confirmar Endereço da Loja</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="address_zipcode">CEP</label>
                                <input type="text" class="form-control search-data-cep" id="address_zipcode" name="address_zipcode" value="{{ old('address_zipcode', $store->address_zipcode) }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="address_public_place">Endereço</label>
                                <input type="text" class="form-control" id="address_public_place" name="address_public_place" value="{{ old('address_public_place', $store->address_public_place) }}" address-search-cep>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="address_number">Número do Endereço</label>
                                <input type="text" class="form-control" id="address_number" name="address_number" value="{{ old('address_number', $store->address_number) }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="address_complement">Complemento</label>
                                <input type="text" class="form-control" id="address_complement" name="address_complement" value="{{ old('address_complement', $store->address_complement) }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="address_reference">Referência</label>
                                <input type="text" class="form-control" id="address_reference" name="address_reference" value="{{ old('address_reference', $store->address_reference) }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="address_neighborhoods">Bairro</label>
                                <input type="text" class="form-control" id="address_neighborhoods" name="address_neighborhoods" value="{{ old('address_neighborhoods', $store->address_neighborhoods) }}" neigh-search-cep>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="address_city">Cidade</label>
                                <input type="text" class="form-control" id="address_city" name="address_city" value="{{ old('address_city', $store->address_city) }}" city-search-cep>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="address_state">Estado</label>
                                <input type="text" class="form-control" id="address_state" name="address_state" value="{{ old('address_state', $store->address_state) }}" state-search-cep>
                            </div>
                        </div>
                        <input type="hidden" name="store_lat" value="{{ old('store_lat', $store->address_lat) }}">
                        <input type="hidden" name="store_lng" value="{{ old('store_lng', $store->address_lng) }}">
                        <input type="hidden" class="form-control" name="store_id_update">
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-between">
                                <a href="{{ route('admin.master.company.edit', ['id' => $store->company_id]) }}" class="btn btn-danger col-md-3"><i class="fas fa-arrow-left"></i> Voltar</a>
                                <button type="submit" class="btn btn-success col-md-3"><i class="fa fa-save"></i> Salvar</button>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="company_id" value="{{ $store->company_id }}">
                    <input type="hidden" name="store_id" value="{{ $store->id }}">
                    {!! csrf_field() !!}
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmAddress" tabindex="-1" role="dialog" aria-labelledby="newUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content card">
                <div class="modal-header">
                    <h5 class="modal-title" id="newUserModalLabel">Cadastrar Usuário</h5>
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
                        <div id="mapStore" style="height: 400px"></div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end">
                    <button type="button" class="btn btn-primary col-md-3" data-dismiss="modal"><i class="fa fa-save"></i> Salvar</button>
                </div>
                <div class="overlay dark d-none screen-user-new">
                    <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/jquery-image-uploader/src/image-uploader.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css') }}">
@stop

@section('css_pre')
    <style>
        #mapStore {
            width: 100%;
            height: 200px;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
@endsection

@section('js')
    <script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js"></script>
    <script src="{{ asset('assets/admin/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/jquery-validation/dist/jquery.validate.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/jquery-image-uploader/src/image-uploader.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/ckeditor4/ckeditor.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/ckeditor4/configs/description_service.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/dist/js/pages/stores/stores.js') }}"></script>
    <script>
        $(function () {
            CKEDITOR.replace('descriptionService', {
                toolbar: [
                    { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline' ] },
                    { name: 'colors', items: [ 'TextColor' ] },
                ]
            });

            $('#stores [name="type_store"]:checked').trigger('change');
            $('#stores [name="domain"]:checked').trigger('change');
            $('#stores input[name="contact_primary_phone_store"], #stores input[name="contact_secondary_phone_store"]').unmask().mask(maskPhone, phoneOptions);
            $('#stores [name="address_zipcode"]').unmask().mask('00.000-000');

            $('#stores #social_networks').select2();

            setTimeout(() => {
                getLocation();
            }, 2000);
        });
    </script>
@stop
