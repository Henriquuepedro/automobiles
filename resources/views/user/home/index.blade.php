@extends('user.template.page')

{{-- set title --}}
@section('title', 'Início')

{{-- import css --}}
@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
@stop

{{-- import css pre --}}
@section('css_pre')
@stop

{{-- import js header --}}
@section('js_head')
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
@stop

{{-- import js footer --}}
@section('js')
    <script>

        $(function () {
            getOrderHomePage();
        });

        const getOrderHomePage = () => {
            $.get(`${window.location.origin}/ajax/config/ordem-pagina-inicial`, data => {
                $.each(data, function (key, value) {
                    switch (value.id) {
                        case 3:
                            getBannerHomePage();
                            break;
                        case 5:
                            getFilterHomePage();
                            break;
                        case 1:
                            getBlogHomePage();
                            break;
                        case 2:
                            getTestimonyHomePage();
                            break;
                        case 4:
                            getAutosFeatured();
                            break;
                        case 6:
                            getAutosRecents();
                            break;
                        case 7:
                            getMapLocationStore('.order-home-page');
                            break;
                    }
                });
            });
        }
    </script>
@stop

@section('body')
    <div class="order-home-page"></div>
@stop
