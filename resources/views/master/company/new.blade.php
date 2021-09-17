{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Cadastrar Empresa', 'no-active' => [['route' => 'admin.master.company.index', 'name' => 'Listagem de Empresas']]]])
{{-- Título da página --}}
@section('title', 'Cadastrar Empresa')

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

            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Cadastrar Empresa</h3>
                </div>
                <form action="{{ route('admin.master.company.insert') }}" enctype="multipart/form-data" id="formCompany" method="POST">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="company_fancy">Nome Fantasia</label>
                                <input type="text" class="form-control" id="company_fancy" name="company_fancy" value="{{ old('company_fancy') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="company_name">Razão Social</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name') }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="inputName2">Tipo de Empresa</label>
                                <label class="col-md-12"><input type="radio" name="type_company" id="type_pf" value="pf" {{ old('type_company') == 'pf' ? 'checked' : '' }} required> Pessoa Física</label>
                                <label class="col-md-12"><input type="radio" name="type_company" id="type_pj" value="pj" {{ old('type_company') == 'pj' ? 'checked' : '' }} required> Pessoa Jurídica</label>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="document_primary">CNPJ</label>
                                <input type="text" class="form-control" id="document_primary" name="document_primary" value="{{ old('document_primary') }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="document_secondary">IE</label>
                                <input type="text" class="form-control" id="document_secondary" name="document_secondary" value="{{ old('document_secondary') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="contact_mail">E-mail de Contato</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="inputSkills">Telefone Primário</label>
                                <input type="text" class="form-control" id="primary_phone" name="primary_phone" value="{{ old('primary_phone') }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="inputSkills">Telefone Secundário</label>
                                <input type="text" class="form-control" id="secondary_phone" name="secondary_phone" value="{{ old('secondary_phone') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-between">
                                <a href="{{ route('admin.master.company.index') }}" class="btn btn-danger col-md-3"><i class="fas fa-arrow-left"></i> Voltar</a>
                                <button type="submit" class="btn btn-success col-md-3"><i class="fa fa-save"></i> Salvar</button>
                            </div>
                        </div>
                    </div>
                    {!! csrf_field() !!}
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/jquery-image-uploader/src/image-uploader.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/summernote/summernote-bs4.css') }}">
@stop

@section('js')
    <script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js"></script>
    <script src="{{ asset('assets/admin/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
    <script>
        $(function () {
            $('#formCompany input[name="type_company"]:checked').trigger('change');
            $('#formCompany input[name="primary_phone"], #formCompany input[name="secondary_phone"]').mask(maskPhone, phoneOptions);
            if ($('#storesCompany option').length === 2) {
                $('#storesCompany')
                    .val($('#storesCompany option:eq(1)').val())
                    .trigger('change')
                    .closest('.form-group')
                    .hide();
            }

            if (!$('[name="type_company"]:checked').length)
                $('[name="type_company"][value="pf"]').prop('checked', true).trigger('change');
        });

        $('#formCompany input[name="type_company"]').on('change', function (){
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
