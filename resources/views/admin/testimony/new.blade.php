{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Cadastrar Depoimento', 'no-active' => [['route' => 'admin.testimony.index', 'name' => 'Listagem de Depoimentos']]]])
{{-- Título da página --}}
@section('title', 'Cadastrar Depoimento')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="error-form alert alert-warning {{ count($errors) == 0 ? 'display-none' : '' }}">
                <h5>Existem erros no envio do formulário, veja abaixo para corrigi-los.</h5>
                <ol>
                    @foreach ($errors->all() as $error)
                        <li><label id="name-error" class="error">{{ $error }}</label></li>
                    @endforeach
                </ol>
            </div>

            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Perfil</h3>
                </div>
                <form action="{{ route('admin.testimony.insert') }}" enctype="multipart/form-data" method="POST">
                    <div class="card-body">
                        <div class="row @if (count($stores) === 1) d-none @endif">
                            <div class="col-md-12 form-group">
                                <label for="autos">Loja</label>
                                <select class="form-control select2" id="stores" name="stores" required>
                                    @if (count($stores) > 1)
                                        <option value="0">Selecione uma Loja</option>
                                    @endif
                                    @foreach ($stores as $store)
                                        <option value="{{ $store->id }}" {{ old() ? (old('stores') == $store->id ? 'selected' : '') : '' }}>{{ $store->store_fancy }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-8">
                                <label for="name">Nome</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name')  }}" placeholder="Digite o nome de quem irá depor" required>
                            </div>
                            <div class="form-group col-md-4 text-center">
                                <label for="name">Avaliação de 1 à 5</label>

                                <div class="star-rating">
                                    <label>
                                        <input type="radio" name="rate" value="1"/>
                                        <i class="fa"></i>
                                    </label>
                                    <label>
                                        <input type="radio" name="rate" value="2"/>
                                        <i class="fa"></i>
                                    </label>
                                    <label>
                                        <input type="radio" name="rate" value="3"/>
                                        <i class="fa"></i>
                                    </label>
                                    <label>
                                        <input type="radio" name="rate" value="4"/>
                                        <i class="fa"></i>
                                    </label>
                                    <label>
                                        <input type="radio" name="rate" value="5"/>
                                        <i class="fa"></i>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-2">
                                <label for="width">Ativo</label></br>
                                <input type="checkbox" data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="SIM"  data-off-text="NÃO"  name="active" value="1">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="width">Primário</label></br>
                                <input type="checkbox" data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="SIM"  data-off-text="NÃO"  name="primary" value="1">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="name">Adicionar Logo</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="picture" name="picture" required>
                                        <label class="custom-file-label" for="exampleInputFile">Alterar</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4 text-center">
                                <label for="name">Visualização Logo</label><br>
                                <img src="{{ asset("assets/admin/dist/images/testimony/noimage.png") }}" id="viewLogo" width="100" height="100">
                            </div>
                        </div>
                        <div class="row">

                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="description">Descrição do Depoimento</label>
                                <div class="mb-3">
                                    <textarea class="form-control" rows="5" id="testimony" name="testimony" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-between">
                                <a href="{{ route('admin.testimony.index') }}" class="btn btn-danger col-md-3"><i class="fas fa-arrow-left"></i> Voltar</a>
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
    <style>
        #viewLogo{
            border-radius: 50%;
        }
        div[class^="star-rating"] label {
            cursor:pointer;
        }
        div[class^="star-rating"] label input{
            display:none;
        }
        div[class^="star-rating"] label i {
            font-size:25px;
            -webkit-transition-property:color, text;
            -webkit-transition-duration: .2s, .2s;
            -webkit-transition-timing-function: linear, ease-in;
            -moz-transition-property:color, text;
            -moz-transition-duration:.2s;
            -moz-transition-timing-function: linear, ease-in;
            -o-transition-property:color, text;
            -o-transition-duration:.2s;
            -o-transition-timing-function: linear, ease-in;
        }
        div[class^="star-rating"] label i:before {
            content:'\f005';
        }
        div[class^="star-rating"] label i.active {
            color:gold;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('assets/admin/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
    <script>
        $(function () {
            $("input[data-bootstrap-switch]").each(function() {
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            });
            $('input[name="percentage"]').mask('#.##0,00', {
                reverse: true
            });

        });
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewLogo').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }

        $("#picture").change(function() {
            readURL(this);
        });
        $('div[class^="star-rating"] label i.fa').on('click mouseover',function() {
            // remove classe ativa de todas as estrelas
            const el = $(this).closest('div[class^="star-rating"]');
            el.find('label i.fa').removeClass('active');
            // pegar o valor do input da estrela clicada
            var val = $(this).prev('input').val();
            //percorrer todas as estrelas
            el.find('label i.fa').each(function() {
                /* checar de o valor clicado é menor ou igual do input atual
                *  se sim, adicionar classe active
                */
                var $input = $(this).prev('input');
                if ($input.val() <= val) {
                    $(this).addClass('active');
                }
            });
        });
        //Ao sair da div star-rating
        $('div[class^="star-rating"]').mouseleave(function() {
            //pegar o valor clicado
            const el = $(this).closest('div[class^="star-rating"]');
            var val = $(this).find('input:checked').val();
            //se nenhum foi clicado remover classe de todos
            if (val == undefined ) {
                el.find('label i.fa').removeClass('active');
            } else {
                //percorrer todas as estrelas
                el.find('label i.fa').each(function() {
                    /* Testar o input atual do laço com o valor clicado
                    *  se maior, remover classe, senão adicionar classe
                    */
                    var $input = $(this).prev('input');
                    if ($input.val() > val) {
                        $(this).removeClass('active');
                    } else {
                        $(this).addClass('active');
                    }
                });
            }
        });
    </script>
@stop
