<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <title>@yield('title')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">

        @yield('css_pre_template')

        <!-- External CSS libraries -->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/animate.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap-submenu.css') }}">

        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/magnific-popup.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/fonts/font-awesome/css/font-awesome.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/fonts/flaticon/font/flaticon.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/fonts/linearicons/style.css') }}">
        <link rel="stylesheet" type="text/css"  href="{{ asset('assets/css/jquery.mCustomScrollbar.css') }}">
        <link rel="stylesheet" type="text/css"  href="{{ asset('assets/css/dropzone.css') }}">
        <link rel="stylesheet" type="text/css"  href="{{ asset('assets/css/slick.css') }}">
        <link rel="stylesheet" type="text/css"  href="{{ asset('assets/css/lightbox.min.css') }}">
        <link rel="stylesheet" type="text/css"  href="{{ asset('assets/css/jnoty.css') }}">

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


        @yield('css_template')

        <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
        <!--[if lt IE 9]><script  src="{{ asset('assets/js/ie8-responsive-file-warning.js') }}"></script><![endif]-->
        <script  src="{{ asset('assets/js/ie-emulation-modes-warning.js') }}"></script>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script  src="{{ asset('assets/js/html5shiv.min.js') }}"></script>
        <script  src="{{ asset('assets/js/respond.min.js') }}"></script>
        <![endif]-->

        @yield('js_head_template')

    </head>
    <body>
        <div class="page_loader"></div>

        <!-- Top header start -->
        <header class="top-header bg-active" id="top-header-2">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-8 col-sm-7">
                        <div class="list-inline">
                            <a href="tel:1-8X0-666-8X88"><i class="fa fa-phone"></i>Need Support? 1-8X0-666-8X88</a>
                            <a href="tel:info@themevessel.com"><i class="fa fa-envelope"></i>info@themevessel.com</a>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-4 col-sm-5">
                        <ul class="top-social-media pull-right">
                            <li>
                                <a href="login.html" class="sign-in"><i class="fa fa-sign-in"></i> Login</a>
                            </li>
                            <li>
                                <a href="signup.html" class="sign-in"><i class="fa fa-user"></i> Register</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>
        <!-- Top header end -->

        <!-- Main header start -->
        <header class="main-header sticky-header sh-2">
            <div class="container">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <a class="navbar-brand company-logo-2" href="index.html">
                        <img src="{{ asset('assets/img/logos/black-logo.png') }}" alt="logo">
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
                        <img src="{{ asset('assets/img/logos/black-logo.png') }}" alt="sidebarlogo">
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
