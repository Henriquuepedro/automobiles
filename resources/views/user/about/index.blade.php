@extends('user.template.page')

{{-- set title --}}
@section('title', 'Sobre a Loja')

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
@stop

@section('body')
    <div class="sub-banner">
        <div class="container breadcrumb-area">
            <div class="breadcrumb-areas">
                <h1>Sobre a Loja</h1>
                <ul class="breadcrumbs">
                    <li><a href="{{ route('user.home') }}">Início</a></li>
                    <li class="active">Sobre a Loja</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="content-area">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                     {!! $about !!}
                </div>
            </div>
        </div>
    </div>
@stop
