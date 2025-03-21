{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Alterar Automóvel', 'no-active' => [['route' => 'admin.automobiles.index', 'name' => 'Listagem Automóveis']], 'route_back_page' => 'admin.automobiles.index']])
{{-- Título da página --}}
@section('title', 'Alterar Automóvel')

@section('content')
    @if (session('message'))
        <div class="alert {{ session('typeMessage') === 'success' ? 'alert-success' : 'alert-warning' }}">
            <p>{{ session('message') }}</p>
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="overlay dark">
                    <i class="fas fa-3x fa-spinner fa-spin"></i>
                </div>
                <div class="card-header">
                    <h3 class="card-title">Alterar Automóvel</h3><br/>
                    <small>Altere um automóvel já cadastrado no sistema</small>
                </div>
                <form action="{{ route('admin.automobiles.cadastro.update') }}" enctype="multipart/form-data" id="formAlteraAutos" method="POST">
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
                        <div class="row @if (count($dataAuto->stores) === 1) d-none @endif">
                            <h4 class="text-primary">Loja para atualização</h4>
                        </div>
                        <div class="row @if (count($dataAuto->stores) === 1) d-none @endif">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="autos">Loja</label>
                                    <select class="form-control select2" id="stores" name="stores" title="Por favor, selecione uma loja." required>
                                        @foreach ($dataAuto->stores as $store)
                                            <option value="{{ $store->id }}" @if ($store->id == $dataAuto->storeSelected) selected @endif>{{ $store->store_fancy }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <h4 class="text-primary">Informações Automóvel</h4>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Tipo Automóvel</label>
                                    <select class="form-control select2" id="autos" name="autos" title="Por favor, selecione um tipo de automóvel para continua." required disabled>
                                        <option value="" disabled>SELECIONE</option>
                                        @foreach ($dataAuto->controlAutos as $control)
                                            <option value="{{ $control->code_str }}" {{ old('autos', $dataAuto->tipoAuto) == $control->code_str ? 'selected' : ''}}>{{ $control->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Marca do Automóvel</label>
                                    <select class="form-control select2" id="marcas" name="marcas" title="Por favor, selecione uma marca do automóvel para continua." required>
                                        <option value="" disabled>Selecione um tipo</option>
                                        @foreach ($dataAuto->brandsFipe as $brand)
                                            <option value="{{ $brand->id }}" {{ old('marcas', $dataAuto->idMarca) == $brand->id ? 'selected' : ''}}>{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Modelo do Automóvel</label>
                                    <select class="form-control select2" id="modelos" name="modelos" title="Por favor, selecione um modelo do automóvel para continua." required>
                                        <option value="" disabled>Selecione a marca</option>
                                        @foreach ($dataAuto->modelsFipe as $model)
                                            <option value="{{ $model->id }}" {{ old('modelos', $dataAuto->idModelo) == $model->id ? 'selected' : ''}}>{{ $model->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Ano do Automóvel</label>
                                    <select class="form-control select2" id="anos" name="anos" title="Por favor, selecione um ano do automóvel para continua." required>
                                        <option value="" disabled>Selecione o modelo</option>
                                        @foreach($dataAuto->yearsFipe as $year)
                                            <option value="{{ $year->id }}" {{ old('anos', $dataAuto->idAno) == $year->id ? 'selected' : ''}}>{{ $year->name }}</option>
                                        @endforeach
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
                                        <input type="text" class="form-control" id="vlrFipe" name="vlrAutoFipe" value="{{ old('vlrAutoFipe', $dataAuto->autoFipe->value) }}" disabled>
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
                                        <input type="text" class="form-control" id="valor" name="valor" value="{{ old('valor', $dataAuto->valor ?? '') }}" autocomplete="off" title="Por favor, informe um valor para o automóvel para continua.">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Quilometragem</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-tachometer-alt"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="quilometragem" name="quilometragem" value="{{ old('quilometragem', isset($dataAuto) ? $dataAuto->kms : '') }}" autocomplete="off" title="Por favor, informe a quilometragem do automóvel para continua.">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Cor do Automóvel</label>
                                    <div class="input-group d-flex flex-nowrap">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-palette"></i></span>
                                        </div>
                                        <select class="form-control select2" name="cor" id="cor" title="Por favor, selecione uma cor do automóvel para continua.">
                                            <option value="" {{ old('cor') ? '' : 'selected' }}>Selecione a loja</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fuel">Combustível</label>
                                    <div class="input-group d-flex flex-nowrap">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-gas-pump"></i></span>
                                        </div>
                                        <select class="form-control select2" name="fuel" title="Por favor, selecione o tipo de combustível do autmóvel.">
                                            @foreach($dataAuto->dataFuels as $fuel)
                                                <option value="{{ $fuel->id }}" {{ old('fuel', $dataAuto->fuel) == $fuel->id ? 'selected' : '' }}>{{ $fuel->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="d-flex justify-content-between">Placa <small class="text-danger">Mostrará: A*****9</small></label>
                                    <input type="text" class="form-control" id="placa" name="placa" value="{{ old('placa', isset($dataAuto) ? $dataAuto->placa : '') }}" autocomplete="off" title="Por favor, informe a placa do automóvel para continua.">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="reference">Referência</label>
                                    <input type="text" class="form-control" id="reference" name="reference" value="{{ old('reference', isset($dataAuto) ? $dataAuto->reference : '') }}" autocomplete="off" title="Referência do automóvel.">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="destaque">Em destaque</label><br/>
                                    <input type="checkbox" data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="SIM"  data-off-text="NÃO" name="destaque" id="destaque" value="1" {{ old('destaque', $dataAuto->destaque) == 1 ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="unicoDono">Único Dono</label><br/>
                                    <input type="checkbox" data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="SIM"  data-off-text="NÃO" name="unicoDono" id="unicoDono" value="1" {{ old('unicoDono', $dataAuto->unicoDono) == 1 ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="aceitaTroca">Aceita Trocas</label><br/>
                                    <input type="checkbox" data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="SIM"  data-off-text="NÃO" name="aceitaTroca" id="aceitaTroca" value="1" {{ old('aceitaTroca', $dataAuto->aceitaTroca) == 1 ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="active">Ativo</label></br>
                                    <input type="checkbox" data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="SIM"  data-off-text="NÃO" name="active" id="active" value="1" {{ old('active', $dataAuto->active) == 1 ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="observation">Observação</label>
                                <textarea id="observation" name="observation">{{ old('observation', isset($dataAuto) ? $dataAuto->observation : '') }}</textarea>
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
                                <h5><i class="fa fa-spinner fa-spin"></i> Carregando </h5>
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
                                <h5><i class="fa fa-spinner fa-spin"></i> Carregando </h5>
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
                                <h5><i class="fa fa-spinner fa-spin"></i> Carregando </h5>
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
                        <a href="{{ route('admin.automobiles.index') }}" class="btn btn-primary col-md-3"><i class="fa fa-arrow-left"></i> Voltar</a>
                        <button class="btn btn-success col-md-3" id="btnCadastrar" disabled><i class="fa fa-save"></i> Atualizar</button>
                    </div>

                    <input type="hidden" name="idTipoAutomovel" value="{{$dataAuto->tipoAuto}}"/>
                    <input type="hidden" name="codeFipe" id="codeFipe" value="{{$dataAuto->code_auto_fipe}}"/>

                    <input type="hidden" name="idAuto" value="{{$dataAuto->codAuto}}"/>
                    <input type="hidden" name="idMarcaAutomovel" value="{{$dataAuto->idMarca}}"/>
                    <input type="hidden" name="idModeloAutomovel" value="{{$dataAuto->idModelo}}"/>
                    <input type="hidden" name="idAnoAutomovel" value="{{$dataAuto->idAno}}"/>
                    <input type="hidden" name="path-file-image" value="{{ $dataAuto->folder_images }}"/>
                    <input type="hidden" name="idColor" value="{{ old('cor', $dataAuto->cor) }}"/>
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

    <script type="text/javascript" src="{{ asset('assets/admin/plugins/jquery-image-uploader/src/image-uploader.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/jquery-validation/dist/jquery.validate.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/ckeditor4/ckeditor.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/ckeditor4/config.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/dist/js/pages/automovel/automovel.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/dist/js/pages/fipe/load-brand-model-year.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/dist/js/pages/automovel/alterar.js') }}"></script>
@endsection
@section('css_pre')
    <link rel="stylesheet" href="https://unpkg.com/filepond/dist/filepond.min.css">
    <link rel="stylesheet" href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css">
    <link rel="stylesheet" href="https://unpkg.com/filepond-plugin-file-poster/dist/filepond-plugin-file-poster.css">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/jquery-image-uploader/src/image-uploader.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
