{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Cadastro Página Dinâmica', 'no-active' => [['route' => 'admin.config.pageDynamic.listagem', 'name' => 'Listagem Página Dinâmica']]]])
{{-- Título da página --}}
@section('title', 'Cadastro Página Dinâmica')

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
                    <h3 class="card-title">Cadastro Página Dinâmica</h3><br/>
                    <small>Cadastro de uma nova página dinâmica</small>
                </div>
                <form action="{{ route('admin.config.pageDynamic.insert') }}" enctype="multipart/form-data" id="formRegister" method="POST">
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
                        <div class="error-form alert alert-warning display-none">
                            <h4>Existem erros no envio do formulário, veja abaixo para corrigi-los.</h4>
                            <ol></ol>
                        </div>
                        <div class="row @if (count($stores) === 1) d-none @endif">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="autos">Loja</label>
                                    <select class="form-control select2" id="stores" name="stores" required>
                                        @if (count($stores) > 1)
                                            <option value="0">Selecione uma Loja</option>
                                        @endif
                                        @foreach ($stores as $store)
                                            <option value="{{ $store->id }}">{{ $store->store_fancy }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Título da Página</label>
                                    <input type="text" class="form-control" name="title" id="title">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nome">Nome da Página</label>
                                    <input type="text" class="form-control" name="nome" id="nome">
                                    <small>Não deve conter espaços, acentos e caracteres especiais. Sua página fica como seudominio.com.br/pagina/<b>[NOME]</b></small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group clearfix">
                                    <div class="icheck-primary d-inline">
                                        <label for="ativo">Ativo</label><br/>
                                        <input type="checkbox" name="ativo" id="ativo">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Conteúdo da Página</label>
                                    <textarea name="conteudo" id="conteudo"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between flex-wrap">
                        <a href="{{ route('admin.config.pageDynamic.listagem') }}" class="btn btn-primary col-md-3"><i class="fa fa-arrow-left"></i> Voltar</a>
                        <button class="btn btn-success col-md-3" id="btnCadastrar"><i class="fa fa-save"></i> Cadastrar</button>
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
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/jquery-validation/dist/jquery.validate.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/icheck2/icheck.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/ckeditor4/ckeditor.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/ckeditor4/config.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#ativo').icheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal',
                increaseArea: '20%' // optional
            });

            CKEDITOR.replace( 'conteudo', {
                //extraPlugins: 'easyimage',
                //removePlugins: 'image',
                filebrowserUploadUrl: "{{ route('admin.ajax.ckeditor.uploadImages', ['_token' => csrf_token() ]) }}",
                filebrowserUploadMethod: 'form'
            } );
        });

        $('#nome').on('keypress', function(e) {
            var regex = new RegExp("^[a-zA-Z0-9._\b]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }

            e.preventDefault();
            return false;
        });
    </script>
@endsection
@section('css_pre')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.3/skins/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
