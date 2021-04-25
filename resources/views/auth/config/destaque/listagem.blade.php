{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Automóveis em Destaque', 'no-active' => []]])
{{-- Título da página --}}
@section('title', 'Automóveis em Destaque')

@section('content')
    @if(session('message'))
        <div class="alert {{ session('typeMessage') === 'success' ? 'alert-success' : 'alert-warning' }}">
            <p>{{ session('message') }}</p>
        </div>
    @endif
    <div class="box">
        <div class="box-body">
            <div class="card">
                <div class="card-header">
                    <div class="col-md-12">
                        <h3 class="card-title">Automóveis em Destaque</h3><br/>
                        <small>Selecione até quatro automóveis para deixar em destaque</small>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body display-flex" id="sortable">
                    @foreach($autosDestaque as $destaque)
                        <div class="col-sm-6 col-md-3 d-flex align-items-stretch ui-state-default">
                            <div class="card bg-light">
                                <div class="card-body p-0 text-right">
                                    <a href="{{ route('admin.automoveis.edit', ['codAuto' => $destaque['codauto']]) }}" class="btn btn-sm btn-primary" target="_blank">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm bg-danger">
                                        &nbsp;<i class="fas fa-times"></i>&nbsp;
                                    </a>
                                </div>
                                <div class="card-body p-0">
                                    <div class="row">
                                        <div class="col-12 text-center">
                                            <img src="{{ asset($destaque['path']) }}" alt="" class="img-fluid">
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="col-12 text-center">
                                        <h2 class="lead"><b>{{ $destaque['marca'] }} - {{ $destaque['modelo'] }}</b></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if(count($autosDestaque) < 4)
                        <div class="col-sm-6 col-md-3 d-flex align-items-stretch ui-state-default btn-add">
                            <div class="card bg-light" style="width: 100%">
                                <div class="card-body">
                                    <div class="form-group col-md-12" style="height: 259px;align-items: center;display: flex">
                                        <button class="btn btn-success col-md-12"><i class="fa fa-plus"></i><br/> Adicionar Automóvel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <!-- /.card-body -->
                <div class="card-footer text-right">
                    <button class="btn btn-primary">Salvar Alteração</button>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $("#sortable").sortable();
        $(document).on('click', '.bg-danger', function(){
            $(this).parents('.ui-state-default').slideUp('slow').remove();
        });
        $( "#sortable" ).on( "sortchange", function( event, ui ) { console.log(event, ui)} );
    </script>
@endsection
@section('css')
    <style>
        #sortable {
            list-style-type: none;
            display: block;
        }
        #sortable .ui-state-default {
            float: left;
        }
        #sortable .card-body.text-right{
            position: absolute;
            z-index: 1;
            right: 0
        }
        #sortable .card-body img{
            width: 100%;
            height: 235px;
        }
    </style>
@endsection
