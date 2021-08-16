@extends('user.template.page')

{{-- set title --}}
@section('title', 'Contato')

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
            getMapLocationStore('.map');
        });

        $('#sendMessageContact').submit(function (){

            const data  = $(this).serialize();
            const url   = $(this).attr('action');
            const type  = $(this).attr('method');
            const btn   = $('[type="submit"]', this);

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
                    $('.main-title').after(`<div class="alert notice alert-message-contact"><strong></strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>`);

                    $('.alert-message-contact strong')
                        .html(`${response.message}`)
                        .parent()
                        .removeClass('notice-danger notice-success')
                        .addClass(response.success ? 'notice-success' : 'notice-danger');

                    $([document.documentElement, document.body]).animate({
                        scrollTop: $('.main-title').offset().top
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
    </script>
@stop

@section('body')
    <div class="sub-banner">
        <div class="container breadcrumb-area">
            <div class="breadcrumb-areas">
                <h1>Contato</h1>
                <ul class="breadcrumbs">
                    <li><a href="{{ route('user.home') }}">Início</a></li>
                    <li class="active">Contato</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="contact-2 content-area-5">
        <div class="container">
            <!-- Main title -->
            <div class="main-title text-center">
                <h1>Contato</h1>
                <p>Entre em contato conosco para mais informações</p>
            </div>
            <form action="{{ route('ajax.contact.sendMessage') }}" method="POST" enctype="multipart/form-data" id="sendMessageContact">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="row">
                            <div class="col-md-6 text-left">
                                <div class="form-group name">
                                    <input type="text" name="name" class="form-control" placeholder="Nome" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group email">
                                    <input type="email" name="email" class="form-control" placeholder="Endereço de E-mail" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group subject">
                                    <input type="text" name="subject" class="form-control" placeholder="Assunto" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group number">
                                    <input type="text" name="phone" class="form-control" placeholder="Telefone" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group message">
                                    <textarea class="form-control" name="message" placeholder="Digite sua mensagem" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="send-btn text-center">
                                    <button type="submit" class="btn btn-5">Enviar Mensagem</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="contact-info-2">
                            <div class="ci-box">
                                <div class="icon">
                                    @if($settings->storeWhatsPhonePrimary)<i class="fa fa-whatsapp"></i>@else<i class="flaticon-phone"></i>@endif
                                </div>
                                <div class="detail">
                                    <h5>Telefone:</h5>
                                    <p><a href="tel:{{ $settings->storePhonePrimary }}">{{ $settings->storePhonePrimary }}</a></p>
                                </div>
                            </div>
                            <div class="ci-box">
                                <div class="icon">
                                    @if($settings->storeWhatsPhoneSecondary)<i class="fa fa-whatsapp"></i>@else<i class="flaticon-phone"></i>@endif
                                </div>
                                <div class="detail">
                                    <h5>Telefone:</h5>
                                    <p><a href="tel:{{ $settings->storePhoneSecondary }}">{{ $settings->storePhoneSecondary }}</a></p>
                                </div>
                            </div>
                            <div class="ci-box">
                                <div class="icon">
                                    <i class="flaticon-mail"></i>
                                </div>
                                <div class="detail">
                                    <h5>E-mail:</h5>
                                    <p><a href="mailto:{{ $settings->storeEmail }}">{{ $settings->storeEmail }}</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Contact 2 end -->

    <!-- Google map start -->
    <div class="section">
        <div class="map"></div>
    </div>
    <!-- Google map end -->
@stop
