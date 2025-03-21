{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Atualizar Empresa', 'no-active' => [['route' => 'admin.master.company.index', 'name' => 'Listagem de Empresas']]]])
{{-- Título da página --}}
@section('title', 'Atualizar Empresa')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="error-form alert alert-warning {{ count($errors) == 0 ? 'display-none' : '' }}">
                <h5>Existem erros no envio do formulário, veja abaixo para corrigi-los.</h5>
                <ol>
                    @foreach ($errors as $error)
                        <li><label id="name-error" class="error">{{ $error }}</label></li>
                    @endforeach
                </ol>
            </div>
            @if (session('message'))
                <div class="alert {{ session('typeMessage') === 'success' ? 'alert-success' : 'alert-warning' }}">
                    <p>{{ session('message') }}</p>
                </div>
            @endif

            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Alterar Empresa</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.master.company.update') }}" enctype="multipart/form-data" id="formCompany" method="POST">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="company_fancy">Nome Fantasia</label>
                                <input type="text" class="form-control" id="company_fancy" name="company_fancy" value="{{ old('company_fancy', $company->company_fancy) }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="company_name">Razão Social</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name', $company->company_name) }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="inputName2">Tipo de Empresa</label>
                                <label class="col-md-12"><input type="radio" name="type_company" id="type_pf" value="pf" {{ old('type_company', $company->type_company) == 'pf' ? 'checked' : '' }} required> Pessoa Física</label>
                                <label class="col-md-12"><input type="radio" name="type_company" id="type_pj" value="pj" {{ old('type_company', $company->type_company) == 'pj' ? 'checked' : '' }} required> Pessoa Jurídica</label>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="document_primary">CNPJ</label>
                                <input type="text" class="form-control" id="document_primary" name="document_primary" value="{{ old('document_primary', $company->company_document_primary) }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="document_secondary">IE</label>
                                <input type="text" class="form-control" id="document_secondary" name="document_secondary" value="{{ old('document_secondary', $company->company_document_secondary) }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="contact_mail">E-mail de Contato</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $company->contact_email) }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="inputSkills">Telefone Primário</label>
                                <input type="text" class="form-control" id="primary_phone" name="primary_phone" value="{{ old('primary_phone', $company->contact_primary_phone) }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="inputSkills">Telefone Secundário</label>
                                <input type="text" class="form-control" id="secondary_phone" name="secondary_phone" value="{{ old('secondary_phone', $company->contact_secondary_phone) }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12 border-top pt-2">
                                <h5 class="font-weight-bold text-uppercase">Plano</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="email_store">Plano</label>
                                <select class="form-control select2" id="plan_id" name="plan_id">
                                    <option value="0">Sem Plano (Experiência)</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" @if(old('plan_id', $company->plan_id) == $plan->id) selected @endif>{{ $plan->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="email_store">Data de Expiração</label>
                                <input type="date" class="form-control" id="plan_expiration_date" name="plan_expiration_date" value="{{ old('plan_expiration_date', $company->plan_expiration_date) }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-success col-md-3"><i class="fa fa-save"></i> Salvar Empresa</button>
                            </div>
                        </div>
                        <input type="hidden" name="company_id" value="{{ $company->id }}">
                        <input type="hidden" name="plan_expiration_date_old" value="{{ $company->plan_expiration_date }}">
                        <input type="hidden" name="plan_id_old" value="{{ $company->plan_id }}">
                        {!! csrf_field() !!}
                    </form>

                    <div class="card card-default collapsed-card mt-3" id="table_stores">
                        <div class="card-header btn-title-card d-flex justify-content-between align-items-center">
                            <h3 class="card-title cursor-pointer" data-card-widget="collapse"><i class="fa fa-eye"></i> Lojas Cadastradas</h3>
                            <a href="{{ route('admin.master.company.store.new', ['company' => $company->id]) }}" class="btn btn-primary col-md-3"><i class="fa fa-plus"></i> Nova Loja</a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <table class="table">
                                        <thead>
                                        <th>#</th>
                                        <th>Fantasia</th>
                                        <th>Documento Primário</th>
                                        <th>Domínio</th>
                                        <th>Criado Em</th>
                                        <th>Ação</th>
                                        </thead>
                                        <tbody>
                                        @foreach ($stores as $store)
                                            <tr>
                                                <td>{{ $store['id'] }}</td>
                                                <td>{{ $store['store_fancy'] }}</td>
                                                <td>{{ $store['store_document_primary'] }}</td>
                                                <td>{!! $store['store_domain'] ? '<strong>Próprio:</strong> ' : '<strong>Compartilhado:</strong> ' !!} {{ $store['store_domain'] ?? $store['store_without_domain'] }}</td>
                                                <td>{{ $store['created_at'] }}</td>
                                                <td><a href="{{ route('admin.master.company.store.edit', ['company' => $company->id, 'store' => $store['id']]) }}" class="btn btn-flat btn-sm btn-primary" data-toggle="tooltip" title="Atualizar"><i class="fa fa-pencil-alt"></i></a></td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-default collapsed-card mt-3" id="table_users">
                        <div class="card-header btn-title-card d-flex justify-content-between align-items-center">
                            <h3 class="card-title cursor-pointer" data-card-widget="collapse"><i class="fa fa-eye"></i> Usuários Cadastrados</h3>
                            <a href="{{ route('admin.master.company.user.new', ['company' => $company->id]) }}" class="btn btn-primary col-md-3"><i class="fa fa-plus"></i> Novo Usuário</a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <table class="table">
                                        <thead>
                                            <th>#</th>
                                            <th>Nome</th>
                                            <th>E-mail</th>
                                            <th>Permissão</th>
                                            <th>Criado Em</th>
                                            <th>Atualizado Em</th>
                                            <th>Ativo</th>
                                            <th>Ação</th>
                                        </thead>
                                        <tbody>
                                        @foreach ($users as $user)
                                            <tr>
                                                <td>{{ $user['id'] }}</td>
                                                <td>{{ $user['name'] }}</td>
                                                <td>{{ $user['email'] }}</td>
                                                <td>{{ $user['permission'] }}</td>
                                                <td>{{ $user['created_at'] }}</td>
                                                <td>{{ $user['updated_at'] }}</td>
                                                <td>{!! $user['active'] !!}</td>
                                                <td><a href="{{ route('admin.master.company.user.edit', ['company' => $company->id, 'user' => $user['id']]) }}" class="btn btn-flat btn-sm btn-primary" data-toggle="tooltip" title="Atualizar"><i class="fa fa-pencil-alt"></i></a></td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-between">
                            <a href="{{ route('admin.master.company.index') }}" class="btn btn-danger col-md-3"><i class="fas fa-arrow-left"></i> Voltar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/jquery-image-uploader/src/image-uploader.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <style>
        #table_stores  .card-header::after,
        #table_users  .card-header::after {
            display: none;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('assets/admin/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
    <script>
        $(function () {
            $('#plan_id').select2();
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

        $('#formCompany input[name="type_company"]').on('change', function () {
            if ($(this).val() === 'pf') {
                $('#document_primary').unmask().mask('000.000.000-00').closest('div').find('label').text('CPF');
                $('#document_secondary').closest('div').find('label').text('RG');
                $('#company_fancy').closest('.form-group').hide();
                $('#company_name').closest('.form-group').removeClass('col-md-6').addClass('col-md-12').find('label').text('Nome Completo');
            }
            else if ($(this).val() === 'pj') {
                $('#document_primary').unmask().mask('00.000.000/0000-00').closest('div').find('label').text('CNPJ');
                $('#document_secondary').closest('div').find('label').text('IE');
                $('#company_fancy').closest('.form-group').show();
                $('#company_name').closest('.form-group').removeClass('col-md-12').addClass('col-md-6').find('label').text('Razão Social');
            }
        });
    </script>
@stop
