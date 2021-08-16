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
                                                            <a class="overlap-btn view-details-auto" data-id="${value.auto_id}">
                                                                <i class="fa fa-eye-slash"></i>
                                                            </a>
                                                            <a class="overlap-btn wishlist-btn">
                                                                <i class="fa fa-heart-o"></i>
                                                            </a>
                                                            <a class="overlap-btn compare-btn">
                                                                <i class="fa fa-balance-scale"></i>
                                                            </a>
                                                            <div class="car-magnify-gallery">
                                                                <a href="img/car/car-1.png" class="overlap-btn" data-sub-html="<h4>Lamborghini</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy....</p>">
                                                                    <i class="fa fa-expand"></i>
                                                                    <img class="hidden" src="${window.location.origin}/${value.file}" alt="hidden-img">
                                                                </a>
                                                            </div>
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
    <!-- Sub banner start -->
    <div class="sub-banner">
        <div class="container breadcrumb-area">
            <div class="breadcrumb-areas">
                <h1>Estoque</h1>
                <ul class="breadcrumbs">
                    <li><a href="{{ route('user.home') }}">Início</a></li>
                    <li class="active">Estoque</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- Sub Banner end -->

    <!-- Featured car start -->
    <div class="featured-car content-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-12">
                    <!-- Option bar start -->
                    <div class="option-bar clearfix">
                        <div class="row">
                            <div class="col-lg-5 col-md-6 col-sm-12">
                                <div class="sorting-options2">
                                    <h5>Mostrando 1-15 de 69 Listados</h5>
                                </div>
                            </div>
                            <div class="col-lg-7 col-md-6 col-sm-12">
                                <div class="sorting-options float-right">
                                    <a href="car-list-rightside.html" class="change-view-btn active-view-btn float-right"><i class="fa fa-th-list"></i></a>
                                    <a href="car-grid-rightside.html" class="change-view-btn float-right"><i class="fa fa-th-large"></i></a>
                                </div>
                                <div class="sorting-options-3 float-right">
                                    <select class="selectpicker search-fields" name="default-order">
                                        <option value="0">Recentes</option>
                                        <option value="1">Preço Maior > Menor</option>
                                        <option value="2">Preço Menor > Maior</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Car box 2 start -->
                    <div class="list-autos"></div>
                    <!-- Page navigation start -->
                    <div class="pagination-box p-box-2 text-center">
                        <nav aria-label="Page navigation example">
                            <ul class="pagination" id="pagination-container">
                                <li class="page-item">
                                    <a class="page-link paginacaoCursor" id="beforePagination" href="#"><i class="fa fa-angle-left"></i></a>
                                </li>
                                <li class="page-item" id="afterPaginationContent">
                                    <a class="page-link paginacaoCursor" id="afterPagination" href="#"><i class="fa fa-angle-right"></i></a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="sidebar-right">
                        <div class="widget advanced-search2 filter-list-autos">
                            <h3 class="sidebar-title">Filtre sua Consulta</h3>
                            <div class="s-border"></div>
                            <div class="m-border"></div>
                            <div class="form-search form-group">
                                <input class="form-control border bg-white" name="search-text" placeholder="Digite sua busca"/>
                            </div>
                            <div class="form-group">
                                <select class="selectpicker search-fields" name="select-brand" multiple data-live-search="true" title="Por marca"></select>
                            </div>
                            <div class="form-group">
                                <select class="selectpicker search-fields" name="select-make" multiple data-live-search="true" title="Por modelo"></select>
                            </div>
                            <div class="form-group">
                                <select class="selectpicker search-fields" name="select-year" multiple data-live-search="true" title="Por ano"></select>
                            </div>
                            <div class="form-group">
                                <select class="selectpicker search-fields" name="select-color" multiple data-live-search="true" title="Por cor"></select>
                            </div>
                            <div class="range-slider clearfix">
                                <label>Price</label>
                                <div data-min="0" data-max="0" data-min-name="min_price" data-max-name="max_price" class="range-slider-ui ui-slider range-price-filter" id="range-price-filter" aria-disabled="false"></div>
                                <div class="clearfix"></div>
                            </div>
                            <a class="show-more-options" data-toggle="collapse" data-target="#options-content-optionals">
                                <i class="fa fa-plus-circle"></i> Mais Opções
                            </a>
                            <div id="options-content-optionals" class="collapse">
                                <h3 class="sidebar-title">Opcionais</h3>
                                <div class="s-border"></div>
                                <div class="m-border"></div>
                            </div>
                            <div class="form-group mb-0">
                                <button type="button" class="search-button btn" onclick="getAutos()">Buscar</button>
                            </div>
                        </div>
                        <!-- Recent posts start -->
                        <div class="widget recent-posts">
                            <h3 class="sidebar-title">Recent Posts</h3>
                            <div class="s-border"></div>
                            <div class="m-border"></div>
                            <div class="media mb-4">
                                <a class="pr-3" href="car-details.html">
                                    <img class="media-object" src="{{ asset('assets/user/img/car/small-car-3.png') }}" alt="small-car">
                                </a>
                                <div class="media-body align-self-center">
                                    <h5>
                                        <a href="car-details.html">Bentley Continental GT</a>
                                    </h5>
                                    <div class="listing-post-meta">
                                        $345,00 | <a href="#"><i class="fa fa-calendar"></i> Jan 12, 2020</a>
                                    </div>
                                </div>
                            </div>
                            <div class="media mb-4">
                                <a class="pr-3" href="car-details.html">
                                    <img class="media-object" src="{{ asset('assets/user/img/car/small-car-1.png') }}" alt="small-car">
                                </a>
                                <div class="media-body align-self-center">
                                    <h5>
                                        <a href="car-details.html">Fiat Punto Red</a>
                                    </h5>
                                    <div class="listing-post-meta">
                                        $745,00 | <a href="#"><i class="fa fa-calendar"></i>Feb 26, 2020</a>
                                    </div>
                                </div>
                            </div>
                            <div class="media">
                                <a class="pr-3" href="car-details.html">
                                    <img class="media-object" src="{{ asset('assets/user/img/car/small-car-2.png') }}" alt="small-car">
                                </a>
                                <div class="media-body align-self-center">
                                    <h5>
                                        <a href="car-details.html">Nissan Micra Gen5</a>
                                    </h5>
                                    <div class="listing-post-meta">
                                        $745,00 | <a href="#"><i class="fa fa-calendar"></i> Feb 14, 2020</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Posts By Category Start -->
                        <div class="posts-by-category widget">
                            <h3 class="sidebar-title">Category</h3>
                            <div class="s-border"></div>
                            <div class="m-border"></div>
                            <ul class="list-unstyled list-cat">
                                <li><a href="#">Luxury <span>(45)</span></a></li>
                                <li><a href="#">Pickup Truck <span>(21)</span> </a></li>
                                <li><a href="#">Sports Car <span>(19)</span></a></li>
                                <li><a href="#">Automakers <span>(22) </span></a></li>
                                <li><a href="#">Wagon <span>(9) </span></a></li>
                            </ul>
                        </div>
                        <!-- Question start -->
                        <div class="widget question widget-3">
                            <h5 class="sidebar-title">Get a Question?</h5>
                            <div class="s-border"></div>
                            <div class="m-border"></div>
                            <ul class="contact-info">
                                <li>
                                    <i class="flaticon-pin"></i>20/F Green Road, Dhanmondi
                                </li>
                                <li>
                                    <i class="flaticon-mail"></i><a href="mailto:info@themevessel.com">info@themevessel.com</a>
                                </li>
                                <li>
                                    <i class="flaticon-phone"></i><a href="tel:+0477-85x6-552">+0477 85x6 552</a>
                                </li>
                            </ul>
                            <div class="social-list clearfix">
                                <ul>
                                    <li><a href="#" class="facebook-bg"><i class="fa fa-facebook"></i></a></li>
                                    <li><a href="#" class="twitter-bg"><i class="fa fa-twitter"></i></a></li>
                                    <li><a href="#" class="google-bg"><i class="fa fa-google-plus"></i></a></li>
                                    <li><a href="#" class="rss-bg"><i class="fa fa-rss"></i></a></li>
                                    <li><a href="#" class="linkedin-bg"><i class="fa fa-linkedin"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Featured car start -->
@stop
