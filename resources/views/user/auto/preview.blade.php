@extends('user.template.page')

{{-- set title --}}
@section('title', 'Início')

{{-- import css --}}
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css" integrity="sha512-17EgCFERpgZKcm0j0fEq1YCJuyAWdz9KUtv1EjVuaOz8pDnh/0nZxmU6BBXwaaxqoi9PQXnRWqlcDB027hgv9A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/slick-fullscreen/slick-fullscreen.css') }}"/>
    <style>
        #carDetailsSliderFor .slick-slide img,
        #carDetailsSliderNav .slick-slide img {
            border: 5px solid #fff;
            display: block;
            width: 100%;
        }
        #carDetailsSliderFor .slick-slide img.slick-loading,
        #carDetailsSliderNav .slick-slide img.slick-loading {
            border: 0;
        }
        .slick-slider {
            margin: 0 auto 10px;
        }
        .slick-slider
        {
            position: relative;

            display: block;
            box-sizing: border-box;

            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;

            -webkit-touch-callout: none;
            -khtml-user-select: none;
            -ms-touch-action: pan-y;
            touch-action: pan-y;
            -webkit-tap-highlight-color: transparent;
        }

        .slider-nav .slick-slide {
            opacity: .5;
            cursor: pointer;
        }

        .slick-list
        {
            position: relative;

            display: block;
            overflow: hidden;

            margin: 0;
            padding: 0;
        }
        .slider-nav .slick-list {
            width: 93%;
        }
        .slick-list:focus
        {
            outline: none;
        }
        .slick-list.dragging
        {
            cursor: pointer;
            cursor: hand;
        }

        .slick-slider .slick-track,
        .slick-slider .slick-list
        {
            -webkit-transform: translate3d(0, 0, 0);
            -moz-transform: translate3d(0, 0, 0);
            -ms-transform: translate3d(0, 0, 0);
            -o-transform: translate3d(0, 0, 0);
            transform: translate3d(0, 0, 0);
        }

        .slick-track
        {
            position: relative;
            top: 0;
            left: 0;

            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .slick-track:before,
        .slick-track:after
        {
            display: table;

            content: '';
        }
        .slick-track:after
        {
            clear: both;
        }
        .slick-loading .slick-track
        {
            visibility: hidden;
        }

        .slick-slide
        {
            display: none;
            float: left;

            height: 100%;
            min-height: 1px;
        }
        [dir='rtl'] .slick-slide
        {
            float: right;
        }
        .slick-slide img
        {
            display: block;
        }
        .slick-slide.slick-loading img
        {
            display: none;
        }
        .slick-slide.dragging img
        {
            pointer-events: none;
        }
        .slick-initialized .slick-slide
        {
            display: block;
        }
        .slick-loading .slick-slide
        {
            visibility: hidden;
        }
        .slick-vertical .slick-slide
        {
            display: block;

            height: auto;

            border: 1px solid transparent;
        }
        .slick-arrow.slick-hidden {
            display: none;
        }
        .slick-next:before {
            font-family: 'Font Awesome 5 Free';
            content: "\f0a9";
            font-weight: 900;
            color: black;
        }

        .slick-prev:before {
            font-family: 'Font Awesome 5 Free';
            content: "\f0a8";
            font-weight: 900;
            color: black;
        }
        .slick-next {
            right: -3px;
        }
        .slick-prev {
            left: -3px;
        }
        .slick-active.slick-center {
            opacity: 1;
        }

        #viewImagesAuto .slick-slider {
            margin: 0 5px;
        }
        #viewImagesAuto .slick-slide img {
            border: none
        }

    </style>
@stop

{{-- import css pre --}}
@section('css_pre')
@stop

{{-- import js header --}}
@section('js_head')
@stop

{{-- import js footer --}}
@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js" integrity="sha512-XtmMtDEcNz2j7ekrtHvOVR4iwwaD6o/FUJe6+Zq+HgcCsk3kj4uSQQR8weQ2QVj1o0Pk6PwYLohm206ZzNfubg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-zoom/1.6.1/jquery.zoom.min.js" integrity="sha512-xhvWWTTHpLC+d+TEOSX2N0V4Se1989D03qp9ByRsiQsYcdKmQhQ8fsSTX3KLlzs0jF4dPmq0nIzvEc3jdYqKkw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.5.0/highlight.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.5.0/languages/xml.min.js"></script>
    <script src="{{ asset('assets/admin/plugins/slick-fullscreen/slick-fullscreen.js') }}"></script>
    <script>hljs.initHighlightingOnLoad();</script>
    <script>
        $(document).ready(function () {
            getAutosRelated($('#previewListRelated'), parseInt(window.location.pathname.split('/').pop()), 6);

            $('.slider-for').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: false,
                fade: true,
                asNavFor: '.slider-nav'
            });
            $('.slider-nav').slick({
                slidesToShow: 3,
                slidesToScroll: 1,
                asNavFor: '.slider-for',
                centerMode: true,
                focusOnSelect: true
            });
        });


        // ZOOM
        $('.image-slider').zoom();

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

                        <section id="carDetailsSliderFor" class="slider-for"  data-slick-fullscreen>
                            @foreach($dataAuto['images'] as $keyImage => $image)
                                <a href="{{ asset("assets/admin/dist/images/autos/{$image->folder}/{$image->arquivo}") }}" class="image-slider">
                                    <img src="{{ asset("assets/admin/dist/images/autos/{$image->folder}/{$image->arquivo}") }}">
                                </a>
                            @endforeach
                        </section>
                        <div id="carDetailsSliderNav" class="slider-nav d-flex justify-content-center">
                            @foreach($dataAuto['images'] as $keyImage => $image)
                                <div>
                                    <img src="{{ asset("assets/admin/dist/images/autos/{$image->folder}/thumbnail_{$image->arquivo}") }}">
                                </div>
                            @endforeach
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
                                    <span>Combustível</span>{{ $dataAuto['auto']['fuel']  }}
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
                                <a href="https://api.whatsapp.com/send?phone=55{{ $settings->storePhonePrimary_n }}&text=Olá,%0DGostaria de mais informações sobre o automóvel%0D%0D-{{ $dataAuto['auto']['marca_nome'] }}%0D-{{ $dataAuto['auto']['modelo_nome'] }}%0D-{{ $dataAuto['auto']['ano_nome'] }}%0D-{{ $dataAuto['auto']['cor'] }}%0D%0D{{ route('user.auto.preview', ['auto' => $dataAuto['auto']['auto_id']]) }}" target="_blank" class="text-center p-1 pt-2 pb-2 btn-whatsapp"><i class="fab fa-whatsapp"></i> Fale com um vendedor no WhatsApp</a>
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

    <div class="modal fade" id="viewImagesAuto" aria-hidden="true" style="z-index: 9999">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ $dataAuto['auto']['modelo_nome'] }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center col-md-12 no-padding"></div>
            </div>
        </div>
    </div>
@stop
