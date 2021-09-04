@extends('user.template.page')

{{-- set title --}}
@section('title', 'Início')

{{-- import css --}}
@section('css')
@stop

{{-- import css pre --}}
@section('css_pre')
@stop

{{-- import js header --}}
@section('js_head')
@stop

{{-- import js footer --}}
@section('js')
    <script>
        $(document).ready(function () {
            getAutosRelated($('#previewListRelated'), parseInt(window.location.pathname.split('/').pop()), 6);
        });
    </script>
@stop

@section('body')
    <div class="car-details-page content-area-6">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-12 col-xs-12">
                    <div class="car-details-section">
                        <div class="heading-car clearfix">
                            <div class="pull-left">
                                <h2>{{ $dataAuto['auto']['modelo_nome'] }}</h2>
                            </div>
                            <div class="pull-right">
                                <div class="price-box-3"><sup>R$</sup>{{ $dataAuto['auto']['valor'] }}</div>
                            </div>
                        </div>
                        <div id="carDetailsSlider" class="carousel car-details-sliders slide slide-2">
                            <div class="carousel-inner">
                                @foreach($dataAuto['images'] as $keyImage => $image)
                                <div class="item carousel-item {{ $keyImage === 0 ? 'active' : '' }}" data-slide-number="{{ $keyImage }}">
                                    <img src="{{ asset("assets/admin/dist/images/autos/{$dataAuto['auto']['type_auto']}/{$dataAuto['auto']['auto_id']}/{$image->arquivo}") }}" class="img-fluid" alt="slider-car">
                                </div>
                                @endforeach
                            </div>
                            <ul class="carousel-indicators car-properties list-inline nav nav-justified">
                                @foreach($dataAuto['images'] as $keyImage => $image)
                                <li class="list-inline-item {{ $keyImage === 0 ? 'active' : '' }}">
                                    <a id="carousel-selector-{{ $keyImage }}" class="selected" data-slide-to="{{ $keyImage }}" data-target="#carDetailsSlider">
                                        <img src="{{ asset("assets/admin/dist/images/autos/{$dataAuto['auto']['type_auto']}/{$dataAuto['auto']['auto_id']}/thumbnail_{$image->arquivo}") }}" class="img-fluid" alt="small-car">
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="tabbing tabbing-box mb-40">
                            <div class="tab-content" id="carTabContent">
                                <div class="tab-pane fade active show" id="one" role="tabpanel" aria-labelledby="one-tab">
                                    <div class="car-amenities mb-30">
                                        <h3 class="heading-2">Opcionais e Acessórios</h3>
                                        <div class="row">
                                            @for($countOpAll = 0; $countOpAll < 3; $countOpAll++)
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                    <ul class="amenities">
                                                        @for($countOp = (ceil(count($dataAuto['optional'])/3) * $countOpAll); $countOp < (ceil(count($dataAuto['optional'])/3) * ($countOpAll + 1)); $countOp++)
                                                            @if(isset($dataAuto['optional'][$countOp]))
                                                                <li>
                                                                    <i class="fa fa-check"></i>{{ $dataAuto['optional'][$countOp]['name'] }}
                                                                </li>
                                                            @endif
                                                        @endfor
                                                    </ul>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="car-description mb-50">
                                        <h3 class="heading-2">
                                            Observações
                                        </h3>
                                        {!! $dataAuto['auto']['observation'] !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="related-cars">
                            <h3 class="heading-2">Confira outros veículos</h3>
                            <div class="slick-slider-area clearfix">
                                <div class="row" id="previewListRelated" data-slick='{"slidesToShow": 3, "responsive":[{"breakpoint": 1024,"settings":{"slidesToShow": 2}}, {"breakpoint": 768,"settings":{"slidesToShow": 1}}]}'></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="sidebar-right">
                        <div class="widget advanced-search d-none-992">
                            <ul>
                                @if(!empty($dataAuto['auto']['reference']))
                                <li>
                                    <span><strong>Referência</strong></span><strong>{{ $dataAuto['auto']['reference'] }}</strong>
                                </li>
                                @endif
                                <li>
                                    <span>Marca</span>{{ $dataAuto['auto']['marca_nome'] }}
                                </li>
                                <li>
                                    <span>Modelo</span>{{ $dataAuto['auto']['modelo_nome'] }}
                                </li>
                                <li>
                                    <span>Ano</span>{{ $dataAuto['auto']['ano_nome'] }}
                                </li>
                                <li>
                                    <span>Cor</span>{{ $dataAuto['auto']['cor'] }}
                                </li>
                                <li>
                                    <span>Quilometragem</span>{{ $dataAuto['auto']['kms'] }}
                                </li>
                                <li>
                                    <span>Placa</span>{{ $dataAuto['auto']['placa']  }}
                                </li>
                                <li>
                                    <span>Único Dono</span>{{ $dataAuto['auto']['only_owner'] }}
                                </li>
                                <li>
                                    <span>Aceita Troca</span>{{ $dataAuto['auto']['accept_exchange'] }}
                                </li>
                                @foreach($dataAuto['complement'] as $complement)
                                    <li>
                                        <span>{{ $complement['name'] }}</span>{{ $complement['value'] }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="widget question">
                            <div class="col-md-12 p-0">
                                <a href="https://api.whatsapp.com/send?phone=55{{ $settings->storePhonePrimary_n }}&text=Olá,%0DGostaria de mais informações sobre o automóvel%0D%0D-{{ $dataAuto['auto']['marca_nome'] }}%0D-{{ $dataAuto['auto']['modelo_nome'] }}%0D-{{ $dataAuto['auto']['ano_nome'] }}%0D-{{ $dataAuto['auto']['cor'] }}%0D%0D{{ route('user.auto.preview', ['auto' => $dataAuto['auto']['auto_id']]) }}" target="_blank" class="btn-5 text-center p-1 pt-2 pb-2"><i class="fab fa-whatsapp"></i> Fale com um vendedor no WhatsApp</a>
                            </div>

                        </div>
                        <div class="widget question">
                            <h5 class="sidebar-title">Ficou interessado?</h5>
                            <h7 class="main-title">Preencha seus dados e contate o vendedor</h7>
                            <form action="{{ route('ajax.contact.sendMessage') }}" method="POST" enctype="multipart/form-data" id="sendMessageContact" class="mt-3">
                                <div class="shop-table">
                                    <div class="form-group name col-md-12">
                                        <input type="text" name="name" class="form-control" placeholder="Nome" required>
                                    </div>
                                    <div class="form-group email col-md-12">
                                        <input type="email" name="email" class="form-control" placeholder="E-mail" required>
                                    </div>
                                    <div class="form-group number col-md-12">
                                        <input type="text" name="phone" class="form-control" placeholder="Telefone" required>
                                    </div>
                                    <div class="form-group subject col-md-12">
                                        <input type="text" name="subject" class="form-control" placeholder="Assunto" required>
                                    </div>
                                    <div class="form-group message col-md-12">
                                        <textarea class="form-control" name="message" placeholder="Mensagem" required></textarea>
                                    </div>
                                    <div class="send-btn col-md-12">
                                        <button type="submit" class="btn btn-5 col-md-12">Enviar Mensagem</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Featured car start -->
@stop
