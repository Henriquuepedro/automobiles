{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Cadastro Automóvel', 'no-active' => [['route' => 'admin.automoveis.listagem', 'name' => 'Listagem Automóveis']]]])
{{-- Título da página --}}
@section('title', 'Listagem Automóveis')

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
                            <div class="row">
                                <h4 class="text-primary">Informações Automóvel</h4>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
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
                                            <option value="">SELECIONE</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Modelo do Automóvel</label>
                                        <select class="form-control select2" id="modelos" name="modelos" title="Por favor, selecione um modelo do automóvel para continua." required>
                                            <option value="">SELECIONE</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Ano do Automóvel</label>
                                        <select class="form-control select2" id="anos" name="anos" title="Por favor, selecione um ano do automóvel para continua." required>
                                            <option value="">SELECIONE</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Valor Tabel FIPE</label>
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
                                <div class="col-md-4">
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
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Cor do Automóvel</label>
                                        <select class="form-control select2" name="cor" id="cor" title="Por favor, selecione uma cor do automóvel para continua.">
                                            <option value="">SELECIONE</option>
                                            <option value="preto"    {{ old('cor') == 'preto'    ? 'selected' : '' }}>Preto</option>
                                            <option value="branco"   {{ old('cor') == 'branco'   ? 'selected' : '' }}>Branco</option>
                                            <option value="prata"    {{ old('cor') == 'prata'    ? 'selected' : '' }}>Prata</option>
                                            <option value="vermelho" {{ old('cor') == 'vermelho' ? 'selected' : '' }}>Vermelho</option>
                                            <option value="cinza"    {{ old('cor') == 'cinza'    ? 'selected' : '' }}>Cinza</option>
                                            <option value="azul"     {{ old('cor') == 'azul'     ? 'selected' : '' }}>Azul</option>
                                            <option value="amarelo"  {{ old('cor') == 'amarelo'  ? 'selected' : '' }}>Amarelo</option>
                                            <option value="verde"    {{ old('cor') == 'verde'    ? 'selected' : '' }}>Verde</option>
                                            <option value="laranja"  {{ old('cor') == 'laranja'  ? 'selected' : '' }}>Laranja</option>
                                            <option value="outra"    {{ old('cor') == 'outra'    ? 'selected' : '' }}>Outra</option>
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
                                        <label>Placa</label>
                                        <input type="text" class="form-control" id="placa" name="placa" value="{{ old('placa') }}" title="Por favor, informe a placa do automóvel para continua.">
                                        <small class="text-danger">Não será divulgado</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Final da Placa</label>
                                        <select class="form-control select2" name="finalPlaca" title="Por favor, selecione o final da placa do automóvel para continua.">
                                            <option value="" selected="selected">SELECIONE</option>
                                            <option value="0" {{ old('finalPlaca') == '0' ? 'selected' : '' }}>0</option>
                                            <option value="1" {{ old('finalPlaca') == '1' ? 'selected' : '' }}>1</option>
                                            <option value="2" {{ old('finalPlaca') == '2' ? 'selected' : '' }}>2</option>
                                            <option value="3" {{ old('finalPlaca') == '3' ? 'selected' : '' }}>3</option>
                                            <option value="4" {{ old('finalPlaca') == '4' ? 'selected' : '' }}>4</option>
                                            <option value="5" {{ old('finalPlaca') == '5' ? 'selected' : '' }}>5</option>
                                            <option value="6" {{ old('finalPlaca') == '6' ? 'selected' : '' }}>6</option>
                                            <option value="7" {{ old('finalPlaca') == '7' ? 'selected' : '' }}>7</option>
                                            <option value="8" {{ old('finalPlaca') == '8' ? 'selected' : '' }}>8</option>
                                            <option value="9" {{ old('finalPlaca') == '9' ? 'selected' : '' }}>9</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Quilometragem:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-tachometer-alt"></i></span>
                                            </div>
                                            <input type="text" class="form-control" id="quilometragem" name="quilometragem" value="{{ old('quilometragem') }}" title="Por favor, informe a quilometragem do automóvel para continua.">
                                        </div>
                                        <!-- /.input group -->
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Câmbio</label>
                                        <select class="form-control select2" name="cambio" title="Por favor, selecione o tipo de câmbio do automóvel para continua.">
                                            <option value="" selected="selected">SELECIONE</option>
                                            <option value="manual"  {{ old('cambio') == 'manual'? 'selected' : '' }}>Manual</option>
                                            <option value="auto"    {{ old('cambio') == 'auto'  ? 'selected' : '' }}>Automático</option>
                                            <option value="semi"    {{ old('cambio') == 'semi'  ? 'selected' : '' }}>Semi-Automático</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Combustível</label>
                                        <select class="form-control select2" name="combustivel" title="Por favor, selecione o tipo de combustível do automóvel para continua.">
                                            <option value="" selected="selected">SELECIONE</option>
                                            <option value="gasolina" {{ old('combustivel') == 'gasolina'    ? 'selected' : '' }}>Gasolina</option>
                                            <option value="alcool"   {{ old('combustivel') == 'alcool'      ? 'selected' : '' }}>Álcool</option>
                                            <option value="flex"     {{ old('combustivel') == 'flex'        ? 'selected' : '' }}>Flex</option>
                                            <option value="gas"      {{ old('combustivel') == 'gas'         ? 'selected' : '' }}>Gás Natural</option>
                                            <option value="diesel"   {{ old('combustivel') == 'diesel'      ? 'selected' : '' }}>Diesel</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Direção</label>
                                        <select class="form-control select2" name="direcao" title="Por favor, selecione o tipo de direção do automóvel para continua.">
                                            <option value="" selected="selected">SELECIONE</option>
                                            <option value="hidraulica"  {{ old('direcao') == 'hidraulica' ? 'selected' : '' }}>Hidráulica</option>
                                            <option value="eletrica"    {{ old('direcao') == 'eletrica' ? 'selected' : '' }}>Elétrica</option>
                                            <option value="mecanica"    {{ old('direcao') == 'mecanica' ? 'selected' : '' }}>Mecânica</option>
                                            <option value="assistida"   {{ old('direcao') == 'assistida' ? 'selected' : '' }}>Assistida</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Potência do Motor</label>
                                        <select class="form-control select2" name="potenciaMotor" title="Por favor, selecione a potência do motor do automóvel para continua.">
                                            <option value="" selected="selected">SELECIONE</option>
                                            <option value="1.0" {{ old('potenciaMotor') == '1.0' ? 'selected' : '' }}>1.0</option>
                                            <option value="1.2" {{ old('potenciaMotor') == '1.2' ? 'selected' : '' }}>1.2</option>
                                            <option value="1.3" {{ old('potenciaMotor') == '1.3' ? 'selected' : '' }}>1.3</option>
                                            <option value="1.4" {{ old('potenciaMotor') == '1.4' ? 'selected' : '' }}>1.4</option>
                                            <option value="1.5" {{ old('potenciaMotor') == '1.5' ? 'selected' : '' }}>1.5</option>
                                            <option value="1.6" {{ old('potenciaMotor') == '1.6' ? 'selected' : '' }}>1.6</option>
                                            <option value="1.7" {{ old('potenciaMotor') == '1.7' ? 'selected' : '' }}>1.7</option>
                                            <option value="1.8" {{ old('potenciaMotor') == '1.8' ? 'selected' : '' }}>1.8</option>
                                            <option value="1.9" {{ old('potenciaMotor') == '1.9' ? 'selected' : '' }}>1.9</option>
                                            <option value="2.0" {{ old('potenciaMotor') == '2.0' ? 'selected' : '' }}>2.0 - 2.9</option>
                                            <option value="3.0" {{ old('potenciaMotor') == '3.0' ? 'selected' : '' }}>3.0 - 3.9</option>
                                            <option value="4.0" {{ old('potenciaMotor') == '4.0' ? 'selected' : '' }}>4.0 ou mais</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tipo Veículo</label>
                                        <select class="form-control select2" name="tipoVeiculo" title="Por favor, selecione o tipo de veículo do automóvel para continua.">
                                            <option value="" selected="selected">SELECIONE</option>
                                            <option value="hatch"       {{ old('tipoVeiculo') == 'hatch'        ? 'selected' : '' }}>Hatch</option>
                                            <option value="seda"        {{ old('tipoVeiculo') == 'seda'         ? 'selected' : '' }}>Sedã</option>
                                            <option value="suv"         {{ old('tipoVeiculo') == 'suv'          ? 'selected' : '' }}>SUV</option>
                                            <option value="van"         {{ old('tipoVeiculo') == 'van'          ? 'selected' : '' }}>Van/Utilitário</option>
                                            <option value="conversivel" {{ old('tipoVeiculo') == 'conversivel'  ? 'selected' : '' }}>Conversível</option>
                                            <option value="pickup"      {{ old('tipoVeiculo') == 'pickup'       ? 'selected' : '' }}>Pick-up</option>
                                            <option value="antigo"      {{ old('tipoVeiculo') == 'antigo'       ? 'selected' : '' }}>Antigo</option>
                                            <option value="buggy"       {{ old('tipoVeiculo') == 'buggy'        ? 'selected' : '' }}>Buggy</option>
                                            <option value="passeio"     {{ old('tipoVeiculo') == 'passeio'      ? 'selected' : '' }}>Passeio</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Portas</label>
                                        <select class="form-control select2" name="portas" title="Por favor, selecione a quantidade de portas do automóvel para continua.">
                                            <option value="" selected="selected">SELECIONE</option>
                                            <option value="2" {{ old('portas') == '2' ? 'selected' : '' }}>2 Portas</option>
                                            <option value="4" {{ old('portas') == '4' ? 'selected' : '' }}>4 Portas</option>
                                        </select>
                                    </div>
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
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group clearfix">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" id="airbag" name="airbag" {{ old('airbag') == 'on' ? 'checked' : '' }}>
                                            <label for="airbag">Air bag</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group clearfix">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" id="alarme" name="alarme" {{ old('alarme') == 'on' ? 'checked' : '' }}>
                                            <label for="alarme">Alarme</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group clearfix">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" id="arcondicionado" name="arcondicionado" {{ old('arcondicionado') == 'on' ? 'checked' : '' }}>
                                            <label for="arcondicionado">Ar Condicionado</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group clearfix">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" id="travaEletrica" name="travaEletrica" {{ old('travaEletrica') == 'on' ? 'checked' : '' }}>
                                            <label for="travaEletrica">Trava Elétrica</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group clearfix">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" id="vidroEletrico" name="vidroEletrico" {{ old('vidroEletrico') == 'on' ? 'checked' : '' }}>
                                            <label for="vidroEletrico">Vidro Elétrico</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group clearfix">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" id="som" name="som" {{ old('som') == 'on' ? 'checked' : '' }}>
                                            <label for="som">Som</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group clearfix">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" id="sensorRe" name="sensorRe" {{ old('sensorRe') == 'on' ? 'checked' : '' }}>
                                            <label for="sensorRe">Sensor de Ré</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group clearfix">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" id="cameraRe" name="cameraRe" {{ old('cameraRe') == 'on' ? 'checked' : '' }}>
                                            <label for="cameraRe">Câmera de Ré</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group clearfix">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" id="blindado" name="blindado" {{ old('blindado') == 'on' ? 'checked' : '' }}>
                                            <label for="blindado">Blindado</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group clearfix">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" id="direcaoHidraulica" name="direcaoHidraulica" {{ old('direcaoHidraulica') == 'on' ? 'checked' : '' }}>
                                            <label for="direcaoHidraulica">Direção Hidráulica</label>
                                        </div>
                                    </div>
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
                                <div class="col-md-3">
                                    <div class="form-group clearfix">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" id="financiado" name="financiado" {{ old('financiado') == 'on' ? 'checked' : '' }}>
                                            <label for="financiado">Financiado</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group clearfix">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" id="comMultas" name="comMultas" {{ old('comMultas') == 'on' ? 'checked' : '' }}>
                                            <label for="comMultas">Com Multas</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group clearfix">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" id="ipvaPago" name="ipvaPago" {{ old('ipvaPago') == 'on' ? 'checked' : '' }}>
                                            <label for="ipvaPago">IPVA Pago</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group clearfix">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" id="leilao" name="leilao" {{ old('leilao') == 'on' ? 'checked' : '' }}>
                                            <label for="leilao">De Leilão</label>
                                        </div>
                                    </div>
                                </div>
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
                        <div class="card-footer">
                            <button class="btn btn-primary pull-right" id="btnCadastrar"><i class="fa fa-save"></i> Cadastrar</button>
                        </div>
                        <input type="hidden" name="marcaTxt" />
                        <input type="hidden" name="modeloTxt" />
                        <input type="hidden" name="anoTxt" />
                        <input type="hidden" name="primaryImage" value="1"/>
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
    <script type="text/javascript" src="{{ asset('admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/plugins/jquery-image-uploader/src/image-uploader.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/plugins/jquery-validation/dist/jquery.validate.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/dist/js/pages/cadastro/automovel.js') }}"></script>
@endsection
@section('css_pre')
    <link rel="stylesheet" href="{{ asset('admin/plugins/jquery-image-uploader/src/image-uploader.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
