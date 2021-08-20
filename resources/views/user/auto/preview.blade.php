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
            initLoad();
        });

        const initLoad = async () => {
            await getFiltersAuto($('.filter-list-autos'));
            await getOptionalsAutos();
            await getAutos();
        }

        const getOptionalsAutos = async () => {
            await $.get(`${window.location.origin}/ajax/opcionais/buscar`, function (optionals) {
                $.each(optionals, function (key, value) {
                    $('#options-content-optionals').append(`
                        <div class="checkbox checkbox-theme checkbox-circle">
                            <input id="checkbox_optional_${value.id}" type="checkbox" value="${value.id}">
                            <label for="checkbox_optional_${value.id}">
                                ${value.name}
                            </label>
                        </div>
                    `);
                });
            });
        }

        const getFiltersStock = async () => {
            let filters = {
                filter: {},
                optionals: []
            }

            filters.order            = $('[name="default-order"]').val();
            filters.filter.text      = $('[name="search-text"]').val();
            filters.filter.brand     = $('[name="select-brand"]').val();
            filters.filter.model     = $('[name="select-make"]').val();
            filters.filter.color     = $('[name="select-color"]').val();
            filters.filter.year      = $('[name="select-year"]').val();
            filters.filter.max_price = $('#range-price-filter [name="max_price"]').length === 0 ? null : $('#range-price-filter [name="max_price"]').val();
            filters.filter.min_price = $('#range-price-filter [name="min_price"]').length === 0 ? null : $('#range-price-filter [name="min_price"]').val();

            await $('#options-content-optionals input[type="checkbox"]').each(function(){
                if ($(this).is(':checked'))
                    filters.optionals.push($(this).val());
            });

            console.log(filters);

            return filters;
        }

        const getAutos = async () => {

            await loadingContent();

            const filters = await getFiltersStock();

            await $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: `${window.location.origin}/ajax/automoveis/listagem/page/1`,
                data: { filters },
                dataType: 'json',
                success: autos => {
                    let featured = '';

                    $('.list-autos').empty();

                    $.each(autos, function (key, value) {

                        featured = value.destaque ? '<div class="tag-2 bg-active">Destaque</div>' : '';

                        $('.list-autos').append(`
                            <div class="car-box-2" >
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5 col-pad">
                                            <div class="car-thumbnail">
                                                <a href="car-details.html" class="car-img">
                                                    ${featured}
                                                    <img class="d-block w-100" src="${window.location.origin}/${value.file}" alt="car">
                                                </a>
                                                <div class="carbox-overlap-wrapper">
                                                    <div class="overlap-box">
                                                        <div class="overlap-btns-area">
                                                            <a class="overlap-btn" href="${window.location.origin}/automovel/${value.auto_id}">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-7 col-md-7 col-pad align-self-center">
                                            <div class="detail">
                                                <h3 class="title">
                                                    <a href="car-details.html">${value.modelo_nome}</a>
                                                </h3>
                                                <ul class="custom-list">
                                                    <li>
                                                        <a href="#">${value.valor}</a>
                                                    </li>
                                                </ul>
                                                <ul class="facilities-list clearfix">
                                                    <li>
                                                        <i class="flaticon-way"></i> ${value.kms} km
                                                    </li>
                                                    <li>
                                                        <i class="flaticon-calendar-1"></i> ${value.ano_nome}
                                                    </li>
                                                    <li>
                                                        <i class="fas fa-compass"></i> ${value.cambio}
                                                    </li>
                                                    <li>
                                                        <i class="fas fa-gas-pump"></i> ${value.combustivel}
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        `);
                    });
                }, error: e => {
                    console.log(e);
                }
            });

            await setPagination(15, '.list-autos', '.car-box-2', '#pagination-container', '.paginacaoCursor');
        }

        const loadingContent = () => {

            $('.list-autos').empty();

            $('.list-autos').append('<div class="row"><div class="col-md-12 d-flex justify-content-center mb-5 mt-5"><h4><i class="fa fa-spin fa-spinner"></i> Consultando Automóveis</h4></div></div>');

        }

        const setPagination = (
            perPage = 15,
            wrapper = '.list-autos',
            lines = '.car-box-2',
            paginationId = '#pagination-container',
            paginationArrowsClass = '.paginacaoCursor'
        ) => {

            $(paginationId + ' li').each(function() {
                if (typeof $(this).find('a').attr('id') === "undefined" || ($(this).find('a').attr('id') !== 'beforePagination' && $(this).find('a').attr('id') !== 'afterPagination'))
                    $(this).remove();
            });

            const paginationCustomClass = 'customPagination';

            const paginationShow = () => {
                if ($(paginationId).children().length > 8) {
                    var a = $(".customPagination.active").attr("data-valor");
                    if (a >= 4) {
                        var i = parseInt(a) - 3,
                            o = parseInt(a) + 2;
                        $(".paginacaoValor").hide(), exibir2 = $(".paginacaoValor").slice(i, o).show()
                    } else $(".paginacaoValor").hide(), exibir2 = $(".paginacaoValor").slice(0, 5).show()
                }
            }

            paginationShow(),
            $("#beforePagination").hide(),
            $(lines).hide();

            for (var tamanhotabela = $(wrapper).children().length, porPagina = perPage, paginas = Math.ceil(tamanhotabela / porPagina), i = 1; i <= paginas;)
                $(paginationId).append(" <li class='page-item'><a href='#' class='page-link paginacaoValor " + paginationCustomClass + "' data-valor=" + i + ">" + i + "</a></li>"),
                i++,
                $(".paginacaoValor").hide(),
                exibir2 = $(".paginacaoValor").slice(0, 5).show();

            $(".paginacaoValor:eq(0)").addClass("active");
            var exibir = $(lines).slice(0, porPagina).show();
            $(".paginacaoValor").on("click", function() {
                $(".paginacaoValor").removeClass("active"), $(this).addClass("active");
                var a = $(this).attr("data-valor"),
                    i = a * porPagina,
                    o = i - porPagina;
                $(lines).hide(), exibir = $(lines).slice(o, i).show(), "1" === a ? $("#beforePagination").hide() : $("#beforePagination").show(), a === "" + $(".paginacaoValor:last").attr("data-valor") ? $("#afterPagination").hide() : $("#afterPagination").show(), paginationShow();
                return false;
            }), $(".page-item:not(#afterPaginationContent)").last().after($("#afterPaginationContent")), $("#beforePagination").on("click", function(e) {
                e.stopImmediatePropagation();
                var a = $(".customPagination.active").attr("data-valor"),
                    i = parseInt(a) - 1;
                $("[data-valor=" + i + "]").click(), paginationShow();
                return false;
            }), $("#afterPagination").on("click", function(e) {
                e.stopImmediatePropagation();
                var a = $(".customPagination.active").attr("data-valor"),
                    i = parseInt(a) + 1;
                $("[data-valor=" + i + "]").click(), paginationShow();
                return false;
            }), $(".paginacaoValor").css("float", "left"), $(paginationArrowsClass).css("float", "left");

            $('a.customPagination.active').trigger('click');
        }
    </script>
@stop

@section('body')
    <!-- Featured car start -->
    <div class="car-details-page content-area-6">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-12 col-xs-12">
                    <div class="car-details-section">
                        <!-- Heading start -->
                        <div class="heading-car clearfix">
                            <div class="pull-left">
                                <h2>{{ $dataAuto['auto']['modelo_nome'] }}</h2>
                            </div>
                            <div class="pull-right">
                                <div class="price-box-3"><sup>R$</sup>{{ $dataAuto['auto']['valor'] }}</div>
                            </div>
                        </div>
                        <!-- carDetailsSlider start -->
                        <div id="carDetailsSlider" class="carousel car-details-sliders slide slide-2">
                            <!-- main slider carousel items -->
                            <div class="carousel-inner">
                                @foreach($dataAuto['images'] as $keyImage => $image)
                                <div class="item carousel-item {{ $keyImage === 0 ? 'active' : '' }}" data-slide-number="{{ $keyImage }}">
                                    <img src="{{ asset("assets/admin/dist/images/autos/{$dataAuto['auto']['type_auto']}/{$dataAuto['auto']['auto_id']}/{$image->arquivo}") }}" class="img-fluid" alt="slider-car">
                                </div>
                                @endforeach
                            </div>
                            <!-- main slider carousel nav controls -->
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
                        <!-- Tabbing box start -->
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
                                        <p>VEÍCULO CONSERVADO</p>
                                        <p>UNICO DONO</p>
                                    </div>
                                </div>



                            </div>
                        </div>

                        <div class="related-cars">
                            <h3 class="heading-2">Confira outros veículos</h3>
                            <div class="slick-slider-area clearfix">
                                <div class="row slick-carousel" data-slick='{"slidesToShow": 3, "responsive":[{"breakpoint": 1024,"settings":{"slidesToShow": 2}}, {"breakpoint": 768,"settings":{"slidesToShow": 1}}]}'>
                                    <div class="slick-slide-item">
                                        <div class="car-box-3">
                                            <div class="car-thumbnail">
                                                <a href="car-details.html" class="car-img">
                                                    <div class="for">For Sale</div>
                                                    <div class="price-box">
                                                        <span class="del"><del>$950.00</del></span>
                                                        <br>
                                                        <span>$1050.00</span>
                                                    </div>
                                                    <img class="d-block w-100" src="{{ asset('assets/user/img/car/car-1.png') }}" alt="car">
                                                </a>
                                                <div class="carbox-overlap-wrapper">
                                                    <div class="overlap-box">
                                                        <div class="overlap-btns-area">
                                                            <a class="overlap-btn" data-toggle="modal" data-target="#carOverviewModal">
                                                                <i class="fa fa-eye-slash"></i>
                                                            </a>
                                                            <a class="overlap-btn wishlist-btn">
                                                                <i class="fa fa-heart-o"></i>
                                                            </a>
                                                            <a class="overlap-btn compare-btn">
                                                                <i class="fa fa-balance-scale"></i>
                                                            </a>
                                                            <div class="car-magnify-gallery">
                                                                <a href="{{ asset('assets/user/img/car/car-1.png') }}" class="overlap-btn" data-sub-html="<h4>Lamborghini</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy....</p>">
                                                                    <i class="fa fa-expand"></i>
                                                                    <img class="hidden" src="{{ asset('assets/user/img/car/car-1.png') }}" alt="hidden-img">
                                                                </a>
                                                                <a href="{{ asset('assets/user/img/car/car-2.png') }}" class="hidden" data-sub-html="<h4>Ferrari Red Car</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy....</p>">
                                                                    <img class="hidden" src="{{ asset('assets/user/img/car/car-2.png') }}" alt="hidden-img">
                                                                </a>
                                                                <a href="{{ asset('assets/user/img/car/car-3.png') }}" class="hidden" data-sub-html="<h4>Bmw e46 m3 Diski Serie</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy....</p>">
                                                                    <img class="hidden" src="{{ asset('assets/user/img/car/car-3.png') }}" alt="hidden-img">
                                                                </a>
                                                                <a href="{{ asset('assets/user/img/car/car-4.png') }}" class="hidden" data-sub-html="<h4>Volkswagen Scirocco 2016</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy....</p>">
                                                                    <img class="hidden" src="{{ asset('assets/user/img/car/car-4.png') }}" alt="hidden-img">
                                                                </a>
                                                                <a href="{{ asset('assets/user/img/car/car-5.png') }}" class="hidden" data-sub-html="<h4>Porsche Cayen Last</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy....</p>">
                                                                    <img class="hidden" src="{{ asset('assets/user/img/car/car-5.png') }}" alt="hidden-img">
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="detail">
                                                <h1 class="title">
                                                    <a href="car-details.html">Lamborghini</a>
                                                </h1>
                                                <ul class="custom-list">
                                                    <li>
                                                        <a href="#">New Car</a> /
                                                    </li>
                                                    <li>
                                                        <a href="#">Automatic</a> /
                                                    </li>
                                                    <li>
                                                        <a href="#">Sports</a>
                                                    </li>
                                                </ul>
                                                <ul class="facilities-list clearfix">
                                                    <li>
                                                        <i class="flaticon-fuel"></i> Petrol
                                                    </li>
                                                    <li>
                                                        <i class="flaticon-way"></i> 4,000 km
                                                    </li>
                                                    <li>
                                                        <i class="flaticon-manual-transmission"></i> Manual
                                                    </li>
                                                    <li>
                                                        <i class="flaticon-car"></i> Sport
                                                    </li>
                                                    <li>
                                                        <i class="flaticon-gear"></i> Blue
                                                    </li>
                                                    <li>
                                                        <i class="flaticon-calendar-1"></i> 2019
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="footer clearfix">
                                                <div class="pull-left ratings">
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star-o"></i>
                                                    <span>(65 Reviews)</span>
                                                </div>
                                                <ul class="pull-right icon">
                                                    <li><a href="#"><i class="fa fa-heart-o"></i></a></li>
                                                    <li><a href="#"><i class="fa fa-share-square-o"></i></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="slick-slide-item">
                                        <div class="car-box-3">
                                            <div class="car-thumbnail">
                                                <a href="car-details.html" class="car-img">
                                                    <div class="tag-2 bg-active">Featured</div>
                                                    <div class="price-box">
                                                        <span class="del"><del>$805.00</del></span>
                                                        <br>
                                                        <span>$780.00</span>
                                                    </div>
                                                    <img class="d-block w-100" src="{{ asset('assets/user/img/car/car-2.png') }}" alt="car">
                                                </a>
                                                <div class="carbox-overlap-wrapper">
                                                    <div class="overlap-box">
                                                        <div class="overlap-btns-area">
                                                            <a class="overlap-btn" data-toggle="modal" data-target="#carOverviewModal">
                                                                <i class="fa fa-eye-slash"></i>
                                                            </a>
                                                            <a class="overlap-btn wishlist-btn">
                                                                <i class="fa fa-heart-o"></i>
                                                            </a>
                                                            <a class="overlap-btn compare-btn">
                                                                <i class="fa fa-balance-scale"></i>
                                                            </a>
                                                            <div class="car-magnify-gallery">
                                                                <a href="{{ asset('assets/user/img/car/car-2.png') }}" class="overlap-btn" data-sub-html="<h4>Ferrari Red Car</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy....</p>">
                                                                    <i class="fa fa-expand"></i>
                                                                    <img class="hidden" src="{{ asset('assets/user/img/car/car-2.png') }}" alt="hidden-img">
                                                                </a>
                                                                <a href="{{ asset('assets/user/img/car/car-1.png') }}" class="hidden" data-sub-html="<h4>Ferrari Red Car</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy....</p>">
                                                                    <img class="hidden" src="{{ asset('assets/user/img/car/car-1.png') }}" alt="hidden-img">
                                                                </a>
                                                                <a href="{{ asset('assets/user/img/car/car-3.png') }}" class="hidden" data-sub-html="<h4>Bmw e46 m3 Diski Serie</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy....</p>">
                                                                    <img class="hidden" src="{{ asset('assets/user/img/car/car-3.png') }}" alt="hidden-img">
                                                                </a>
                                                                <a href="{{ asset('assets/user/img/car/car-4.png') }}" class="hidden" data-sub-html="<h4>Volkswagen Scirocco 2016</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy....</p>">
                                                                    <img class="hidden" src="{{ asset('assets/user/img/car/car-4.png') }}" alt="hidden-img">
                                                                </a>
                                                                <a href="{{ asset('assets/user/img/car/car-5.png') }}" class="hidden" data-sub-html="<h4>Porsche Cayen Last</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy....</p>">
                                                                    <img class="hidden" src="{{ asset('assets/user/img/car/car-5.png') }}" alt="hidden-img">
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="detail">
                                                <h1 class="title">
                                                    <a href="car-details.html">Ferrari Red Car</a>
                                                </h1>
                                                <ul class="custom-list">
                                                    <li>
                                                        <a href="#">New Car</a> /
                                                    </li>
                                                    <li>
                                                        <a href="#">Automatic</a> /
                                                    </li>
                                                    <li>
                                                        <a href="#">Sports</a>
                                                    </li>
                                                </ul>
                                                <ul class="facilities-list clearfix">
                                                    <li>
                                                        <i class="flaticon-fuel"></i> Petrol
                                                    </li>
                                                    <li>
                                                        <i class="flaticon-way"></i> 4,000 km
                                                    </li>
                                                    <li>
                                                        <i class="flaticon-manual-transmission"></i> Manual
                                                    </li>
                                                    <li>
                                                        <i class="flaticon-car"></i> Sport
                                                    </li>
                                                    <li>
                                                        <i class="flaticon-gear"></i> Blue
                                                    </li>
                                                    <li>
                                                        <i class="flaticon-calendar-1"></i> 2019
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="footer clearfix">
                                                <div class="pull-left ratings">
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star-o"></i>
                                                    <span>(65 Reviews)</span>
                                                </div>
                                                <ul class="pull-right icon">
                                                    <li><a href="#"><i class="fa fa-heart-o"></i></a></li>
                                                    <li><a href="#"><i class="fa fa-share-square-o"></i></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="slick-slide-item">
                                        <div class="car-box-3">
                                            <div class="car-thumbnail">
                                                <a href="car-details.html" class="car-img">
                                                    <div class="for">For Rent</div>
                                                    <div class="price-box">
                                                        <span class="del"><del>$830.00</del></span>
                                                        <br>
                                                        <span>$940.00</span>
                                                    </div>
                                                    <img class="d-block w-100" src="{{ asset('assets/user/img/car/car-3.png') }}" alt="car">
                                                </a>
                                                <div class="carbox-overlap-wrapper">
                                                    <div class="overlap-box">
                                                        <div class="overlap-btns-area">
                                                            <a class="overlap-btn" data-toggle="modal" data-target="#carOverviewModal">
                                                                <i class="fa fa-eye-slash"></i>
                                                            </a>
                                                            <a class="overlap-btn wishlist-btn">
                                                                <i class="fa fa-heart-o"></i>
                                                            </a>
                                                            <a class="overlap-btn compare-btn">
                                                                <i class="fa fa-balance-scale"></i>
                                                            </a>
                                                            <div class="car-magnify-gallery">
                                                                <a href="{{ asset('assets/user/img/car/car-3.png') }}" class="overlap-btn" data-sub-html="<h4>Bmw e46 m3 Diski Serie</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy....</p>">
                                                                    <i class="fa fa-expand"></i>
                                                                    <img class="hidden" src="{{ asset('assets/user/img/car/car-3.png') }}" alt="hidden-img">
                                                                </a>
                                                                <a href="{{ asset('assets/user/img/car/car-2.png') }}" class="hidden" data-sub-html="<h4>Ferrari Red Car</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy....</p>">
                                                                    <img class="hidden" src="{{ asset('assets/user/img/car/car-2.png') }}" alt="hidden-img">
                                                                </a>
                                                                <a href="{{ asset('assets/user/img/car/car-1.png') }}" class="hidden" data-sub-html="<h4>Lamborghini</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy....</p>">
                                                                    <img class="hidden" src="{{ asset('assets/user/img/car/car-1.png') }}" alt="hidden-img">
                                                                </a>
                                                                <a href="{{ asset('assets/user/img/car/car-4.png') }}" class="hidden" data-sub-html="<h4>Volkswagen Scirocco 2016</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy....</p>">
                                                                    <img class="hidden" src="{{ asset('assets/user/img/car/car-4.png') }}" alt="hidden-img">
                                                                </a>
                                                                <a href="{{ asset('assets/user/img/car/car-5.png') }}" class="hidden" data-sub-html="<h4>Porsche Cayen Last</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy....</p>">
                                                                    <img class="hidden" src="{{ asset('assets/user/img/car/car-5.png') }}" alt="hidden-img">
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="detail">
                                                <h1 class="title">
                                                    <a href="car-details.html">Bmw e46 m3 Diski Serie</a>
                                                </h1>
                                                <ul class="custom-list">
                                                    <li>
                                                        <a href="#">New Car</a> /
                                                    </li>
                                                    <li>
                                                        <a href="#">Automatic</a> /
                                                    </li>
                                                    <li>
                                                        <a href="#">Sports</a>
                                                    </li>
                                                </ul>
                                                <ul class="facilities-list clearfix">
                                                    <li>
                                                        <i class="flaticon-fuel"></i> Petrol
                                                    </li>
                                                    <li>
                                                        <i class="flaticon-way"></i> 4,000 km
                                                    </li>
                                                    <li>
                                                        <i class="flaticon-manual-transmission"></i> Manual
                                                    </li>
                                                    <li>
                                                        <i class="flaticon-car"></i> Sport
                                                    </li>
                                                    <li>
                                                        <i class="flaticon-gear"></i> Blue
                                                    </li>
                                                    <li>
                                                        <i class="flaticon-calendar-1"></i> 2019
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="footer clearfix">
                                                <div class="pull-left ratings">
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star-o"></i>
                                                    <span>(65 Reviews)</span>
                                                </div>
                                                <ul class="pull-right icon">
                                                    <li><a href="#"><i class="fa fa-heart-o"></i></a></li>
                                                    <li><a href="#"><i class="fa fa-share-square-o"></i></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="slick-slide-item">
                                        <div class="car-box-3">
                                            <div class="car-thumbnail">
                                                <a href="car-details.html" class="car-img">
                                                    <div class="tag-2 bg-active">Featured</div>
                                                    <div class="price-box">
                                                        <span class="del"><del>$950.00</del></span>
                                                        <br>
                                                        <span>$1050.00</span>
                                                    </div>
                                                    <img class="d-block w-100" src="{{ asset('assets/user/img/car/car-4.png') }}" alt="car">
                                                </a>
                                                <div class="carbox-overlap-wrapper">
                                                    <div class="overlap-box">
                                                        <div class="overlap-btns-area">
                                                            <a class="overlap-btn" data-toggle="modal" data-target="#carOverviewModal">
                                                                <i class="fa fa-eye-slash"></i>
                                                            </a>
                                                            <a class="overlap-btn wishlist-btn">
                                                                <i class="fa fa-heart-o"></i>
                                                            </a>
                                                            <a class="overlap-btn compare-btn">
                                                                <i class="fa fa-balance-scale"></i>
                                                            </a>
                                                            <div class="car-magnify-gallery">
                                                                <a href="{{ asset('assets/user/img/car/car-4.png') }}" class="overlap-btn" data-sub-html="<h4>Volkswagen Scirocco 2016</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy....</p>">
                                                                    <i class="fa fa-expand"></i>
                                                                    <img class="hidden" src="{{ asset('assets/user/img/car/car-4.png') }}" alt="hidden-img">
                                                                </a>
                                                                <a href="{{ asset('assets/user/img/car/car-2.png') }}" class="hidden" data-sub-html="<h4>Ferrari Red Car</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy....</p>">
                                                                    <img class="hidden" src="{{ asset('assets/user/img/car/car-2.png') }}" alt="hidden-img">
                                                                </a>
                                                                <a href="{{ asset('assets/user/img/car/car-3.png') }}" class="hidden" data-sub-html="<h4>Bmw e46 m3 Diski Serie</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy....</p>">
                                                                    <img class="hidden" src="{{ asset('assets/user/img/car/car-3.png') }}" alt="hidden-img">
                                                                </a>
                                                                <a href="{{ asset('assets/user/img/car/car-1.png') }}" class="hidden" data-sub-html="<h4>Lamborghini</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy....</p>">
                                                                    <img class="hidden" src="{{ asset('assets/user/img/car/car-1.png') }}" alt="hidden-img">
                                                                </a>
                                                                <a href="{{ asset('assets/user/img/car/car-5.png') }}" class="hidden" data-sub-html="<h4>Porsche Cayen Last</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy....</p>">
                                                                    <img class="hidden" src="{{ asset('assets/user/img/car/car-5.png') }}" alt="hidden-img">
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="detail">
                                                <h1 class="title">
                                                    <a href="car-details.html">Volkswagen Scirocco 2016</a>
                                                </h1>
                                                <ul class="custom-list">
                                                    <li>
                                                        <a href="#">New Car</a> /
                                                    </li>
                                                    <li>
                                                        <a href="#">Automatic</a> /
                                                    </li>
                                                    <li>
                                                        <a href="#">Sports</a>
                                                    </li>
                                                </ul>
                                                <ul class="facilities-list clearfix">
                                                    <li>
                                                        <i class="flaticon-fuel"></i> Petrol
                                                    </li>
                                                    <li>
                                                        <i class="flaticon-way"></i> 4,000 km
                                                    </li>
                                                    <li>
                                                        <i class="flaticon-manual-transmission"></i> Manual
                                                    </li>
                                                    <li>
                                                        <i class="flaticon-car"></i> Sport
                                                    </li>
                                                    <li>
                                                        <i class="flaticon-gear"></i> Blue
                                                    </li>
                                                    <li>
                                                        <i class="flaticon-calendar-1"></i> 2019
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="footer clearfix">
                                                <div class="pull-left ratings">
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star-o"></i>
                                                    <span>(65 Reviews)</span>
                                                </div>
                                                <ul class="pull-right icon">
                                                    <li><a href="#"><i class="fa fa-heart-o"></i></a></li>
                                                    <li><a href="#"><i class="fa fa-share-square-o"></i></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact 2 start -->

                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="sidebar-right">
                        <!-- Advanced search start -->
                        <div class="widget advanced-search d-none-992">
                            <ul>
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
                                <a href="https://api.whatsapp.com/send?phone=55{{ $settings->storePhonePrimary_n }}&text=Olá,%0DGostaria de mais informações sobre o automóvel%0D%0D-{{ $dataAuto['auto']['marca_nome'] }}%0D-{{ $dataAuto['auto']['modelo_nome'] }}%0D-{{ $dataAuto['auto']['ano_nome'] }}%0D-{{ $dataAuto['auto']['cor'] }}" target="_blank" class="btn-5 text-center p-1 pt-2 pb-2"><i class="fab fa-whatsapp"></i> Fale com um vendedor no WhatsApp</a>
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

                        <!-- Question start -->



                        <!-- Financing calculator start -->

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Featured car start -->
@stop
