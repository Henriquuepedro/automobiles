<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <title>@yield('title')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        @yield('css_pre')

        <!-- External CSS libraries -->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/user/css/bootstrap.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/user/css/animate.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/user/css/bootstrap-submenu.css') }}">

        <link rel="stylesheet" type="text/css" href="{{ asset('assets/user/css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/user/css/magnific-popup.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/user/fonts/font-awesome/css/font-awesome.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/user/fonts/flaticon/font/flaticon.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/user/fonts/linearicons/style.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/user/css/jquery.mCustomScrollbar.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/user/css/dropzone.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/user/css/slick.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/user/css/lightbox.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/user/css/jnoty.css') }}">

        <!-- Custom stylesheet -->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/user/css/sidebar.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/user/css/style.css') }}">
        <link rel="stylesheet" type="text/css" id="style_sheet" href="{{ asset('assets/user/css/custom/styles.css') }}">

        <!-- Favicon icon -->
        <link rel="shortcut icon" href="{{ $settings->logotipo }}" type="image/x-icon" >

        <!-- Google fonts -->
        <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,600,700%7CUbuntu:300,400,700" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700,800,900&display=swap" rel="stylesheet">

        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/user/css/ie10-viewport-bug-workaround.css') }}">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">

        @yield('css')

        <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
        <!--[if lt IE 9]><script  src="{{ asset('assets/user/js/ie8-responsive-file-warning.js') }}"></script><![endif]-->
        <script src="{{ asset('assets/user/js/ie-emulation-modes-warning.js') }}"></script>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="{{ asset('assets/user/js/html5shiv.min.js') }}"></script>
        <script src="{{ asset('assets/user/js/respond.min.js') }}"></script>
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
                            <a href="mailto:{{ $settings->storeEmail }}"><i class="fa fa-envelope"></i>{{ $settings->storeEmail }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Top header end -->

        <!-- Main header start -->
        <header class="main-header sticky-header sh-2">
            <div class="container">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <a class="navbar-brand company-logo-2" href="{{ route('user.home') }}">
                        <img src="{{$settings->logotipo }}" alt="logo">
                    </a>
                    <button class="navbar-toggler" type="button" id="drawer">
                        <span class="fa fa-bars"></span>
                    </button>
                    <div class="navbar-collapse collapse w-100" id="navbar">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item dropdown">
                                <a class="nav-link" href="{{ route('user.home') }}">Início</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link" href="{{ route('user.auto.list') }}">Estoque</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link" href="{{ route('user.contact.index') }}">Contato</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link" href="{{ route('user.about.index') }}">Sobre</a>
                            </li>
                            @foreach ($settings->pages as $page)
                                <li class="nav-item dropdown">
                                    <a class="nav-link" href="{{ route('user.pageDynamic.view', ['page' => $page->nome]) }}">{{ $page->title }}</a>
                                </li>
                            @endforeach
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
                    <a href="{{ route('user.home') }}">
                        <img src="{{$settings->logotipo }}" alt="sidebarlogo">
                    </a>
                </div>
                <div class="sidebar-navigation">
                    <h3 class="heading">Páginas</h3>
                    <ul class="menu-list">
                        <li>
                            <a href="{{ route('user.home') }}">Início</a>
                        </li>
                        <li>
                            <a href="{{ route('user.auto.list') }}">Estoque</a>
                        </li>
                        <li>
                            <a href="{{ route('user.contact.index') }}">Contato</a>
                        </li>
                        <li>
                            <a href="{{ route('user.about.index') }}">Sobre</a>
                        </li>
                        @foreach ($settings->pages as $page)
                            <li>
                                <a href="{{ route('user.pageDynamic.view', ['page' => $page->nome]) }}">{{ $page->title }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="get-in-touch">
                    <h3 class="heading">Contato</h3>
                    <div class="media">
                        <i class="flaticon-phone"></i>
                        <div class="media-body">
                            <a href="tel:{{ $settings->storePhonePrimary }}">{{ $settings->storePhonePrimary }}</a>
                        </div>
                    </div>
                    <div class="media">
                        <i class="flaticon-mail"></i>
                        <div class="media-body">
                            <a href="mailto:{{ $settings->storeEmail }}">{{ $settings->storeEmail }}</a>
                        </div>
                    </div>
                </div>
                <div class="get-social">
                    <h3 class="heading">Redes Sociais</h3>
                    @foreach ($settings->socialNetworks as $network)
                        <a href="{{$network['link']}}" class="{{$network['network']}}-bg" target="_blank"><i class="fab fa-{{$network['network']}}"></i></a>
                    @endforeach
                </div>
            </div>
        </nav>
        <!-- Sidenav end -->

        @yield('body')

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
                                <p>{!! $settings->shortAbout !!}</p>
                            </div>
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
                                <li>
                                    <a href="{{ route('user.auto.list') }}"><i class="fa fa-angle-right"></i>Estoque</a>
                                </li>
                                <li>
                                    <a href="{{ route('user.contact.index') }}"><i class="fa fa-angle-right"></i>Contato</a>
                                </li>
                                <li>
                                    <a href="{{ route('user.about.index') }}"><i class="fa fa-angle-right"></i>Sobre</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                        <div class="footer-item clearfix">
                            <h4>Informações de Contato</h4>
                            <div class="s-border"></div>
                            <div class="m-border"></div>
                            <ul class="contact-info">
                                <li>
                                    <i class="flaticon-pin"></i><a href="https://www.google.com/maps/dir//{{ str_replace('/', ' - ', $settings->address) }}" class="address-stores-link-google" target="_blank">{{ $settings->address }}</a>
                                </li>
                                <li>
                                    <i class="flaticon-mail"></i><a href="mailto:{{ $settings->storeEmail }}">{{ $settings->storeEmail }}</a>
                                </li>
                                <li>
                                    @if ($settings->storeWhatsPhonePrimary)
                                        <a href="https://api.whatsapp.com/send?phone=55{{ $settings->storePhonePrimary_n }}">{{ $settings->storePhonePrimary }}</a>
                                        <i class="fab fa-whatsapp"></i>
                                    @else
                                        <a href="tel:{{ $settings->storePhonePrimary_n }}">{{ $settings->storePhonePrimary }}</a>
                                        <i class="flaticon-phone"></i>
                                    @endif
                                </li>
                                <li>
                                    @if ($settings->storeWhatsPhoneSecondary)
                                        <a href="https://api.whatsapp.com/send?phone=55{{ $settings->storePhoneSecondary_n }}">{{ $settings->storePhoneSecondary }}</a>
                                        <i class="fab fa-whatsapp"></i>
                                    @else
                                        <a href="tel:{{ $settings->storePhoneSecondary_n }}">{{ $settings->storePhoneSecondary }}</a>
                                        <i class="flaticon-phone"></i>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                        <div class="footer-item clearfix description-service">
                            <h4>Atendimento</h4>
                            <div class="s-border"></div>
                            <div class="m-border"></div>
                            {!! $settings->descriptionService !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="sub-footer">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6">
                            <p class="copy">© {{ date('Y') }} <a href="https://www.manusoftware.com.br/" target="_blank">Manu Software.</a> All Rights Reserved.</p>
                        </div>
                        <div class="col-lg-6">
                            <div class="social-list-2">
                                <ul>
                                    @foreach ($settings->socialNetworks as $network)
                                        <li><a href="{{$network['link']}}" class="{{$network['network']}}-bg" target="_blank"><i class="fab fa-{{$network['network']}}"></i></a></li>
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
            <form action="{{ route('user.auto.list') }}" method="GET">
                <input type="search" name="search-text" placeholder="Digite sua busca aqui" />
                <button type="submit" class="btn btn-sm button-theme">Procurar</button>
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
                                <div class="col-lg-6 modal-left d-flex align-self-center">
                                    <div class="item active">
                                        <img src="{{ asset('assets/user/img/car-11.png') }}" alt="best-car" class="img-fluid">
                                        <div class="sobuz">
                                            <div class="price-box">
                                                <span class="del-2">$1050.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 modal-right">
                                    <div class="modal-right-content">
                                        <section>
                                            <h3>Dados Automóvel</h3>
                                            <div class="features">
                                                <table class="auto bullets"></table>
                                                <table class="complements bullets"></table>
                                            </div>
                                        </section>
                                        <section>
                                            <h3>Opcionais</h3>
                                            <table class="optionals bullets"></table>
                                        </section>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($settings->storeWhatsPhonePrimary)
            <a href="https://api.whatsapp.com/send?phone=55{{ $settings->storePhonePrimary_n }}" class="btn-whatsapp-float" target="_blank">
                <i style="margin-top:16px" class="fab fa-whatsapp"></i>
            </a>
        @endif

        <script src="{{ asset('assets/user/js/jquery-2.2.0.min.js') }}"></script>
        <script src="{{ asset('assets/user/js/popper.min.js') }}"></script>
        <script src="{{ asset('assets/user/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/user/js/bootstrap-submenu.js') }}"></script>
        <script src="{{ asset('assets/user/js/rangeslider.js') }}"></script>
        <script src="{{ asset('assets/user/js/jquery.mb.YTPlayer.js') }}"></script>
        <script src="{{ asset('assets/user/js/bootstrap-select.min.js') }}"></script>
        <script src="{{ asset('assets/user/js/jquery.easing.1.3.js') }}"></script>
        <script src="{{ asset('assets/user/js/jquery.scrollUp.js') }}"></script>
        <script src="{{ asset('assets/user/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
        <script src="{{ asset('assets/user/js/dropzone.js') }}"></script>
        <script src="{{ asset('assets/user/js/slick.min.js') }}"></script>
        <script src="{{ asset('assets/user/js/jquery.filterizr.js') }}"></script>
        <script src="{{ asset('assets/user/js/jquery.magnific-popup.min.js') }}"></script>
        <script src="{{ asset('assets/user/js/jquery.countdown.js') }}"></script>
        <script src="{{ asset('assets/user/js/jquery.mousewheel.min.js') }}"></script>
        <script src="{{ asset('assets/user/js/lightgallery-all.js') }}"></script>
        <script src="{{ asset('assets/user/js/jnoty.js') }}"></script>
        <script src="{{ asset('assets/user/js/sidebar.js') }}"></script>
        <script src="{{ asset('assets/user/js/app.js') }}"></script>

        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="{{ asset('assets/user/js/ie10-viewport-bug-workaround.js') }}"></script>
        <!-- Custom javascript -->
        <script src="{{ asset('assets/user/js/ie10-viewport-bug-workaround.js') }}"></script>

        @yield('js')
    </body>
</html>

