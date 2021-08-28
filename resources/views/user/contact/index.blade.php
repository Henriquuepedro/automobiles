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
    </script>
@stop

@section('body')
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
                                    @if($settings->storeWhatsPhonePrimary)<i class="fab fa-whatsapp"></i>@else<i class="flaticon-phone"></i>@endif
                                </div>
                                <div class="detail">
                                    <h5>Telefone:</h5>
                                    <p><a href="tel:{{ $settings->storePhonePrimary }}">{{ $settings->storePhonePrimary }}</a></p>
                                </div>
                            </div>
                            <div class="ci-box">
                                <div class="icon">
                                    @if($settings->storeWhatsPhoneSecondary)<i class="fab fa-whatsapp"></i>@else<i class="flaticon-phone"></i>@endif
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
