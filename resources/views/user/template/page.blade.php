<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <title>@yield('title')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">

        @yield('css_pre')

        <!-- External CSS libraries -->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/animate.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap-submenu.css') }}">

        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/magnific-popup.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/fonts/font-awesome/css/font-awesome.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/fonts/flaticon/font/flaticon.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/fonts/linearicons/style.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/jquery.mCustomScrollbar.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/dropzone.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/slick.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/lightbox.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/jnoty.css') }}">

        <!-- Custom stylesheet -->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/sidebar.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
        <link rel="stylesheet" type="text/css" id="style_sheet" href="{{ asset('assets/css/skins/yellow.css') }}">

        <!-- Favicon icon -->
        <link rel="shortcut icon" href="{{ asset('assets/img/favicon.ico') }}" type="image/x-icon" >

        <!-- Google fonts -->
        <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,600,700%7CUbuntu:300,400,700" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700,800,900&display=swap" rel="stylesheet">

        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/ie10-viewport-bug-workaround.css') }}">

        @yield('css')

        <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
        <!--[if lt IE 9]><script  src="{{ asset('assets/js/ie8-responsive-file-warning.js') }}"></script><![endif]-->
        <script src="{{ asset('assets/js/ie-emulation-modes-warning.js') }}"></script>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="{{ asset('assets/js/html5shiv.min.js') }}"></script>
        <script src="{{ asset('assets/js/respond.min.js') }}"></script>
        <![endif]-->

        @yield('js_head')
    </head>
    <body>
        <div class="page_loader"></div>

        <!-- Top header start -->
        <header class="top-header bg-active" id="top-header-2">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-8 col-sm-7">
                        <div class="list-inline">
                            <a href="tel:{{ $settings->storePhonePrimary }}"><i class="fa fa-phone"></i>{{ $settings->storePhonePrimary }}</a>
                            <a href="tel:{{ $settings->storeEmail }}"><i class="fa fa-envelope"></i>{{ $settings->storeEmail }}</a>
                        </div>
                    </div>
                    {{--<div class="col-lg-6 col-md-4 col-sm-5">
                        <ul class="top-social-media pull-right">
                            <li>
                                <a href="login.html" class="sign-in"><i class="fa fa-sign-in"></i> Login</a>
                            </li>
                            <li>
                                <a href="signup.html" class="sign-in"><i class="fa fa-user"></i> Register</a>
                            </li>
                        </ul>
                    </div>--}}
                </div>
            </div>
        </header>
        <!-- Top header end -->

        <!-- Main header start -->
        <header class="main-header sticky-header sh-2">
            <div class="container">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <a class="navbar-brand company-logo-2" href="index.html">
                        <img src="{{$settings->logotipo }}" alt="logo">
                    </a>
                    <button class="navbar-toggler" type="button" id="drawer">
                        <span class="fa fa-bars"></span>
                    </button>
                    <div class="navbar-collapse collapse w-100" id="navbar">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item dropdown active">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Home
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                    <li><a class="dropdown-item" href="index.html">Home 1</a></li>
                                    <li><a class="dropdown-item" href="index-2.html">Home 2</a></li>
                                    <li><a class="dropdown-item" href="index-3.html">Home 3</a></li>
                                    <li><a class="dropdown-item" href="index-4.html">Home 4</a></li>
                                    <li><a class="dropdown-item" href="index-5.html">Home 5</a></li>
                                    <li><a class="dropdown-item" href="index-6.html">Home 6</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Car Listing
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                    <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">List Layout</a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="car-list-rightside.html">List Right Sidebar</a></li>
                                            <li><a class="dropdown-item" href="car-list-leftsidebar.html">List Left Sidebar</a></li>
                                            <li><a class="dropdown-item" href="car-list-fullWidth.html">List FullWidth</a></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">Grid Layout</a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="car-grid-rightside.html">Grid Right Sidebar</a></li>
                                            <li><a class="dropdown-item" href="car-grid-leftside.html">Grid Left Sidebar</a></li>
                                            <li><a class="dropdown-item" href="car-grid-fullWidth.html">Grid FullWidth</a></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">Car Details</a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="car-details.html">Car Details 1</a></li>
                                            <li><a class="dropdown-item" href="car-details-2.html">Car Details 2</a></li>
                                            <li><a class="dropdown-item" href="car-details-3.html">Car Details 3</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown megamenu-li">
                                <a class="nav-link dropdown-toggle" href="" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Pages</a>
                                <div class="dropdown-menu megamenu" aria-labelledby="dropdown01">
                                    <div class="megamenu-area">
                                        <div class="row">
                                            <div class="col-sm-6 col-md-4 col-lg-4">
                                                <div class="megamenu-section">
                                                    <h6 class="megamenu-title">Pages</h6>
                                                    <a class="dropdown-item" href="about.html">About Us</a>
                                                    <a class="dropdown-item" href="agent.html">Agent</a>
                                                    <a class="dropdown-item" href="agent-detail.html">Agent Details</a>
                                                    <a class="dropdown-item" href="services.html">Services</a>
                                                    <a class="dropdown-item" href="services-2.html">Services 2</a>
                                                    <a class="dropdown-item" href="car-comparison.html">Car Compare</a>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4 col-lg-4">
                                                <div class="megamenu-section">
                                                    <h6 class="megamenu-title">Pages</h6>
                                                    <a class="dropdown-item" href="pricing-tables.html">Pricing Tables</a>
                                                    <a class="dropdown-item" href="gallery.html">Gallery</a>
                                                    <a class="dropdown-item" href="typography.html">Typography</a>
                                                    <a class="dropdown-item" href="elements.html">Elements</a>
                                                    <a class="dropdown-item" href="faq.html">Faq</a>
                                                    <a class="dropdown-item" href="search-brand.html">Car Brands</a>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4 col-lg-4">
                                                <div class="megamenu-section">
                                                    <h6 class="megamenu-title">Pages</h6>
                                                    <a class="dropdown-item" href="icon.html">Icons</a>
                                                    <a class="dropdown-item" href="coming-soon.html">Coming Soon</a>
                                                    <a class="dropdown-item" href="404.html">Error Page</a>
                                                    <a class="dropdown-item" href="login.html">login</a>
                                                    <a class="dropdown-item" href="signup.html">Register</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink6" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Blog
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                    <li><a class="dropdown-item" href="blog-post.html">Blog Post</a></li>
                                    <li><a class="dropdown-item" href="blog-sidebar.html">Blog Sidebar</a></li>
                                    <li><a class="dropdown-item" href="blog-details.html">Blog Details</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink7" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Shop
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                    <li><a class="dropdown-item" href="shop-list.html">Shop List</a></li>
                                    <li><a class="dropdown-item" href="shop-cart.html">Shop Cart</a></li>
                                    <li><a class="dropdown-item" href="shop-checkout.html">Shop Checkout</a></li>
                                    <li><a class="dropdown-item" href="shop-details.html">Shop Details</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link" href="contact.html">Contact</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a href="#full-page-search" class="nav-link h-icon">
                                    <i class="fa fa-search"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </header>
        <!-- Main header end -->

        <!-- Sidenav start -->
        <nav id="sidebar" class="nav-sidebar">
            <!-- Close btn-->
            <div id="dismiss">
                <i class="fa fa-close"></i>
            </div>
            <div class="sidebar-inner">
                <div class="sidebar-logo">
                    <a href="index.html">
                        <img src="{{$settings->logotipo }}" alt="sidebarlogo">
                    </a>
                </div>
                <div class="sidebar-navigation">
                    <h3 class="heading">Pages</h3>
                    <ul class="menu-list">
                        <li><a href="#" class="active pt0">Index <em class="fa fa-chevron-down"></em></a>
                            <ul>
                                <li><a href="index.html">Index 01</a></li>
                                <li><a href="index-2.html">Index 02</a></li>
                                <li><a href="index-3.html">Index 03</a></li>
                                <li><a href="index-4.html">Index 04</a></li>
                                <li><a href="index-5.html">Index 05</a></li>
                                <li><a href="index-6.html">Index 06</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#">Car Listing <em class="fa fa-chevron-down"></em></a>
                            <ul>
                                <li>
                                    <a href="#">List Layout <em class="fa fa-chevron-down"></em></a>
                                    <ul>
                                        <li><a href="car-list-rightside.html">List Right Sidebar</a></li>
                                        <li><a href="car-list-leftsidebar.html">List Left Sidebar</a></li>
                                        <li><a href="car-list-fullWidth.html">List FullWidth</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="#">Grid Layout <em class="fa fa-chevron-down"></em></a>
                                    <ul>
                                        <li><a href="car-grid-rightside.html">Grid Right Sidebar</a></li>
                                        <li><a href="car-grid-leftside.html">Grid Left Sidebar</a></li>
                                        <li><a href="car-grid-fullWidth.html">Grid FullWidth</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="#">Car Details <em class="fa fa-chevron-down"></em></a>
                                    <ul>
                                        <li><a href="car-details.html">Car Details 1</a></li>
                                        <li><a href="car-details-2.html">Car Details 2</a></li>
                                        <li><a href="car-details-3.html">Car Details 3</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#">Pages <em class="fa fa-chevron-down"></em></a>
                            <ul>
                                <li><a href="about.html">About Us</a></li>
                                <li>
                                    <a href="#">Agent <em class="fa fa-chevron-down"></em></a>
                                    <ul>
                                        <li><a href="agent.html">Agent</a></li>
                                        <li><a href="agent-detail.html">Agent Details</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="#">Services <em class="fa fa-chevron-down"></em></a>
                                    <ul>
                                        <li><a href="services.html">Services 1</a></li>
                                        <li><a href="services-2.html">Services 2</a></li>
                                    </ul>
                                </li>
                                <li><a href="car-comparison.html">Car Compare</a></li>
                                <li><a href="pricing-tables.html">Pricing Tables</a></li>
                                <li><a href="gallery.html">Gallery</a></li>
                                <li><a href="typography.html">Typography</a></li>
                                <li><a href="elements.html">Elements</a></li>
                                <li><a href="faq.html">Faq</a></li>
                                <li><a href="search-brand.html">Car Brands</a></li>
                                <li><a href="icon.html">Icons</a></li>
                                <li><a href="coming-soon.html">Coming Soon</a></li>
                                <li><a href="404.html">Error Page</a></li>
                                <li><a href="login.html">login</a></li>
                                <li><a href="signup.html">Register</a></li>
                            </ul>
                        </li>
                        <li><a href="#" class="pt0">Blog <em class="fa fa-chevron-down"></em></a>
                            <ul>
                                <li><a href="blog-post.html">Blog Post</a></li>
                                <li><a href="blog-sidebar.html">Blog Sidebar</a></li>
                                <li><a href="blog-details.html">Blog Details</a></li>
                            </ul>
                        </li>
                        <li><a href="#" class="pt0">Shop <em class="fa fa-chevron-down"></em></a>
                            <ul>
                                <li><a href="shop-list.html">Shop List</a></li>
                                <li><a href="shop-cart.html">Shop Cart</a></li>
                                <li><a href="shop-checkout.html">Shop Checkout</a></li>
                                <li><a href="shop-details.html">Shop Details</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="contact.html">Contact</a>
                        </li>
                    </ul>
                </div>
                <div class="get-in-touch">
                    <h3 class="heading">Get in Touch</h3>
                    <div class="media">
                        <i class="flaticon-phone"></i>
                        <div class="media-body">
                            <a href="tel:0477-0477-8556-552">0477 8556 552</a>
                        </div>
                    </div>
                    <div class="media">
                        <i class="flaticon-mail"></i>
                        <div class="media-body">
                            <a href="#">info@themevessel.com</a>
                        </div>
                    </div>
                    <div class="media mb-0">
                        <i class="flaticon-earth"></i>
                        <div class="media-body">
                            <a href="#">info@themevessel.com</a>
                        </div>
                    </div>
                </div>
                <div class="get-social">
                    <h3 class="heading">Get Social</h3>
                    <a href="#" class="facebook-bg">
                        <i class="fa fa-facebook"></i>
                    </a>
                    <a href="#" class="twitter-bg">
                        <i class="fa fa-twitter"></i>
                    </a>
                    <a href="#" class="google-bg">
                        <i class="fa fa-google"></i>
                    </a>
                    <a href="#" class="linkedin-bg">
                        <i class="fa fa-linkedin"></i>
                    </a>
                </div>
            </div>
        </nav>
        <!-- Sidenav end -->

        {{--@extends('user.template.header')--}}

        @yield('body')

        {{--@extends('user.template.footer')--}}

        <!-- Footer start -->
        <footer class="footer overview-bgi">
            <div class="container footer-inner">
                <div class="row">
                    <div class="col-xl-4 col-lg-3 col-md-6 col-sm-6">
                        <div class="footer-item clearfix">
                            <img src="{{ $settings->logotipo }}" alt="logo" class="f-logo">
                            <div class="s-border"></div>
                            <div class="m-border"></div>
                            <div class="text">
                                <P>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas in pulvinar neque. Nulla finibus lobortis pulvinar. Donec a consectetur nulla. Nulla posuere sapien vitae lectus suscipit, et pulvinar nisi tincidunt. Aliquam erat.</P>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                        <div class="footer-item clearfix">
                            <h4>
                                Informações de Contato
                            </h4>
                            <div class="s-border"></div>
                            <div class="m-border"></div>
                            <ul class="contact-info">
                                <li>
                                    <i class="flaticon-pin"></i><a href="https://www.google.com/maps/dir//{{ str_replace('/', ' - ', $settings->address) }}" target="_blank">{{ $settings->address }}</a>
                                </li>
                                <li>
                                    <i class="flaticon-mail"></i><a href="mailto:{{ $settings->storeEmail }}">{{ $settings->storeEmail }}</a>
                                </li>
                                <li>
                                    @if($settings->storeWhatsPhonePrimary)<i class="fa fa-whatsapp"></i>@else<i class="flaticon-phone"></i>@endif
                                    <a href="tel:{{ $settings->storePhonePrimary }}">{{ $settings->storePhonePrimary }}</a>
                                </li>
                                <li>
                                    @if($settings->storeWhatsPhoneSecondary)<i class="fa fa-whatsapp"></i>@else<i class="flaticon-phone"></i>@endif
                                    <a href="tel:{{ $settings->storePhoneSecondary }}">{{ $settings->storePhoneSecondary }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6">
                        <div class="footer-item">
                            <h4>
                                Links Úteis
                            </h4>
                            <div class="s-border"></div>
                            <div class="m-border"></div>
                            <ul class="links">
                                <li>
                                    <a href="{{ route('user.home') }}"><i class="fa fa-angle-right"></i>Home</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                        <div class="footer-item clearfix">
                            <h4>Inscreva-se</h4>
                            <div class="s-border"></div>
                            <div class="m-border"></div>
                            <div class="Subscribe-box">
                                <p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit.</p>
                                <form class="form-inline" action="#" method="GET">
                                    <input type="text" class="form-control mb-sm-0" id="inlineFormInputName3" placeholder="Email Address">
                                    <button type="submit" class="btn"><i class="fa fa-paper-plane"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sub-footer">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6">
                            <p class="copy">© 2019 <a href="#">Theme Vessel.</a> All Rights Reserved.</p>
                        </div>
                        <div class="col-lg-6">
                            <div class="social-list-2">
                                <ul>
                                    @foreach($settings->socialNetworks as $network)
                                        <li><a href="{{$network['link']}}" class="{{$network['network']}}-bg"><i class="fa fa-{{$network['network']}}"></i></a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- Footer end -->

        <!-- Full Page Search -->
        <div id="full-page-search">
            <button type="button" class="close">×</button>
            <form action="index.html#">
                <input type="search" value="" placeholder="type keyword(s) here" />
                <button type="submit" class="btn btn-sm button-theme">Search</button>
            </form>
        </div>

        <!-- Car Overview Modal -->
        <div class="car-model-2">
            <div class="modal fade" id="carOverviewModal" tabindex="-1" role="dialog" aria-labelledby="carOverviewModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="modal-title" id="carOverviewModalLabel">
                                <h4>Find Your Dream Car</h4>
                                <h5><i class="flaticon-pin"></i> 123 Kathal St. Tampa City,</h5>
                            </div>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row modal-raw">
                                <div class="col-lg-6 modal-left">
                                    <div class="item active">
                                        <img src="{{ asset('assets/img/car-11.png') }}" alt="best-car" class="img-fluid">
                                        <div class="sobuz">
                                            <div class="price-box">
                                                <span class="del"><del>$950.00</del></span>
                                                <br>
                                                <span class="del-2">$1050.00</span>
                                            </div>
                                            <div class="ratings-2">
                                                <span class="ratings-box">4.5/5</span>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star-o"></i>
                                                ( 7 Reviews )
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 modal-right">
                                    <div class="modal-right-content">
                                        <section>
                                            <h3>Features</h3>
                                            <div class="features">
                                                <ul class="bullets">
                                                    <li>Cruise Control</li>
                                                    <li>Airbags</li>
                                                    <li>Air Conditioning</li>
                                                    <li>Alarm System</li>
                                                    <li>Audio Interface</li>
                                                    <li>CDR Audio</li>
                                                    <li>Seat Heating</li>
                                                    <li>ParkAssist</li>
                                                </ul>
                                            </div>
                                        </section>
                                        <section>
                                            <h3>Overview</h3>
                                            <ul class="bullets">
                                                <li>Model</li>
                                                <li>Year</li>
                                                <li>Condition</li>
                                                <li>Price</li>
                                                <li>Audi</li>
                                                <li>2020</li>
                                                <li>Brand New</li>
                                                <li>$178,000</li>
                                            </ul>
                                        </section>
                                        <div class="description">
                                            <h3>Description</h3>
                                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard.</p>
                                            <a href="car-details.html" class="btn btn-md btn-round btn-theme">Show Detail</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="{{ asset('assets/js/jquery-2.2.0.min.js') }}"></script>
        <script src="{{ asset('assets/js/popper.min.js') }}"></script>
        <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/js/bootstrap-submenu.js') }}"></script>
        <script src="{{ asset('assets/js/rangeslider.js') }}"></script>
        <script src="{{ asset('assets/js/jquery.mb.YTPlayer.js') }}"></script>
        <script src="{{ asset('assets/js/bootstrap-select.min.js') }}"></script>
        <script src="{{ asset('assets/js/jquery.easing.1.3.js') }}"></script>
        <script src="{{ asset('assets/js/jquery.scrollUp.js') }}"></script>
        <script src="{{ asset('assets/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
        <script src="{{ asset('assets/js/dropzone.js') }}"></script>
        <script src="{{ asset('assets/js/slick.min.js') }}"></script>
        <script src="{{ asset('assets/js/jquery.filterizr.js') }}"></script>
        <script src="{{ asset('assets/js/jquery.magnific-popup.min.js') }}"></script>
        <script src="{{ asset('assets/js/jquery.countdown.js') }}"></script>
        <script src="{{ asset('assets/js/jquery.mousewheel.min.js') }}"></script>
        <script src="{{ asset('assets/js/lightgallery-all.js') }}"></script>
        <script src="{{ asset('assets/js/jnoty.js') }}"></script>
        <script src="{{ asset('assets/js/sidebar.js') }}"></script>
        <script src="{{ asset('assets/js/app.js') }}"></script>

        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="{{ asset('assets/js/ie10-viewport-bug-workaround.js') }}"></script>
        <!-- Custom javascript -->
        <script src="{{ asset('assets/js/ie10-viewport-bug-workaround.js') }}"></script>

        @yield('js')
    </body>
</html>

