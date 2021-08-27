$(function () {

    'use strict';

    // Showing page loader
    $(window).on('load', function () {
        setColorLayout();
        populateColorPlates();

        if ($('body .filter-portfolio').length > 0) {
            $(function () {
                $('.filter-portfolio').filterizr(
                    {
                        delay: 0
                    }
                );
            });
            $('.filteriz-navigation li').on('click', function () {
                $('.filteriz-navigation .filtr').removeClass('active');
                $(this).addClass('active');
            });
        }
    });


    // Made the left sidebar's min-height to window's height
    var winHeight = $(window).height();
    $('.dashboard-nav').css('min-height', winHeight);


    // Magnify activation
    $('.portfolio-item').magnificPopup({
        delegate: 'a',
        type: 'image',
        gallery:{enabled:true}
    });

    $(".car-magnify-gallery").lightGallery();

    $(document).on('click', '.compare-btn', function () {
        if($(this).hasClass('active')){
            $(this).removeClass('active');
            $.jnoty("Car has been removed from compare list", {
                header: 'Warning',
                sticky: true,
                theme: 'jnoty-warning',
                icon: 'fa fa-check-circle'
            });

        } else {
            $(this).addClass('active');
            $.jnoty("Car has been added to compare list", {
                header: 'Success',
                sticky: true,
                theme: 'jnoty-success',
                icon: 'fa fa-check-circle'
            });
        }
    });

    $(document).on('click', '.wishlist-btn', function () {
        if($(this).hasClass('active')){
            $(this).removeClass('active');
            $.jnoty("Car has been removed from wishlist list", {
                header: 'Warning',
                sticky: true,
                theme: 'jnoty-warning',
                icon: 'fa fa-check-circle'
            });

        } else {
            $(this).addClass('active');
            $.jnoty("Car has been added to wishlist list", {
                header: 'Success',
                sticky: true,
                theme: 'jnoty-success',
                icon: 'fa fa-check-circle'
            });
        }
    });


    // Header shrink while page scroll
    adjustHeader();
    doSticky();
    placedDashboard();
    $(window).on('scroll', function () {
        adjustHeader();
        doSticky();
        placedDashboard();
    });

    // Header shrink while page resize
    $(window).on('resize', function () {
        adjustHeader();
        doSticky();
        placedDashboard();
    });

    function adjustHeader()
    {
        var windowWidth = $(window).width();
        if(windowWidth > 0) {
            if ($(document).scrollTop() >= 100) {
                if($('.header-shrink').length < 1) {
                    $('.sticky-header').addClass('header-shrink');
                }
                if($('.do-sticky').length < 1) {
                    $('.company-logo img').attr('src', 'img/logos/black-logo.png');
                }
            }
            else {
                $('.sticky-header').removeClass('header-shrink');
                if($('.do-sticky').length < 1 && $('.fixed-header').length == 0 && $('.fixed-header2').length == 0) {
                    $('.company-logo img').attr('src', 'img/logos/logo.png');
                } else {
                    $('.company-logo img').attr('src', 'img/logos/black-logo.png');
                }
            }
        } else {
            $('.company-logo img').attr('src', 'img/logos/black-logo.png');
        }
    }

    function doSticky()
    {
        if ($(document).scrollTop() > 40) {
            $('.do-sticky').addClass('sticky-header');
            //$('.do-sticky').addClass('header-shrink');
        }
        else {
            $('.do-sticky').removeClass('sticky-header');
            //$('.do-sticky').removeClass('header-shrink');
        }
    }

    function placedDashboard() {
        var headerHeight = parseInt($('.main-header').height(), 10);
        $('.dashboard').css('top', headerHeight);
    }


    // Banner slider
    (function ($) {
        //Function to animate slider captions
        function doAnimations(elems) {
            //Cache the animationend event in a variable
            var animEndEv = 'webkitAnimationEnd animationend';
            elems.each(function () {
                var $this = $(this),
                    $animationType = $this.data('animation');
                $this.addClass($animationType).one(animEndEv, function () {
                    $this.removeClass($animationType);
                });
            });
        }

        //Variables on page load
        var $myCarousel = $('#carousel-example-generic')
        var $firstAnimatingElems = $myCarousel.find('.item:first').find("[data-animation ^= 'animated']");
        //Initialize carousel
        $myCarousel.carousel();

        //Animate captions in first slide on page load
        doAnimations($firstAnimatingElems);
        //Pause carousel
        $myCarousel.carousel('pause');
        //Other slides to be animated on carousel slide event
        $myCarousel.on('slide.bs.carousel', function (e) {
            var $animatingElems = $(e.relatedTarget).find("[data-animation ^= 'animated']");
            doAnimations($animatingElems);
        });
        $('#carousel-example-generic').carousel({
            interval: 3000,
            pause: "false"
        });
    })(jQuery);

    // Page scroller initialization.
    $.scrollUp({
        scrollName: 'page_scroller',
        scrollDistance: 300,
        scrollFrom: 'top',
        scrollSpeed: 500,
        easingType: 'linear',
        animation: 'fade',
        animationSpeed: 200,
        scrollTrigger: false,
        scrollTarget: false,
        scrollText: '<i class="fa fa-chevron-up"></i>',
        scrollTitle: false,
        scrollImg: false,
        activeOverlay: false,
        zIndex: 2147483647
    });

    // Counter
    function isCounterElementVisible($elementToBeChecked) {
        var TopView = $(window).scrollTop();
        var BotView = TopView + $(window).height();
        var TopElement = $elementToBeChecked.offset().top;
        var BotElement = TopElement + $elementToBeChecked.height();
        return ((BotElement <= BotView) && (TopElement >= TopView));
    }

    $(window).on('scroll', function () {
        $(".counter").each(function () {
            var isOnView = isCounterElementVisible($(this));
            if (isOnView && !$(this).hasClass('Starting')) {
                $(this).addClass('Starting');
                $(this).prop('Counter', 0).animate({
                    Counter: $(this).text()
                }, {
                    duration: 3000,
                    easing: 'swing',
                    step: function (now) {
                        $(this).text(Math.ceil(now));
                    }
                });
            }
        });
    });


    // Countdown activation
    $( function() {
        // Add background image
        //$.backstretch('../img/nature.png');
        var endDate = "December  27, 2019 15:03:25";
        $('.countdown.simple').countdown({ date: endDate });
        $('.countdown.styled').countdown({
            date: endDate,
            render: function(data) {
                $(this.el).html("<div>" + this.leadingZeros(data.days, 3) + " <span>Days</span></div><div>" + this.leadingZeros(data.hours, 2) + " <span>Hours</span></div><div>" + this.leadingZeros(data.min, 2) + " <span>Minutes</span></div><div>" + this.leadingZeros(data.sec, 2) + " <span>Seconds</span></div>");
            }
        });
        $('.countdown.callback').countdown({
            date: +(new Date) + 10000,
            render: function(data) {
                $(this.el).text(this.leadingZeros(data.sec, 2) + " sec");
            },
            onEnd: function() {
                $(this.el).addClass('ended');
            }
        }).on("click", function() {
            $(this).removeClass('ended').data('countdown').update(+(new Date) + 10000).start();
        });

    });

    // Search option's icon toggle
    $('.search-options-btn').on('click', function () {
        $('.search-section').toggleClass('show-search-area');
        $('.search-options-btn .fa').toggleClass('fa-chevron-down');
    });

    // Carousel with partner initialization
    (function () {
        $('#ourPartners').carousel({interval: 3600});
    }());

    (function () {
        $('.our-partners .item').each(function () {
            var itemToClone = $(this);
            for (var i = 1; i < 4; i++) {
                itemToClone = itemToClone.next();
                if (!itemToClone.length) {
                    itemToClone = $(this).siblings(':first');
                }
                itemToClone.children(':first-child').clone()
                    .addClass("cloneditem-" + (i))
                    .appendTo($(this));
            }
        });
    }());

    // Background video playing script
    $(document).ready(function () {
        $(".player").mb_YTPlayer(
            {
                mobileFallbackImage: 'img/banner/banner-1.png'
            }
        );
    });

    // Multilevel menuus
    $('[data-submenu]').submenupicker();

    // Expending/Collapsing advance search content
    $('.show-more-options').on('click', function () {
        if ($(this).find('.fa').hasClass('fa-minus-circle')) {
            $(this).find('.fa').removeClass('fa-minus-circle');
            $(this).find('.fa').addClass('fa-plus-circle');
        } else {
            $(this).find('.fa').removeClass('fa-plus-circle');
            $(this).find('.fa').addClass('fa-minus-circle');
        }
    });

    var videoWidth = $('.sidebar-widget').width();
    var videoHeight = videoWidth * .61;
    $('.sidebar-widget iframe').css('height', videoHeight);


    // Megamenu activation
    $(".megamenu").on("click", function (e) {
        e.stopPropagation();
    });

    // Dropdown activation
    $('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
        if (!$(this).next().hasClass('show')) {
            $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
        }
        var $subMenu = $(this).next(".dropdown-menu");
        $subMenu.toggleClass('show');


        $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
            $('.dropdown-submenu .show').removeClass("show");
        });

        return false;
    });


    // Full  Page Search Activation
    $(function () {
        $('a[href="#full-page-search"]').on('click', function(event) {
            event.preventDefault();
            $('#full-page-search').addClass('open');
            $('#full-page-search > form > input[type="search"]').focus();
        });

        $('#full-page-search, #full-page-search button.close').on('click keyup', function(event) {
            if (event.target == this || event.target.className == 'close' || event.keyCode == 27) {
                $(this).removeClass('open');
            }
        });
    });


    // Slick Sliders
    $('.slick-carousel').each(function () {
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


    $(".dropdown.btns .dropdown-toggle").on('click', function() {
        $(this).dropdown("toggle");
        return false;
    });



    // Dropzone initialization
    Dropzone.autoDiscover = false;
    $(function () {
        $("div#myDropZone").dropzone({
            url: "/file-upload"
        });
    });

    // Filterizr initialization
    $(function () {
        //$('.filtr-container').filterizr();
    });

    function toggleChevron(e) {
        $(e.target)
            .prev('.panel-heading')
            .find(".fa")
            .toggleClass('fa-minus fa-plus');
    }

    $('.panel-group').on('shown.bs.collapse', toggleChevron);
    $('.panel-group').on('hidden.bs.collapse', toggleChevron);

    // Switching Color schema
    function populateColorPlates() {
        var plateStings = '<div class="option-panel option-panel-collased">\n' +
            '    <h2>Change Color</h2>\n' +
            '    <div class="color-plate default-plate" data-color="default"></div>\n' +
            '    <div class="color-plate midnight-blue-plate" data-color="midnight-blue"></div>\n' +
            '    <div class="color-plate yellow-plate" data-color="yellow"></div>\n' +
            '    <div class="color-plate blue-plate" data-color="blue"></div>\n' +
            '    <div class="color-plate green-light-plate" data-color="green-light"></div>\n' +
            '    <div class="color-plate yellow-light-plate" data-color="yellow-light"></div>\n' +
            '    <div class="color-plate green-plate" data-color="green"></div>\n' +
            '    <div class="color-plate green-light-2-plate" data-color="green-light-2"></div>\n' +
            '    <div class="color-plate red-plate" data-color="red"></div>\n' +
            '    <div class="color-plate purple-plate" data-color="purple"></div>\n' +
            '    <div class="color-plate brown-plate" data-color="brown"></div>\n' +
            '    <div class="color-plate olive-plate" data-color="olive"></div>\n' +
            '    <div class="setting-button">\n' +
            '        <i class="fa fa-gear"></i>\n' +
            '    </div>\n' +
            '</div>';
        $('body').append(plateStings);
    }
    $(document).on('click', '.color-plate', function () {
        var name = $(this).attr('data-color');
        $('link[id="style_sheet"]').attr('href', 'css/skins/' + name + '.css');
    });

    $(document).on('click', '.setting-button', function () {
        $('.option-panel').toggleClass('option-panel-collased');
    });
});

// mCustomScrollbar initialization
(function ($) {
    $(window).resize(function () {
        $('#map').css('height', $(this).height() - 110);
        if ($(this).width() > 768) {
            $(".map-content-sidebar").mCustomScrollbar(
                {theme: "minimal-dark"}
            );
            $('.map-content-sidebar').css('height', $(this).height() - 110);
        } else {
            $('.map-content-sidebar').mCustomScrollbar("destroy"); //destroy scrollbar
            $('.map-content-sidebar').css('height', '100%');
        }
    }).trigger("resize");
})(jQuery);

$(document).on('click', '.view-details-auto', async function(){
    const id = $(this).data('id');

    await getDataAutoPreview(id);
    await $('#carOverviewModal').modal();
});

const getDataAutoPreview = id => {
    $.get(`${window.location.origin}/ajax/automoveis/buscar/${id}`, data => {

        const modal = $('#carOverviewModal');

        modal.find('.optionals').empty();
        modal.find('.complements').empty();
        modal.find('.auto').empty();

        modal.find('.auto').append(`<tr><td>Modelo</td><td>${data.auto.modelo_nome}</td></tr>`);
        modal.find('.auto').append(`<tr><td>Marca</td><td>${data.auto.marca_nome}</td></tr>`);
        modal.find('.auto').append(`<tr><td>Ano</td><td>${data.auto.ano_nome}</td></tr>`);
        modal.find('.auto').append(`<tr><td>Cor</td><td>${data.auto.cor}</td></tr>`);
        modal.find('.auto').append(`<tr><td>Kms</td><td>${data.auto.kms}</td></tr>`);
        modal.find('.item.active img').attr('src', `${window.location.origin}/${data.auto.file}`);
        modal.find('.price-box span').text(data.auto.valor);


        let htmlOptional = '';
        $.each(data.optional, function (key, value) {
            if (key % 2 === 0) htmlOptional += '<tr>';

            htmlOptional += `<td>${value.name}</td>`;

            if (key % 2 !== 0 || (key + 1) === data.optional.length) htmlOptional += '</tr>';
        });
        modal.find('.optionals').append(htmlOptional);

        $.each(data.complement, function (key, value) {
            modal.find('.complements').append(`<tr><td>${value.name}</td><td>${value.value}</td></tr>`);
        });
    });
}

const getAutosFeatured = () => {

    $('.order-home-page').append(`
    <div class="featured-car content-area">
        <div class="container">
            <!-- Main title -->
            <div class="main-title">
                <h1>Automóveis em Destaque</h1>
            </div>
            <div class="row list-autos"></div>
        </div>
    </div>
    `);

    $.get(`${window.location.origin}/ajax/automoveis/listagem/destaque`, function (autos) {
        $('.featured-car .list-autos').empty();

        $.each(autos, function (key, value) {

            $('.featured-car .list-autos').append('<div class="col-lg-4 col-md-6"><div class="car-box-3">'+getCardAuto(value, 'Destaque')+'</div></div>');
        });
    }, 'JSON').fail(function(e) {
        console.log(e);
    });
}

const getCardAuto = (value, tag = '') => {
    return `
    <div class="car-thumbnail">
        <a href="${window.location.origin}/automovel/${value.auto_id}" class="car-img">
            <div class="tag-2 bg-active">${tag}</div>
            <img class="d-block w-100" src="${window.location.origin}/${value.file}" alt="car">
        </a>
        <div class="carbox-overlap-wrapper">
            <div class="overlap-box">
                <div class="overlap-btns-area">
                    <a class="overlap-btn" href="${window.location.origin}/automovel/${value.auto_id}" ">
                        <i class="fa fa-eye"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="detail">
        <h1 class="title">
            <a href="${window.location.origin}/automovel/${value.auto_id}">${value.modelo_nome}</a>
        </h1>
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
    `;
}

const getBannerHomePage = () => {

    $('.order-home-page').append(`
    <div class="banner" id="banner">
        <div id="bannerCarousole" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner banner-slider-inner text-center banner-home-page"></div>
        </div>
    </div>
    `);

    const bodyBanner = $('.banner-home-page');
    let active = '';
    let countBanner = 0;

    $.get(`${window.location.origin}/ajax/banner/inicio`, function (autos) {
        $.each(autos, function (key, value) {
            active = key === 0 ? 'active' : '';
            bodyBanner.append(`
                <div class="carousel-item banner-max-height ${active} item-bg">
                    <img class="d-block w-100 h-100" src="${value}" alt="banner">
                </div>
            `);
            countBanner++;
        });

        if (countBanner > 1) {
            $('#bannerCarousole').append(`
            <a class="carousel-control-prev none-580" href="#bannerCarousole" role="button" data-slide="prev">
                <span class="slider-mover-left" aria-hidden="true">
                    <i class="fa fa-angle-left"></i>
                </span>
            </a>
            <a class="carousel-control-next none-580" href="#bannerCarousole" role="button" data-slide="next">
                <span class="slider-mover-right" aria-hidden="true">
                    <i class="fa fa-angle-right"></i>
                </span>
            </a>`);
        }
    }, 'JSON').fail(function(e) {
        console.log(e);
    });
}

const getTestimonyHomePage = () => {

    $('.order-home-page').append(`
    <div class="testimonial-3">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Main title -->
                    <div class="main-title">
                        <h1>Depoimentos dos nossos clientes</h1>
                    </div>
                </div>
                <div class="col-lg-12">
                    <!-- Slick slider area start -->
                    <div class="slick-slider-area">
                        <div class="row slick-carousel-blog-home" data-slick='{"slidesToShow": 2, "responsive":[{"breakpoint": 1024,"settings":{"slidesToShow": 1}}, {"breakpoint": 768,"settings":{"slidesToShow": 1}}]}'>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `);

    let bodyTestimony = $('.order-home-page .slick-carousel-blog-home');
    let stars = '';
    let starYellow = '';
    $.get(`${window.location.origin}/ajax/depoimento/primario`, function (testimonies) {

        $.each(testimonies, function (key, testimony) {
            stars = '';
            for (let s = 0; s < 5; s++) {
                starYellow = s < testimony.rate ? '' : '-o';
                stars += `<i class="fa fa-star${starYellow}"></i>`;
            }

            bodyTestimony.append(`
            <div class="slick-slide-item">
                <div class="testimonial-item-new">
                    <div class="author-img fix">
                        <div class="author-avatar">
                            <img src="${window.location.origin}/assets/admin/dist/images/testimony/${testimony.id}/${testimony.picture}" alt="testimonial-${testimony.picture}">
                            <div class="icon">
                                <i class="fa fa-quote-right"></i>
                            </div>
                        </div>
                    </div>
                    <div class="author-content">
                        <h5 class="left-line pl-40">${testimony.name}</h5>
                    </div>
                    <p>${testimony.testimony}</p>
                    <div class="rating">
                        ${stars}
                    </div>
                </div>
            </div>
            `);
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

const getBlogHomePage = () => {
    $('.order-home-page').append(`
    <div class="blog content-area">
        <div class="container">
            <!-- Main title -->
            <div class="main-title">
                <h1>Our Blog</h1>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            </div>
            <!-- Slick slider area start -->
            <div class="slick-slider-area">
                <div class="row slick-carousel-blog-home" data-slick='{"slidesToShow": 3, "responsive":[{"breakpoint": 1024,"settings":{"slidesToShow": 2}}, {"breakpoint": 768,"settings":{"slidesToShow": 1}}]}'>
                    <div class="slick-slide-item">
                        <div class="blog-3">
                            <div class="blog-image">
                                <img src="${window.location.origin}/assets/user/img/blog/blog-2.png" alt="blog" class="img-fluid bp">
                                <div class="date-box-2 db-2">14 Aug</div>
                                <div class="post-meta clearfix">
                                    <span><a href="#"><i class="flaticon-user-1"></i></a>Admin</span>
                                    <span><a href="#"><i class="flaticon-comment"></i></a>17K</span>
                                    <span><a href="#"><i class="flaticon-calendar"></i></a>73k</span>
                                </div>
                            </div>
                            <div class="detail">
                                <h3>
                                    <a href="blog-details.html">Buying a Best Sports Car</a>
                                </h3>
                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's</p>
                                <a href="blog-details.html" class="b-btn">Rea more...!</a>
                            </div>
                        </div>
                    </div>
                    <div class="slick-slide-item">
                        <div class="blog-3">
                            <div class="blog-image">
                                <img src="${window.location.origin}/assets/user/img/blog/blog-1.png" alt="blog-3" class="img-fluid bp">
                                <div class="date-box-2 db-2">27 Nov</div>
                                <div class="post-meta clearfix">
                                    <span><a href="#"><i class="flaticon-user-1"></i></a>Admin</span>
                                    <span><a href="#"><i class="flaticon-comment"></i></a>17K</span>
                                    <span><a href="#"><i class="flaticon-calendar"></i></a>73k</span>
                                </div>
                            </div>
                            <div class="detail">
                                <h3>
                                    <a href="blog-details.html">Selling Your New Cars?</a>
                                </h3>
                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's</p>
                                <a href="blog-details.html" class="b-btn">Rea more...!</a>
                            </div>
                        </div>
                    </div>
                    <div class="slick-slide-item">
                        <div class="blog-3">
                            <div class="blog-image">
                                <img src="${window.location.origin}/assets/user/img/blog/blog-2.png" alt="blog-3" class="img-fluid bp">
                                <div class="date-box-2 db-2">09 Sep</div>
                                <div class="post-meta clearfix">
                                    <span><a href="#"><i class="flaticon-user-1"></i></a>Admin</span>
                                    <span><a href="#"><i class="flaticon-comment"></i></a>17K</span>
                                    <span><a href="#"><i class="flaticon-calendar"></i></a>73k</span>
                                </div>
                            </div>
                            <div class="detail">
                                <h3>
                                    <a href="blog-details.html">Find Your Dream Car</a>
                                </h3>
                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's</p>
                                <a href="blog-details.html" class="b-btn">Rea more...!</a>
                            </div>
                        </div>
                    </div>
                    <div class="slick-slide-item">
                        <div class="blog-3">
                            <div class="blog-image">
                                <img src="${window.location.origin}/assets/user/img/blog/blog-3.png" alt="blog-3" class="img-fluid bp">
                                <div class="date-box-2 db-2">08 Nov</div>
                                <div class="post-meta clearfix">
                                    <span><a href="#"><i class="flaticon-user-1"></i></a>Admin</span>
                                    <span><a href="#"><i class="flaticon-comment"></i></a>17K</span>
                                    <span><a href="#"><i class="flaticon-calendar"></i></a>73k</span>
                                </div>
                            </div>
                            <div class="detail">
                                <h3>
                                    <a href="blog-details.html">Find Your Dream Car</a>
                                </h3>
                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's</p>
                                <a href="blog-details.html" class="b-btn">Rea more...!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `);


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
}

const getFilterHomePage = () => {
    $('.order-home-page').append(`
    <div class="search-box-3 sb-7">
        <div class="container">
            <div class="search-area-inner">
                <div class="search-contents filter-home-page">
                    <form method="GET" action="${window.location.origin}/automoveis">
                        <div class="row">
                        <div class="col-md-12 mb-3">
                            <h5 class="text-center">Faça sua busca e encontre seu próximo veículo</h5>
                        </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                                <div class="form-group">
                                    <select class="selectpicker search-fields" multiple data-live-search="true" name="select-brand" title="Por marca"></select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                                <div class="form-group">
                                    <select class="selectpicker search-fields" multiple data-live-search="true" name="select-make" title="Por modelo"></select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                                <div class="form-group">
                                    <select class="selectpicker search-fields" multiple data-live-search="true" name="select-year" title="Por ano"></select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                                <div class="form-group">
                                    <select class="selectpicker search-fields" multiple data-live-search="true" name="select-color" title="Por cor"></select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                                <div class="form-group">
                                    <div class="range-slider">
                                        <div data-min="0" data-max="0" data-min-name="min_price" data-max-name="max_price" class="range-slider-ui ui-slider range-price-filter" aria-disabled="false"></div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                                <div class="form-group">
                                    <button class="btn btn-block button-theme btn-md">
                                        <i class="fa fa-search"></i>Buscar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    `);

    getFiltersAuto($('.filter-home-page'));
}

const getAutosRecent = () => {

    $('.order-home-page').append(`
    <div class="recent-car content-area">
        <div class="container">
            <!-- Main title -->
            <div class="main-title">
                <h1>Automóveis Recentes</h1>
            </div>
            <div class="row list-autos"></div>
        </div>
    </div>
    `);

    $.get(`${window.location.origin}/ajax/automoveis/listagem/recente`, function (autos) {
        $('.recent-car .list-autos').empty();

        $.each(autos, function (key, value) {

            $('.recent-car .list-autos').append('<div class="col-lg-4 col-md-6"><div class="car-box-3">'+getCardAuto(value, 'Novidade')+'</div></div>');
        });
    }, 'JSON').fail(function(e) {
        console.log(e);
    });
}

const getMapLocationStore = async el => {
    $(el).append(`<div id="mapStore" style="height: 450px"></div>`);

    const dataStore = await getDataStore();

    getLocation(dataStore);
}

const getLocation = store => {

    const latLng = L.latLng(store.address_lat, store.address_lng);

    // Where you want to render the map.
    const element = document.getElementById('mapStore');
    // Create Leaflet map on map element.
    const map = L.map(element, {
        // fullscreenControl: true,
        // OR
        fullscreenControl: {
            pseudoFullscreen: false // if true, fullscreen to page width and height
        }
    });
    // Add OSM tile leayer to the Leaflet map.
    L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    const linkGoogle = $('.address-stores-link-google').attr('href');

    // const icon    = L.icon({
    //     iconUrl: 'dist/img/marcadores/cacamba.png',
    //     iconSize: [40, 40],
    // });
    // marker = L.marker(latLng, { draggable:'true', icon }).addTo(map);
    const marker = L.marker(latLng)
        .bindPopup(`<h4>${store.store_fancy}</h4><br><div style="width: 100%;display: flex; justify-content: center"><a style="font-size: 16px" href='${linkGoogle}' target="_blank">Navegar até a Loja</a></div>`)
        .addTo(map);


    map.setView(latLng, 13);
    setTimeout(() => {
        map.invalidateSize();
        marker.openPopup();
    }, 1000);
}

const getFiltersAuto = async elFilter => {

    const filterGET = window.location.search.replace('?', '');
    let filtersSearch = {};

    if (filterGET !== '') {
        let splitValueSearch;
        await $(filterGET.split('&')).each(function(key, value){
            splitValueSearch = value.split('=');

            if (!filtersSearch.hasOwnProperty(splitValueSearch[0])) filtersSearch[splitValueSearch[0]] = [];

                filtersSearch[splitValueSearch[0]].push(splitValueSearch[1]);
        });
    }

    await $.ajax({
        url: `${window.location.origin}/ajax/filtro/buscar`,
        type: 'GET',
        dataType: 'json',
        async: true
    }).done(filter => {
        // elFilter.find('[name="select-brand"]').append(`<option value="0">Selecione</option>`);
        // elFilter.find('[name="select-make"]').append(`<option value="0">Selecione</option>`);
        // elFilter.find('[name="select-year"]').append(`<option value="0">Selecione</option>`);
        // elFilter.find('[name="select-color"]').append(`<option value="0">Selecione</option>`);

        let selected;

        $.each(filter.brand, function (key, value) {
            selected = '';
            if (filtersSearch.hasOwnProperty('select-brand') && $.inArray(key, filtersSearch['select-brand']) !== -1) selected = 'selected';
            elFilter.find('[name="select-brand"]').append(`<option value="${key}" ${selected}>${value}</option>`);
        });

        $.each(filter.model, function (key, value) {
            selected = '';
            if (filtersSearch.hasOwnProperty('select-make') && $.inArray(key, filtersSearch['select-make']) !== -1) selected = 'selected';
            elFilter.find('[name="select-make"]').append(`<option value="${key}" ${selected}>${value}</option>`);
        });

        $.each(filter.year, function (key, value) {
            selected = '';
            if (filtersSearch.hasOwnProperty('select-year') && $.inArray(key, filtersSearch['select-year']) !== -1) selected = 'selected';
            elFilter.find('[name="select-year"]').append(`<option value="${key}" ${selected}>${value}</option>`);
        });

        $.each(filter.color, function (key, value) {
            selected = '';
            if (filtersSearch.hasOwnProperty('select-color') && $.inArray(key, filtersSearch['select-color']) !== -1) selected = 'selected';
            elFilter.find('[name="select-color"]').append(`<option value="${key}" ${selected}>${value}</option>`);
        });

        elFilter.find('[name="search-text"]').val(filtersSearch['search-text'] ?? '');

        elFilter.find('.range-price-filter').attr('data-max', filter.range_price.max_price);
        elFilter.find('.range-price-filter').attr('data-min', filter.range_price.min_price);

        // Select picket
        // $('.selectpicker')

        $.each(elFilter.find('select'), function () {
            $(this).selectpicker('destroy').selectpicker().parent().find('button').trigger('click').trigger('click');
        });

        elFilter.find(".range-slider-ui").each(function () {
            const minRangeValue = parseFloat($(this).attr('data-min'));
            const maxRangeValue = parseFloat($(this).attr('data-max'));
            const minName = $(this).attr('data-min-name');
            const maxName = $(this).attr('data-max-name');

            $(this).append("" +
                "<span class='min-value'></span> " +
                "<span class='max-value'></span>" +
                "<input class='current-min' type='hidden' name='"+minName+"'>" +
                "<input class='current-max' type='hidden' name='"+maxName+"'>"
            );
            $(this).slider({
                range: true,
                min: minRangeValue,
                max: maxRangeValue,
                values: [minRangeValue, maxRangeValue],
                step: 250,
                slide: function (event, ui) {
                    const currentMin = parseInt(ui.values[0], 10);
                    const currentMax = parseInt(ui.values[1], 10);
                    $(this).children(".min-value").text(currentMin.toLocaleString('pt-br',{style: 'currency', currency: 'BRL'}));
                    $(this).children(".max-value").text(currentMax.toLocaleString('pt-br',{style: 'currency', currency: 'BRL'}));
                    $(this).children(".current-min").val(currentMin);
                    $(this).children(".current-max").val(currentMax);
                }
            });

            const currentMin = parseInt($(this).slider("values", 0), 10);
            const currentMax = parseInt($(this).slider("values", 1), 10);

            $(this).children(".min-value").text(currentMin.toLocaleString('pt-br',{style: 'currency', currency: 'BRL'}));
            $(this).children(".max-value").text(currentMax.toLocaleString('pt-br',{style: 'currency', currency: 'BRL'}));
            $(this).children(".current-min").val(currentMin);
            $(this).children(".current-max").val(currentMax);
        });

        if (filtersSearch.hasOwnProperty('min_price'))
            elFilter.find(".range-slider-ui").slider('values', 0, filtersSearch['min_price'][0]).find('[name="min_price"]').val(filtersSearch['min_price'][0]);

        if (filtersSearch.hasOwnProperty('max_price'))
            elFilter.find(".range-slider-ui").slider('values', 1, filtersSearch['max_price'][0]).find('[name="max_price"]').val(filtersSearch['max_price'][0]);

    }).fail(e => {
        console.log(e);
    });

}

const getDataStore = async () => {
    let data;
    await $.get(`${window.location.origin}/ajax/loja/dados`, store => {
        data =  store;
    }, 'JSON').fail(function(e) {
        console.log(e);
    });

    return data;
}

const setColorLayout = async () => {
    const colors = await getDataStore();

    document.body.style.setProperty('--color-primary', colors.color_layout_primary ?? '#000');
    document.body.style.setProperty('--color-secondary', colors.color_layout_secondary ?? '#666');

    setTimeout(function () {
        $(".page_loader").fadeOut("fast");
    }, 100);
}

const getAutosRelated = (el, auto, countRegisters) => {

    $.get(`${window.location.origin}/ajax/automoveis/listagem/relacionados/${auto}/${countRegisters}`, function (autos) {
        let featured = '';

        el.empty();

        $.each(autos, function (key, value) {

            featured = value.destaque ? '<div class="tag-2 bg-active">Destaque</div>' : '';

            el.append(`
                <div class="slick-slide-item">
                    <div class="car-box-3">
                        <div class="car-thumbnail">
                            <a href="${window.location.origin}/automovel/${value.auto_id}" class="car-img">
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
                        <div class="detail">
                            <h1 class="title">
                                <a href="${window.location.origin}/automovel/${value.auto_id}">${value.modelo_nome}</a>
                            </h1>
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
            `);
        });

    }).fail(function(e) {
        console.log(e);
    }).always(() => {
        el.each(function () {
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

$('#sendMessageContact').submit(function (){

    const contentForm   = $(this).closest('div');
    const data          = $(this).serialize();
    const url           = $(this).attr('action');
    const type          = $(this).attr('method');
    const btn           = $('[type="submit"]', this);

    btn.html('<i class="fa fa-spin fa-spinner"></i> Enviando').prop('disabled', true);

    $.ajax({
        url,
        type,
        data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: response => {

            $('.alert-message-contact').remove();
            contentForm.find('.main-title').after(`<div class="alert notice alert-message-contact"><strong></strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>`);

            $('.alert-message-contact strong')
                .html(`${response.message}`)
                .parent()
                .removeClass('notice-danger notice-success')
                .addClass(response.success ? 'notice-success' : 'notice-danger');

            $([document.documentElement, document.body]).animate({
                scrollTop: contentForm.find('.main-title').offset().top
            }, 'slow');

            if (response.success) {
                const splitField = data.split('&');

                $(splitField).each(function(k, v) {
                    $(`[name="${v.split('=')[0]}"]`).val('');
                });
            }
        }, error: e => {
            console.log(e)
        }
    }).always(function() {
        btn.text('Enviar Mensagem').prop('disabled', false);
    });

    return false;
});
