@extends('user.template.page')

{{-- set title --}}
@section('title', $dataPage->title)

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
    <div class="content-area-4">
        <div class="container">
            <div class="row">
                <div class="col-md-12 body-pagedynamic mt-3">
                     {!! $dataPage->conteudo !!}
                </div>
            </div>
        </div>
    </div>
@stop
