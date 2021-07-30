$(function () {

    'use strict';

    // Showing page loader
    $(window).on('load', function () {
        populateColorPlates();
        setTimeout(function () {
            $(".page_loader").fadeOut("fast");
        }, 100);

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

    $(".range-slider-ui").each(function () {
        var minRangeValue = $(this).attr('data-min');
        var maxRangeValue = $(this).attr('data-max');
        var minName = $(this).attr('data-min-name');
        var maxName = $(this).attr('data-max-name');
        var unit = $(this).attr('data-unit');

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
            slide: function (event, ui) {
                event = event;
                var currentMin = parseInt(ui.values[0], 10);
                var currentMax = parseInt(ui.values[1], 10);
                $(this).children(".min-value").text( currentMin + " " + unit);
                $(this).children(".max-value").text(currentMax + " " + unit);
                $(this).children(".current-min").val(currentMin);
                $(this).children(".current-max").val(currentMax);
            }
        });

        var currentMin = parseInt($(this).slider("values", 0), 10);
        var currentMax = parseInt($(this).slider("values", 1), 10);
        $(this).children(".min-value").text( currentMin + " " + unit);
        $(this).children(".max-value").text(currentMax + " " + unit);
        $(this).children(".current-min").val(currentMin);
        $(this).children(".current-max").val(currentMax);
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
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod.</p>
            </div>
            <div class="row list-autos"></div>
        </div>
    </div>
    `);

    $.get(`${window.location.origin}/ajax/automoveis/listagem/destaque`, function (autos) {
        $('.featured-car .list-autos').empty();

        $.each(autos, function (key, value) {

            $('.featured-car .list-autos').append(`
            <div class="col-lg-4 col-md-6">
                <div class="car-box-3">
                    <div class="car-thumbnail">
                        <a href="car-details.html" class="car-img">
                            <div class="tag-2 bg-active">Destaque</div>
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
                                        <a href="${window.location.origin}/${value.file}" class="overlap-btn" data-sub-html="<h4>Ferrari Red Car</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy....</p>">
                                            <i class="fa fa-expand"></i>
                                            <img class="hidden" src="${window.location.origin}/${value.file}" alt="hidden-img">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="detail">
                        <h1 class="title">
                            <a href="car-details.html">${value.modelo_nome}</a>
                        </h1>
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
            `);
        });
    }, 'JSON').fail(function(e) {
        console.log(e);
    });
}

const getBannerHomePage = () => {

    $('.order-home-page').append(`
    <div class="banner" id="banner">
        <div id="bannerCarousole" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner banner-slider-inner text-center banner-home-page"></div>
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
    `);

    const bodyBanner = $('.banner-home-page');
    let active = '';

    $.get(`${window.location.origin}/ajax/banner/inicio`, function (autos) {
        $.each(autos, function (key, value) {
            active = key === 0 ? 'active' : '';
            bodyBanner.append(`
                <div class="carousel-item banner-max-height ${active} item-bg">
                    <img class="d-block w-100 h-100" src="${value}" alt="banner">
                </div>
            `);
        });
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
                        <h1>Depoimentos</h1>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod.</p>
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

        console.log(testimonies);


        $.each(testimonies, function (key, testimony) {
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
    `);

    // Select picket
    $('.selectpicker').selectpicker();
}

const getAutosRecents = () => {

    $('.order-home-page').append(`
    <div class="recent-car content-area">
        <div class="container">
            <!-- Main title -->
            <div class="main-title">
                <h1>Automóveis Recentes</h1>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod.</p>
            </div>
            <div class="row list-autos"></div>
        </div>
    </div>
    `);

    $.get(`${window.location.origin}/ajax/automoveis/listagem/recente`, function (autos) {
        $('.recent-car .list-autos').empty();

        $.each(autos, function (key, value) {

            $('.recent-car .list-autos').append(`
            <div class="col-lg-4 col-md-6">
                <div class="car-box-3">
                    <div class="car-thumbnail">
                        <a href="car-details.html" class="car-img">
                            <div class="tag-2 bg-active">Novidade</div>
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
                                        <a href="${window.location.origin}/${value.file}" class="overlap-btn" data-sub-html="<h4>Ferrari Red Car</h4><p>A beautiful Sunrise this morning taken En-route to Keswick not one as planned but I'm extremely happy....</p>">
                                            <i class="fa fa-expand"></i>
                                            <img class="hidden" src="${window.location.origin}/${value.file}" alt="hidden-img">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="detail">
                        <h1 class="title">
                            <a href="car-details.html">${value.modelo_nome}</a>
                        </h1>
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
            `);
        });
    }, 'JSON').fail(function(e) {
        console.log(e);
    });
}

const getMapLocationStore = () => {
    $('.order-home-page').append(`<div id="mapStore" class="mb-3" style="height: 450px"></div>`);

    $.get(`${window.location.origin}/ajax/loja/dados`, function (store) {

        getLocation(store);

    }, 'JSON').fail(function(e) {
        console.log(e);
    });
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
