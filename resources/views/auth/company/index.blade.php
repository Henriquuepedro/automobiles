{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Administrar Cadastro', 'no-active' => []]])
{{-- Título da página --}}
@section('title', 'Cadastro Automóvel')

@section('content')
        @if(session('message'))
            <div class="alert {{ session('typeMessage') === 'success' ? 'alert-success' : 'alert-warning' }}">
                <p>{{ session('message') }}</p>
            </div>
        @endif
        <div class="row">
            @if(isset($errors) && count($errors) > 0)
                <div class="alert alert-warning col-md-12">
                    <h4>Existem erros no envio do formulário, veja abaixo para corrigi-los.</h4>
                    <ol>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ol>
                </div>
            @endif
            <div class="error-form alert alert-warning display-none col-md-12 d-none">
                <h4>Existem erros no envio do formulário, veja abaixo para corrigi-los.</h4>
                <ol></ol>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#company" data-toggle="tab">Empresa</a></li>
                            <li class="nav-item"><a class="nav-link" href="#stores" data-toggle="tab">Lojas</a></li>
                            <li class="nav-item"><a class="nav-link" href="#users" data-toggle="tab">Usuários</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="company">
                                <form action="{{ route('admin.company.update') }}" method="post" enctype="multipart/form-data" id="formCompany">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="company_fancy">Nome Fantasia</label>
                                            <input type="text" class="form-control" id="company_fancy" name="company_fancy" value="{{ old('company_fancy', $dataCompany->company_fancy) }}">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="company_name">Razão Social</label>
                                            <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name', $dataCompany->company_name) }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="inputName2">Tipo de Empresa</label>
                                            <label class="col-md-12"><input type="radio" name="type_company" id="type_pf" value="pf" {{ old('type_company', $dataCompany->type_company) == 'pf' ? 'checked' : '' }}> Pessoa Física</label>
                                            <label class="col-md-12"><input type="radio" name="type_company" id="type_pj" value="pj" {{ old('type_company', $dataCompany->type_company) == 'pj' ? 'checked' : '' }}> Pessoa Jurídica</label>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="document_primary">CNPJ</label>
                                            <input type="text" class="form-control" id="document_primary" name="document_primary" value="{{ old('document_primary', $dataCompany->company_document_primary) }}">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="document_secondary">IE</label>
                                            <input type="text" class="form-control" id="document_secondary" name="document_secondary" value="{{ old('document_secondary', $dataCompany->company_document_secondary) }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="contact_mail">E-mail de Contato</label>
                                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $dataCompany->contact_email) }}">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="inputSkills">Telefone Primário</label>
                                            <input type="text" class="form-control" id="primary_phone" name="primary_phone" value="{{ old('primary_phone', $dataCompany->contact_primary_phone) }}">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="inputSkills">Telefone Secundário</label>
                                            <input type="text" class="form-control" id="secondary_phone" name="secondary_phone" value="{{ old('secondary_phone', $dataCompany->contact_secondary_phone) }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 border-top pt-3 text-right">
                                            <button type="submit" class="btn btn-success col-md-4"><i class="fa fa-save"></i> Salvar Alterações</button>
                                        </div>
                                    </div>
                                    {!! csrf_field() !!}
                                </form>
                            </div>
                            <div class="tab-pane" id="stores">
                                <div class="row">
                                    <div class="col-md-12 form-group border-bottom pb-3">
                                        <label>Lojas</label>
                                        <select class="select2 form-control" id="storesCompany">
                                            <option value="0">Selecione</option>
                                            @foreach($dataStores as $store)
                                                <option value="{{ $store['id'] }}">{{ $store['store_fancy'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <form action="{{ route('admin.store.update') }}" method="post" enctype="multipart/form-data" id="formStore" style="display: none">
                                    <div class="row">
                                        <div class="form-group col-md-6 no-padding">
                                            <div class="form-group col-md-12">
                                                <label for="store_name">Razão Social</label>
                                                <input type="text" class="form-control" id="store_name" name="store_name">
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label for="store_fancy">Nome Fantasia (Visível ao Cliente)</label>
                                                <input type="text" class="form-control" id="store_fancy" name="store_fancy">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6 upload-image-logo d-flex flex-wrap justify-content-center no-padding">
                                            <div class="img-preview-logo d-flex justify-content-center col-md-12">
                                                <img />
                                            </div>
                                            <small class="col-md-12 text-center">Proporção 3:1 (300 x 100)</small>
                                            <input type="file" accept="image/*" class="choose-file-logo" id="choose-file-logo-store" name="store_logotipo" />
                                            <label for="choose-file-logo-store" class="btn btn-primary btn-lg col-md-6">Alterar Logotipo</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label>Tipo de Empresa</label>
                                            <label class="col-md-12"><input type="radio" name="type_store" value="pf"> Pessoa Física</label>
                                            <label class="col-md-12"><input type="radio" name="type_store" value="pj"> Pessoa Jurídica</label>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="document_primary_store">CNPJ</label>
                                            <input type="text" class="form-control" id="document_primary_store" name="document_primary">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="document_secondary_store">IE</label>
                                            <input type="text" class="form-control" id="document_secondary_store" name="document_secondary">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label>Tipo de Domínio</label>
                                            <label class="col-md-12"><input type="radio" name="domain" value="1"> Domínio Próprio</label>
                                            <label class="col-md-12"><input type="radio" name="domain" value="0"> Domínio Compartilhado</label>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="with_domain_store">Domínio Próprio</label>
                                            <input type="text" class="form-control" id="with_domain_store" name="with_domain">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="without_domain">Nome do Domínio</label>
                                            <input type="text" class="form-control" id="without_domain" name="without_domain">
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
                                            <input type="email" class="form-control" id="email_store" name="email_store">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="password_store">Senha E-mail</label>
                                            <input type="password" class="form-control" id="password_store" name="password_store">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="mail_smtp">Endereço SMTP</label>
                                            <input type="text" class="form-control" id="mail_smtp" name="mail_smtp">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="mail_port">Porta SMTP</label>
                                            <input type="text" class="form-control" id="mail_port" name="mail_port">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="mail_security">Segurança SMTP</label>
                                            <input type="text" class="form-control" id="mail_security" name="mail_security">
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
                                            <input type="email" class="form-control" id="contact_email_store" name="contact_email_store">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="contact_primary_phone_store">Telefone Primário</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text pb-0 pt-0 pl-1 pr-1">
                                                        <input type="checkbox" value="whatsapp" name="contact_primary_phone_store_whatsapp" id="contact_primary_phone_store_whatsapp">
                                                        <label for="contact_primary_phone_store_whatsapp" class="no-margin">
                                                            <img src="https://imagepng.org/wp-content/uploads/2017/08/whatsapp-icone-4.png" width="33">
                                                        </label>
                                                    </span>
                                                </div>
                                                <input type="text" class="form-control" id="contact_primary_phone_store" name="contact_primary_phone_store">
                                            </div>

                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="contact_secondary_phone_store">Telefone Secundário</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text pb-0 pt-0 pl-1 pr-1">
                                                        <input type="checkbox" value="whatsapp" name="contact_secondary_phone_store_whatsapp" id="contact_secondary_phone_store_whatsapp">
                                                        <label for="contact_secondary_phone_store_whatsapp" class="no-margin">
                                                            <img src="https://imagepng.org/wp-content/uploads/2017/08/whatsapp-icone-4.png" width="33">
                                                        </label>
                                                    </span>
                                                </div>
                                                <input type="text" class="form-control" id="contact_secondary_phone_store" name="contact_secondary_phone_store">
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
                                    <div class="row" id="social_network_store"></div>

                                    <div class="row">
                                        <div class="form-group col-md-12 border-top pt-2">
                                            <h5 class="font-weight-bold text-uppercase">Endereço da Loja</h5>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-3">
                                            <label for="address_zipcode">CEP</label>
                                            <input type="text" class="form-control search-data-cep" id="address_zipcode" name="address_zipcode">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="address_public_place">Endereço</label>
                                            <input type="text" class="form-control" id="address_public_place" name="address_public_place" address-search-cep>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="address_number">Número do Endereço</label>
                                            <input type="text" class="form-control" id="address_number" name="address_number">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="address_complement">Complemento</label>
                                            <input type="text" class="form-control" id="address_complement" name="address_complement">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="address_reference">Referência</label>
                                            <input type="text" class="form-control" id="address_reference" name="address_reference">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="address_neighborhoods">Bairro</label>
                                            <input type="text" class="form-control" id="address_neighborhoods" name="address_neighborhoods" neigh-search-cep>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="address_city">Cidade</label>
                                            <input type="text" class="form-control" id="address_city" name="address_city" city-search-cep>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="address_state">Estado</label>
                                            <input type="text" class="form-control" id="address_state" name="address_state" state-search-cep>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12 d-flex justify-content-between flex-wrap border-top pt-3">
                                            <button type="button" class="btn btn-danger" id="ignoreUpdateStore"><i class="fa fa-times"></i> Ignorar Alteração</button>
                                            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar Alteração</button>
                                        </div>
                                    </div>
                                    <input type="hidden" class="form-control" name="store_id_update">
                                </form>
                            </div>
                            <div class="tab-pane" id="users">
                                <form class="form-horizontal">
                                    <div class="form-group">
                                        <label for="inputName">Name</label>
                                        <div class="col-sm-10">
                                            <input type="email" class="form-control" id="inputName" placeholder="Name">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail">Email</label>
                                        <div class="col-sm-10">
                                            <input type="email" class="form-control" id="inputEmail">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputName2">Name</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="inputName2" placeholder="Name">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="offset-sm-2 col-sm-10">
                                            <button type="submit" class="btn btn-danger">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="overlay dark d-none screen-company-store-user">
                                <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@stop
@section('js_head')
@endsection
@section('js')
    <script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js"></script>
    <script type="text/javascript" src="{{ asset('admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/plugins/jquery-image-uploader/src/image-uploader.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/plugins/jquery-validation/dist/jquery.validate.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/dist/js/pages/stores/stores.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/dist/js/pages/companies/companies.js') }}"></script>
    <script>
        const maskPhone = val => {
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
        }
        const phoneOptions = {
            onKeyPress: function(val, e, field, options) {
                field.mask(maskPhone.apply({}, arguments), options);
            }
        }

        $(function(){
            $('#formCompany input[name="type_company"]:checked').trigger('change');
            $('#formCompany input[name="primary_phone"], #formCompany input[name="secondary_phone"]').mask(maskPhone, phoneOptions);
            if ($('#storesCompany option').length === 2) {
                $('#storesCompany')
                    .val($('#storesCompany option:eq(1)').val())
                    .trigger('change')
                    .closest('.form-group')
                    .hide();
            }
        });

        $('#formCompany input[name="type_company"]').on('change', function (){
            if ($(this).val() === 'pf') {
                $('#document_primary').unmask().mask('000.000.000-00').closest('div').find('label').text('CPF');
                $('#document_secondary').closest('div').find('label').text('RG');
            }
            else if ($(this).val() === 'pj') {
                $('#document_primary').unmask().mask('00.000.000/0000-00').closest('div').find('label').text('CNPJ');
                $('#document_secondary').closest('div').find('label').text('IE');
            }
        });
    </script>
@endsection
@section('css_pre')
    <link rel="stylesheet" href="{{ asset('admin/plugins/jquery-image-uploader/src/image-uploader.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
