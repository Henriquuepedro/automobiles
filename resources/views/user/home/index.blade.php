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
        $(function(){

            getOrderHomePage();

        });

        const getOrderHomePage = () => {
            $.get(`${window.location.origin}/ajax/config/ordem-pagina-inicial`, data => {
                console.log(data);
                $.each(data, function (key, value) {
                    if (parseInt(value.id) === 1) getBlogHomePage();
                    if (parseInt(value.id) === 2) getDepositionsHomePage();
                    if (parseInt(value.id) === 3) getBannerHomePage();
                    if (parseInt(value.id) === 4) getAutosFeatured();
                });

                // Slick Sliders
                $('.slick-carousel-blog-home').each(function () {
                    var slider = $(this);
                    $(this).slick({
                        infinite: true,
                        dots: false,
                        arrows: false,
                        centerMode: true,
                        centerPadding: '0'
                    });

                    $(this).closest('.slick-slider-area').find('.slick-prev').on("click", function () {
                        slider.slick('slickPrev');
                    });
                    $(this).closest('.slick-slider-area').find('.slick-next').on("click", function () {
                        slider.slick('slickNext');
                    });
                });
            });
        }
    </script>
@stop

@section('body')
<!-- Banner start -->
<div class="banner" id="banner">
    <div id="bannerCarousole" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner banner-slider-inner text-center banner-home-page">
        </div>
        <a class="carousel-control-prev none-580" href="#bannerCarousole" role="button" data-slide="prev">
            <span class="slider-mover-left" aria-hidden="true">
                <i class="fa fa-angle-left"></i>
            </span>
        </a>
        <a class="carousel-control-next none-580" href="#bannerCarousole" role="button" data-slide="next">
            <span class="slider-mover-right" aria-hidden="true">
                <i class="fa fa-angle-right"></i>
            </span>
        </a>
    </div>
</div>
<!-- Banner end -->

<!-- Search box 2 start -->
<div class="search-box-3 sb-7">
    <div class="container">
        <div class="search-area-inner">
            <div class="search-contents">
                <form method="GET">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                            <div class="form-group">
                                <select class="selectpicker search-fields" name="select-brand">
                                    <option>Select Brand</option>
                                    <option>Audi</option>
                                    <option>BMW</option>
                                    <option>Honda</option>
                                    <option>Nissan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                            <div class="form-group">
                                <select class="selectpicker search-fields" name="select-make">
                                    <option>Select Make</option>
                                    <option>BMW</option>
                                    <option>Honda</option>
                                    <option>Lamborghini</option>
                                    <option>Sports Car</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                            <div class="form-group">
                                <select class="selectpicker search-fields" name="select-location">
                                    <option>Select Location</option>
                                    <option>United States</option>
                                    <option>United Kingdom</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                            <div class="form-group">
                                <select class="selectpicker search-fields" name="select-year">
                                    <option>Select Year</option>
                                    <option>2016</option>
                                    <option>2017</option>
                                    <option>2018</option>
                                    <option>2019</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                            <div class="form-group">
                                <select class="selectpicker search-fields" name="select-type">
                                    <option>Select Type Of Car</option>
                                    <option>New Car</option>
                                    <option>Used Car</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                            <div class="form-group">
                                <select class="selectpicker search-fields" name="transmission">
                                    <option>Transmission</option>
                                    <option>Automatic</option>
                                    <option>Manual</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                            <div class="form-group">
                                <div class="range-slider">
                                    <div data-min="0" data-max="150000" data-unit="USD" data-min-name="min_price" data-max-name="max_price" class="range-slider-ui ui-slider" aria-disabled="false"></div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                            <div class="form-group">
                                <button class="btn btn-block button-theme btn-md">
                                    <i class="fa fa-search"></i>Find
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Search box 2 end -->

<!-- Featured car start -->
<div class="order-home-page">

</div>
@stop
