{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Cadastro Automóvel', 'no-active' => [['route' => 'admin.automoveis.listagem', 'name' => 'Listagem Automóveis']]]])
{{-- Título da página --}}
@section('title', 'Cadastro Automóvel')

@section('content')
        @if(session('message'))
            <div class="alert {{ session('typeMessage') === 'success' ? 'alert-success' : 'alert-warning' }}">
                <p>{{ session('message') }}</p>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Cadastro de Automóvel</h3><br/>
                        <small>Cadastro de um novo automóvel para o sistema</small>
                    </div>
                    <form action="{{ route('admin.automoveis.cadastro.save') }}" enctype="multipart/form-data" id="formCadastroAutos" method="POST">
                        <div class="card-body">
                            @if(isset($errors) && count($errors) > 0)
                                <div class="alert alert-warning">
                                    <h4>Existem erros no envio do formulário, veja abaixo para corrigi-los.</h4>
                                    <ol>
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ol>
                                </div>
                            @endif
                            <div class="error-form alert alert-warning display-none">


                                <h4>Existem erros no envio do formulário, veja abaixo para corrigi-los.</h4>
                                <ol></ol>

                            </div>
                            <div class="row @if(count($dataAuto->stores) === 1) d-none @endif">
                                <h4 class="text-primary">Loja para cadastro</h4>
                            </div>
                            <div class="row @if(count($dataAuto->stores) === 1) d-none @endif">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="autos">Loja</label>
                                        <select class="form-control select2" id="stores" name="stores" title="Por favor, selecione uma loja." required>
                                            @if(count($dataAuto->stores) > 1)
                                                <option value="0">Selecione uma Loja</option>
                                            @endif
                                            @foreach($dataAuto->stores as $store)
                                                <option value="{{ $store->id }}">{{ $store->store_fancy }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @if(count($dataAuto->stores) > 1)
                                <div class="row" id="content-warning-store-not-selected">
                                    <div class="col-md-12">
                                        <div class="alert alert-warning alert-dismissible">
                                            <h5><i class="icon fas fa-exclamation-triangle"></i> Atenção!</h5>
                                            Selecione uma loja para carregar os dados de cadastro
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="row d-flex justify-content-between flex-wrap">
                                <h4 class="text-primary">Informações Automóvel</h4>
                                <div class="col-md-4 text-right">
                                    <label for="active">Ativo</label></br>
                                    <input type="checkbox" data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="SIM"  data-off-text="NÃO" name="active" id="active" value="1" {{ old('active', 1) == 1 ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="autos">Tipo Automóvel</label>
                                        <select class="form-control select2" id="autos" name="autos" title="Por favor, selecione um tipo de automóvel para continua." required disabled>
                                            @foreach($dataAuto->controlAutos as $control)
                                                <option value="{{ $control->code_str }}">{{ $control->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="marcas">Marca do Automóvel</label>
                                        <select class="form-control select2" id="marcas" name="marcas" title="Por favor, selecione uma marca do automóvel para continua." required>
                                            <option value="">Selecione um tipo</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Modelo do Automóvel</label>
                                        <select class="form-control select2" id="modelos" name="modelos" title="Por favor, selecione um modelo do automóvel para continua." required>
                                            <option value="">Selecione uma marca</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="anos">Ano do Automóvel</label>
                                        <select class="form-control select2" id="anos" name="anos" title="Por favor, selecione um ano do automóvel para continua." required>
                                            <option value="">Selecione um modelo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="vlrFipe">Valor Tabela FIPE Hoje</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                  <strong>R$</strong>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control" id="vlrFipe" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Valor Automóvel</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                  <strong>R$</strong>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control" id="valor" name="valor" value="{{ old('valor') }}" title="Por favor, informe um valor para o automóvel para continua.">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="destaque">Destaque</label>
                                        <select class="form-control" name="destaque" id="destaque">
                                            <option value="0" {{ old('destaque') == 0 ? 'selected' : '' }}>Não</option>
                                            <option value="1" {{ old('destaque') == 1 ? 'selected' : '' }}>Sim</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Quilometragem:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-tachometer-alt"></i></span>
                                            </div>
                                            <input type="text" class="form-control" id="quilometragem" name="quilometragem" value="{{ old('quilometragem') }}" title="Por favor, informe a quilometragem do automóvel para continua.">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Cor do Automóvel</label>
                                        <select class="form-control select2" name="cor" id="cor" title="Por favor, selecione uma cor do automóvel para continua.">
                                            <option value="">SELECIONE</option>
                                            @foreach($dataAuto->colors as $color)
                                                <option value="{{ $color->id }}" {{ old('cor') == $color->id ? 'selected' : '' }}>{{ $color->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Único Dono</label>
                                        <select class="form-control" name="unicoDono" title="Por favor, selecione se o automóvel é de único dono ou não para continua.">
                                            <option value="">SELECIONE</option>
                                            <option value="1" {{ old('unicoDono') == '1' ? 'selected' : '' }}>Sim</option>
                                            <option value="0" {{ old('unicoDono') == '0' ? 'selected' : '' }}>Não</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Aceita Trocas</label>
                                        <select class="form-control" name="aceitaTroca" title="Por favor, selecione se o  automóvel permite trocas ou não para continua.">
                                            <option value="" selected="selected">SELECIONE</option>
                                            <option value="1" {{ old('aceitaTroca') == '1' ? 'selected' : '' }}>Sim</option>
                                            <option value="0" {{ old('aceitaTroca') == '0' ? 'selected' : '' }}>Não</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="d-flex justify-content-between">Placa <small class="text-danger">Mostrará: A*****9</small></label>
                                        <input type="text" class="form-control" id="placa" name="placa" value="{{ old('placa') }}" title="Por favor, informe a placa do automóvel para continua.">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="reference">Referência</label>
                                        <input type="text" class="form-control" id="reference" name="reference" value="{{ old('reference') }}" title="Referência do automóvel.">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fuel">Combustível</label>
                                        <select class="form-control select2" name="fuel" title="Por favor, selecione o tipo de combustível do autmóvel.">
                                            @foreach($dataAuto->dataFuels as $fuel)
                                                <option value="{{ $fuel->id }}" {{ old('cor') == $fuel->id ? 'selected' : '' }}>{{ $fuel->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="observation">Observação</label>
                                    <textarea id="observation" name="observation">{{ old('observation') }}</textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <h4 class="text-primary">Informações Complementares do Veículo</h4>
                            </div>
                            <div class="row" id="complements">
                                <div class="col-md-12 text-center mt-3 mb-2">
                                    <h5><i class="fas fa-exclamation-triangle"></i> Selecione um tipo de veículo para informar os dados complementares!</h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <h4 class="text-primary">Opcionais</h4>
                            </div>
                            <div class="row" id="optional">
                                <div class="col-md-12 text-center mt-3 mb-2">
                                    <h5><i class="fas fa-exclamation-triangle"></i> Selecione um tipo de veículo para informar os opcionais!</h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <h4 class="text-primary">Estado Financeiro</h4>
                            </div>
                            <div class="row" id="financialStatus">
                                <div class="col-md-12 text-center mt-3 mb-2">
                                    <h5><i class="fas fa-exclamation-triangle"></i> Selecione um tipo de veículo para informar o estado financeiro!</h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <hr>
                                </div>
                            </div>
                                <div class="row">
                                    <h4 class="no-padding text-primary col-md-12">Imagens do Automóvel</h4>
                                    <small class="no-padding col-md-12">A primeira imagem será a principal do anúncio. Proporção de 4:3. Até 20 imagens</small>
                                </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="file"
                                               class="filepond"
                                               name="filepond"
                                               multiple>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between flex-wrap">
                            <a href="{{ route('admin.automoveis.listagem') }}" class="btn btn-primary col-md-3"><i class="fa fa-arrow-left"></i> Voltar</a>
                            <button class="btn btn-success col-md-3" id="btnCadastrar"><i class="fa fa-save"></i> Cadastrar</button>
                        </div>
                        <input type="hidden" name="codeFipe" id="codeFipe"/>
                        <input type="hidden" name="path-file-image" value="{{ uniqid() }}"/>
                        <input type="hidden" name="order-file-image"/>
                        {!! csrf_field() !!}
                    </form>
                </div>
            </div>
        </div>
@stop
@section('js_head')
@endsection
@section('js')
    <!-- include FilePond library -->
    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>

    <!-- include FilePond plugins -->
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-metadata/dist/filepond-plugin-file-metadata.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-poster/dist/filepond-plugin-file-poster.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.js"></script>


    <!-- include FilePond jQuery adapter -->
    <script src="https://unpkg.com/jquery-filepond/filepond.jquery.js"></script>

    <script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/jquery-image-uploader/src/image-uploader.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/jquery-validation/dist/jquery.validate.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/ckeditor4/ckeditor.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/ckeditor4/config.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/dist/js/pages/automovel/automovel.js') }}"></script>
@endsection
@section('css_pre')
    <link rel="stylesheet" href="https://unpkg.com/filepond/dist/filepond.min.css">
    <link rel="stylesheet" href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css">
    <link rel="stylesheet" href="https://unpkg.com/filepond-plugin-file-poster/dist/filepond-plugin-file-poster.css">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/jquery-image-uploader/src/image-uploader.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
