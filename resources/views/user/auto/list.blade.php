@extends('user.template.page')

{{-- set title --}}
@section('title', 'Estoque')

{{-- import css --}}
@section('css')
    <style>
        .filter-list-autos .dropdown-menu[x-placement] {
            right: 0 !important;
            width: 100% !important;
            min-width: unset !important;
            top: 50px !important;
            z-index: 3;
            transform: unset !important;
        }
        .filter-list-autos .bootstrap-select .dropdown-menu.inner {
            box-shadow: unset;
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
    <script>
        $(document).ready(function () {

            if ($(document).width() >= 975) $('.content-list-autos-after').remove();
            else $('.content-list-autos-before').remove();

            if ($(document).width() < 751) $('.sorting-options').hide();

            initLoad();
        });

        const initLoad = async () => {
            await getFiltersAuto($('.filter-list-autos'));
            //await getOptionalsAutos();
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
            //filters.filter.color     = $('[name="select-color"]').val();
            filters.filter.year      = $('[name="select-year"]').val();
            filters.filter.max_price = $('[name="max_price"]').val() ?? null;
            filters.filter.min_price = $('[name="min_price"]').val() ?? null;

            await $('#options-content-optionals input[type="checkbox"]').each(function(){
                if ($(this).is(':checked'))
                    filters.optionals.push($(this).val());
            });

            return filters;
        }

        $('.change-view-btn').on('click', function(){
            sessionStorage.setItem('viewListAutos', $('i', this).hasClass('fa-th-list') ? 'list' : 'grid');
            getAutos();
            return false;
        });

        $('select[name="default-order"]').on('change', function (){
            getAutos();
        });

        const getAutos = async () => {

            let typeView = sessionStorage.getItem('viewListAutos');
            if (typeView === null) typeView = 'grid';

            $('.change-view-btn').removeClass('active-view-btn');
            $(typeView === 'list' ? '.fa-th-list' : '.fa-th-large').closest('a').addClass('active-view-btn');

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

                    $('.list-autos').removeClass('row').empty();
                    if (typeView === 'grid') $('.list-autos').addClass('row');

                    if (autos.length === 0) {
                        $('.pagination-box').hide();
                        $('.list-autos').append('<div class="row"><div class="col-md-12 d-flex justify-content-center mb-5 mt-5"><h4>Nenhum Automóvel Encontrado</h4></div></div>');
                    } else
                        $('.pagination-box').show();

                    $.each(autos, function (key, value) {

                        featured = value.destaque ? '<div class="tag-2 bg-active">Destaque</div>' : '';

                        $('.list-autos').append(
                            typeView === 'list' ?
                            `<div class="car-box-2" >
                                    <div class="row">
                                        <div class="col-lg-5 col-md-5 col-pad">
                                            <div class="car-thumbnail">
                                                <a href="${window.location.origin}/automovel/${value.auto_id}" class="car-img">
                                                    ${featured}
                                                    <img class="d-block w-100" src="${window.location.origin}/${value.file}" alt="car">
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-lg-7 col-md-7 col-pad align-self-center">
                                            <div class="detail">
                                                <h3 class="title">
                                                    <a href="${window.location.origin}/automovel/${value.auto_id}" title="${value.modelo_nome}">${value.modelo_nome}</a>
                                                </h3>
                                                <ul class="custom-list">
                                                    <li>
                                                        <a href="#">${value.rs_valor}</a>
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
                                                        <i class="fas fa-project-diagram"></i> ${value.cambio}
                                                    </li>
                                                    <li>
                                                        <i class="fas fa-gas-pump"></i> ${value.combustivel}
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        ` :
                        `
                        <div class="col-lg-4 col-md-4">
                            <div class="car-box-2">
                                <div class="car-thumbnail">
                                    <a href="${window.location.origin}/automovel/${value.auto_id}" class="car-img">
                                        ${featured}
                                        <img class="d-block w-100" src="${window.location.origin}/${value.file}" alt="car">
                                    </a>
                                </div>
                                <div class="detail">
                                    <h1 class="title">
                                        <a href="${window.location.origin}/automovel/${value.auto_id}" title="${value.modelo_nome}">${value.modelo_nome}</a>
                                    </h1>
                                    <ul class="custom-list">
                                        <li>
                                            <a href="#">${value.rs_valor}</a>
                                        </li>
                                    </ul>
                                    <hr>
                                    <ul class="facilities-list clearfix">
                                        <li>
                                            <i class="flaticon-way"></i> ${value.kms} km
                                        </li>
                                        <li>
                                            <i class="flaticon-calendar-1"></i> ${value.ano_nome}
                                        </li>
                                        <li>
                                            <i class="fas fa-project-diagram"></i> ${value.cambio}
                                        </li>
                                        <li>
                                            <i class="fas fa-gas-pump"></i> ${value.combustivel}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        `
                        );
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

            if ($('.list-autos div[class^="car-box-"]').length === 0) tamanhotabela=0;

            $('.index-pagination').text(`${tamanhotabela} Automóveis Listados`);

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
    <div class="featured-car content-area-4">
        <div class="container">
            <div class="row">
                <div class="col-lg-9 col-md-12 content-list-autos-before">
                    <!-- Option bar start -->
                    <div class="option-bar clearfix">
                        <div class="row">
                            <div class="col-lg-5 col-md-6 col-sm-12">
                                <div class="sorting-options2">
                                    <h5 class="index-pagination"><i class="fa fa-spin fa-spinner"></i></h5>
                                </div>
                            </div>
                            <div class="col-lg-7 col-md-6 col-sm-12">
                                <div class="sorting-options float-right">
                                    <a href="#" class="change-view-btn float-right"><i class="fa fa-th-list"></i></a>
                                    <a href="#" class="change-view-btn float-right"><i class="fa fa-th-large"></i></a>
                                </div>
                                <div class="col-md-9 float-right no-padding">
                                    <select class="selectpicker search-fields" name="default-order">
                                        <option value="0">Mais Recentes</option>
                                        <option value="1">Por Preço: Menor para maior</option>
                                        <option value="2">Por Preço: Maior para menor</option>
                                        <option value="3">Por Ano: Menor para maior</option>
                                        <option value="4">Por Ano: Maior para menor</option>
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
                <div class="col-lg-3 col-md-12 no-padding">
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
                            <div class="range-slider clearfix">
                                <label>Preço</label>
                                <div data-min="0" data-max="0" data-min-name="min_price" data-max-name="max_price" class="range-slider-ui ui-slider range-price-filter" id="range-price-filter" aria-disabled="false"></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="form-group mb-0">
                                <button type="button" class="search-button btn" onclick="getAutos()">Buscar</button>
                            </div>
                        </div>
                        <div class="widget question widget-3">
                            <h5 class="sidebar-title">Precisa de ajuda?</h5>
                            <div class="s-border"></div>
                            <div class="m-border"></div>
                            <ul class="contact-info">
                                <li>
                                    <i class="flaticon-pin"></i>{{ $settings->address }}
                                </li>
                                <li>
                                    <i class="flaticon-mail"></i><a href="mailto:info@themevessel.com">{{ $settings->storeEmail }}</a>
                                </li>
                                <li>
                                    @if($settings->storeWhatsPhonePrimary)
                                        <a href="https://api.whatsapp.com/send?phone=55{{ $settings->storePhonePrimary_n }}">{{ $settings->storePhonePrimary }}</a>
                                        <i class="fab fa-whatsapp"></i>
                                    @else
                                        <a href="tel:{{ $settings->storePhonePrimary_n }}">{{ $settings->storePhonePrimary }}</a>
                                        <i class="flaticon-phone"></i>
                                    @endif
                                </li>
                            </ul>
                            <div class="social-list clearfix">
                                <ul>
                                    @foreach($settings->socialNetworks as $network)
                                        <li><a href="{{$network['link']}}" class="{{$network['network']}}-bg" target="_blank"><i class="fab fa-{{$network['network']}}"></i></a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-md-12 content-list-autos-after">
                    <!-- Option bar start -->
                    <div class="option-bar clearfix">
                        <div class="row">
                            <div class="col-lg-5 col-md-6 col-sm-12">
                                <div class="sorting-options2">
                                    <h5 class="index-pagination"><i class="fa fa-spin fa-spinner"></i></h5>
                                </div>
                            </div>
                            <div class="col-lg-7 col-md-6 col-sm-12">
                                <div class="sorting-options float-right">
                                    <a href="#" class="change-view-btn float-right"><i class="fa fa-th-list"></i></a>
                                    <a href="#" class="change-view-btn float-right"><i class="fa fa-th-large"></i></a>
                                </div>
                                <div class="col-md-9 float-right no-padding">
                                    <select class="selectpicker search-fields" name="default-order">
                                        <option value="0">Mais Recentes</option>
                                        <option value="1">Por Preço: Menor para maior</option>
                                        <option value="2">Por Preço: Maior para menor</option>
                                        <option value="3">Por Ano: Menor para maior</option>
                                        <option value="4">Por Ano: Maior para menor</option>
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
            </div>
        </div>
    </div>
    <!-- Featured car start -->
@stop
