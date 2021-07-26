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
                        @include('admin.company.companies.form')
                        @include('admin.company.stores.form')
                        @include('admin.company.users.form')
                        <div class="overlay dark d-none screen-company-store-user">
                            <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.company.users.modalRegister')
    @include('admin.company.users.modalUpdate')
@stop
@section('js_head')
@endsection
@section('js')
    <script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/jquery-image-uploader/src/image-uploader.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/jquery-validation/dist/jquery.validate.js') }}"></script>

    @yield('js_form_user')
    @yield('js_form_store')
    @yield('js_form_company')

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
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/jquery-image-uploader/src/image-uploader.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
