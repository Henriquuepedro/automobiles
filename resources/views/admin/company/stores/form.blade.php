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
    <form action="{{ route('ajax.store.update') }}" method="post" enctype="multipart/form-data" id="formStore" style="display: none">
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
@section('js_form_store')
    <script type="text/javascript" src="{{ asset('admin/dist/js/pages/stores/stores.js') }}"></script>
@endsection
