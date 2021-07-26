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
        $(function (){
            getAutos();
        });

        const getAutos = () => {
            console.log(`${window.location.origin}/ajax/automoveis/listagem`);
            $.get(`${window.location.origin}/ajax/automoveis/listagem`, function (autos) {
                console.log(autos);
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
                                                <div class="price-box">
                                                    <span>${value.valor}</span>
                                                </div>
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
                                                    <a href="#">${value.marca_nome}</a>
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
                                                    <i class="flaticon-gear"></i> ${value.cor}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    `);
                });
            }, 'JSON').fail(function(e) {
                console.log(e);
            });
        }
    </script>
@stop

@section('body')
    <!-- Sub banner start -->
    <div class="sub-banner">
        <div class="container breadcrumb-area">
            <div class="breadcrumb-areas">
                <h1>Car List</h1>
                <ul class="breadcrumbs">
                    <li><a href="index.html">Home</a></li>
                    <li class="active">Car List</li>
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
                                    <h5>Showing 1-15 of 69 Listings</h5>
                                </div>
                            </div>
                            <div class="col-lg-7 col-md-6 col-sm-12">
                                <div class="sorting-options float-right">
                                    <a href="car-list-rightside.html" class="change-view-btn active-view-btn float-right"><i class="fa fa-th-list"></i></a>
                                    <a href="car-grid-rightside.html" class="change-view-btn float-right"><i class="fa fa-th-large"></i></a>
                                </div>
                                <div class="sorting-options-3 float-right">
                                    <select class="selectpicker search-fields" name="default-order">
                                        <option>Default Order</option>
                                        <option>Price High to Low</option>
                                        <option>Price: Low to High</option>
                                        <option>Newest Properties</option>
                                        <option>Oldest Properties</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Car box 2 start -->
                    <div class="list-autos">

                    </div>
                    <!-- Page navigation start -->
                    <div class="pagination-box p-box-2 text-center">
                        <nav aria-label="Page navigation example">
                            <ul class="pagination">
                                <li class="page-item">
                                    <a class="page-link" href="#"><i class="fa fa-angle-left"></i></a>
                                </li>
                                <li class="page-item"><a class="page-link active" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#"><i class="fa fa-angle-right"></i></a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="sidebar-right">
                        <!-- Advanced search start -->
                        <div class="widget advanced-search2">
                            <h3 class="sidebar-title">Search Cars</h3>
                            <div class="s-border"></div>
                            <div class="m-border"></div>
                            <form method="GET">
                                <div class="form-group">
                                    <select class="selectpicker search-fields" name="brand">
                                        <option>Brand</option>
                                        <option>Any</option>
                                        <option>New</option>
                                        <option>Semi-New</option>
                                        <option>Damaged</option>
                                        <option>Used</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="selectpicker search-fields" name="location">
                                        <option>Location</option>
                                        <option>United States</option>
                                        <option>United Kingdom</option>
                                        <option>American Samoa</option>
                                        <option>Belgium</option>
                                        <option>Cameroon</option>
                                        <option>Canada</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="selectpicker search-fields" name="year">
                                        <option>Year</option>
                                        <option>2016</option>
                                        <option>2017</option>
                                        <option>2018</option>
                                        <option>2019</option>
                                        <option>2020</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="selectpicker search-fields" name="category">
                                        <option>Category</option>
                                        <option>Luxury Car</option>
                                        <option>Pickup Truck</option>
                                        <option>Minivan</option>
                                        <option>Truck</option>
                                        <option>Sports Car</option>
                                        <option>Wagon</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="selectpicker search-fields" name="transmission">
                                        <option>Transmission</option>
                                        <option>Automatic</option>
                                        <option>Manual</option>
                                        <option>Tiptronic</option>
                                    </select>
                                </div>
                                <div class="range-slider clearfix">
                                    <label>Price</label>
                                    <div data-min="0" data-max="150000"  data-min-name="min_price" data-max-name="max_price" data-unit="USD" class="range-slider-ui ui-slider" aria-disabled="false"></div>
                                    <div class="clearfix"></div>
                                </div>
                                <a class="show-more-options" data-toggle="collapse" data-target="#options-content">
                                    <i class="fa fa-plus-circle"></i> Other Features
                                </a>
                                <div id="options-content" class="collapse">
                                    <h3 class="sidebar-title">Brands</h3>
                                    <div class="s-border"></div>
                                    <div class="m-border"></div>
                                    <div class="checkbox checkbox-theme checkbox-circle">
                                        <input id="checkbox2" type="checkbox">
                                        <label for="checkbox2">
                                            Audi
                                        </label>
                                    </div>
                                    <div class="checkbox checkbox-theme checkbox-circle">
                                        <input id="checkbox3" type="checkbox">
                                        <label for="checkbox3">
                                            BMW
                                        </label>
                                    </div>
                                    <div class="checkbox checkbox-theme checkbox-circle">
                                        <input id="checkbox4" type="checkbox">
                                        <label for="checkbox4">
                                            Honda
                                        </label>
                                    </div>
                                    <div class="checkbox checkbox-theme checkbox-circle">
                                        <input id="checkbox1" type="checkbox">
                                        <label for="checkbox1">
                                            Lamborghini
                                        </label>
                                    </div>
                                    <div class="checkbox checkbox-theme checkbox-circle">
                                        <input id="checkbox5" type="checkbox">
                                        <label for="checkbox5">
                                            Toyota
                                        </label>
                                    </div>
                                    <br>
                                </div>
                                <div class="form-group mb-0">
                                    <button class="search-button btn">Search</button>
                                </div>
                            </form>
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
