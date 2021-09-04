{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Alterar Automóvel', 'no-active' => [['route' => 'admin.automoveis.listagem', 'name' => 'Listagem Automóveis']]]])
{{-- Título da página --}}
@section('title', 'Alterar Automóvel')

@section('content')
    @if(session('message'))
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
                    <h3 class="card-title">Alterar de Automóvel</h3><br/>
                    <small>Altere um automóvel já cadastrado no sistema</small>
                </div>
                <form action="{{ route('admin.automoveis.cadastro.update') }}" enctype="multipart/form-data" id="formAlteraAutos" method="POST">
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
                            <h4 class="text-primary">Loja para atualização</h4>
                        </div>
                        <div class="row @if(count($dataAuto->stores) === 1) d-none @endif">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="autos">Loja</label>
                                    <select class="form-control select2" id="stores" name="stores" title="Por favor, selecione uma loja." required>
                                        @foreach($dataAuto->stores as $store)
                                            <option value="{{ $store->id }}" @if($store->id == $dataAuto->storeSelected) selected @endif>{{ $store->store_fancy }}</option>
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
                                        <option value="">SELECIONE</option>
                                        <option value="carros">Carro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Marca do Automóvel</label>
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
                                    <label>Ano do Automóvel</label>
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
                                        <input type="text" class="form-control" id="valor" name="valor" value="{{ old('valor') ? old('valor') : isset($dataAuto->valor) ? $dataAuto->valor : '' }}" title="Por favor, informe um valor para o automóvel para continua.">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Destaque</label>
                                    <select class="form-control" name="destaque" id="destaque" >
                                        <option value="0" {{ old() ? (old('destaque') == 0 ? 'selected' : '') : ($dataAuto->destaque == 0 ? 'selected' : '') }}>Não</option>
                                        <option value="1" {{ old() ? (old('destaque') == 1 ? 'selected' : '') : ($dataAuto->destaque == 1 ? 'selected' : '') }}>Sim</option>
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
                                        <input type="text" class="form-control" id="quilometragem" name="quilometragem" value="{{ old('quilometragem') ? old('quilometragem') : isset($dataAuto) ? $dataAuto->kms : '' }}" title="Por favor, informe a quilometragem do automóvel para continua.">
                                    </div>
                                    <!-- /.input group -->
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
                                            <option value="{{ $color->id }}" {{ old() ? (old('cor') == $color->id ? 'selected' : '') : ($dataAuto->cor == $color->id ? 'selected' : '') }}>{{ $color->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Único Dono</label>
                                    <select class="form-control" name="unicoDono" title="Por favor, selecione se o automóvel é de único dono ou não para continua.">
                                        <option value="">SELECIONE</option>
                                        <option value="1" {{ old('unicoDono') == '1' || $dataAuto->unicoDono == '1' ? 'selected' : '' }}>Sim</option>
                                        <option value="0" {{ old('unicoDono') == '0' || $dataAuto->unicoDono == '0' ? 'selected' : '' }}>Não</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Aceita Trocas</label>
                                    <select class="form-control" name="aceitaTroca" title="Por favor, selecione se o  automóvel permite trocas ou não para continua.">
                                        <option value="" selected="selected">SELECIONE</option>
                                        <option value="1" {{ old('aceitaTroca') == '1' || $dataAuto->aceitaTroca == '1' ? 'selected' : '' }}>Sim</option>
                                        <option value="0" {{ old('aceitaTroca') == '0' || $dataAuto->aceitaTroca == '0' ? 'selected' : '' }}>Não</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Placa</label>
                                    <input type="text" class="form-control" id="placa" name="placa" value="{{ old('placa') ? old('placa') : isset($dataAuto) ? $dataAuto->placa : '' }}" title="Por favor, informe a placa do automóvel para continua.">
                                    <small class="text-danger">Não será divulgado</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="reference">Referência</label>
                                    <input type="text" class="form-control" id="reference" name="reference" value="{{ old('reference') ? old('reference') : isset($dataAuto) ? $dataAuto->reference : '' }}" title="Referência do automóvel.">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="observation">Observação</label>
                                <textarea id="observation" name="observation">{{ old('observation') ? old('observation') : isset($dataAuto) ? $dataAuto->observation : '' }}</textarea>
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
                        <div class="row">
                            @foreach($dataAuto->financials as $financialStatus)
                                <div class="col-md-3">
                                    <div class="form-group clearfix">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" id="{{ "financialStatus_{$financialStatus['id']}" }}" name="{{ "financialStatus_{$financialStatus['id']}" }}" {{ old() ? (old("financialStatus_{$financialStatus['id']}") == 'on' ? 'checked' : '') : ($financialStatus['checked'] ? 'checked' : '') }}>
                                            <label for="{{ "financialStatus_{$financialStatus['id']}" }}">{{ $financialStatus['nome'] }}</label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <h4 class="text-primary">Imagens do Automóvel</h4>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="input-field">
                                        <div class="input-images" style="padding-top: .5rem;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between flex-wrap">
                        <a href="{{ route('admin.automoveis.listagem') }}" class="btn btn-primary col-md-3"><i class="fa fa-arrow-left"></i> Voltar</a>
                        <button class="btn btn-success col-md-3" id="btnCadastrar"><i class="fa fa-save"></i> Atualizar</button>
                    </div>
                    <input type="hidden" name="primaryImage" value="old_{{$dataAuto->primaryKey}}1"/>

                    <input type="hidden" name="idTipoAutomovel" value="{{$dataAuto->tipoAuto}}"/>
                    <input type="hidden" name="codeFipe" id="codeFipe" value="{{$dataAuto->code_auto_fipe}}"/>

                    <input type="hidden" name="idAuto" value="{{$dataAuto->codAuto}}"/>
                    <input type="hidden" name="idMarcaAutomovel" value="{{$dataAuto->idMarca}}"/>
                    <input type="hidden" name="idModeloAutomovel" value="{{$dataAuto->idModelo}}"/>
                    <input type="hidden" name="idAnoAutomovel" value="{{$dataAuto->idAno}}"/>
                    <div class="images-pre">
                        @foreach($dataAuto->imagens as $images)
                            <input type="hidden" value="{{ asset('assets/admin/dist/images/autos/' . $dataAuto->tipoAuto . '/' . $dataAuto->codAuto . '/thumbnail_' . $images->url) }}" img-primary="{{ $images->primary }}" cod-img="{{ $images->cod }}"/>
                        @endforeach
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
    <script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/jquery-image-uploader/src/image-uploader.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/jquery-validation/dist/jquery.validate.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/ckeditor4/ckeditor.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/ckeditor4/config.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/dist/js/pages/automovel/automovel.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/dist/js/pages/automovel/alterar.js') }}"></script>
@endsection
@section('css_pre')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/jquery-image-uploader/src/image-uploader.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
