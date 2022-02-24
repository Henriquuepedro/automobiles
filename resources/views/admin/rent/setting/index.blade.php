{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Configuração do Aluguel', 'no-active' => []]])
{{-- Título da página --}}
@section('title', 'Configuração do Aluguel')

@section('content')
    @if (session('message'))
        <div class="alert {{ session('typeMessage') === 'success' ? 'alert-success' : 'alert-warning' }}">
            <p>{{ session('message') }}</p>
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form action="{{ route('admin.ajax.rent.setting.update') }}" enctype="multipart/form-data" id="formUpdateRentSetting" method="POST">
                    <div class="card-header">
                        <h3 class="card-title">Configuração do Aluguel</h3><br/>
                        <small>Configuração de como será apresentado o alguel ao cliente</small>
                    </div>
                    <div class="card-body">
                        <div class="row @if (count($stores) === 1) d-none @endif">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="stores">Loja</label>
                                    <select class="form-control select2" id="stores" name="stores" required>
                                        @if (count($stores) > 1)
                                            <option value="0">Selecione uma loja</option>
                                        @endif
                                        @foreach ($stores as $store)
                                            <option value="{{ $store->id }}" {{ old('stores') == $store->id ? 'selected' : ''}}>{{ $store->store_fancy }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="load-setting" class="display-none">
                            <h4 class="text-center">Carregando <i class="fa fa-spin fa-spinner"></i></h4>
                        </div>
                        <div id="view-setting" class="display-none">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="autos">Visibilidade de aluguel <i class="fa fa-info-circle" data-toggle="tooltip" data-html="true" title="<b>Grupo de Automóveis</b><br>Os automóveis serão agrupados por automóveis similares, ou seja, o cliente escolherá um grupo de automóveis e de acordo com a disponibilidade o automóvel será entregue ao cliente.<br><br><b>Automóveis Simples</b><br>Não haverá grupo nesse formato, ao contrário da outra opção, o cliente irá escolher o automóvel de sua preferência."></i></label>
                                        <label><input type="radio" name="visible_type" value="0"/> Grupo de Automóveis</label>
                                        <label><input type="radio" name="visible_type" value="1"/> Automóveis Simples</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-end flex-wrap">
                                <button type="submit" class="btn btn-success col-md-3"><i class="fa fa-save"></i> Atualizar</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@stop
@section('js_head')
@endsection
@section('js')
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/jquery-image-uploader/src/image-uploader.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/jquery-validation/dist/jquery.validate.js') }}"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>

    <script>
        $(function() {
            $('#stores').trigger('change');
        });

        $('#stores').on('change', function () {
            const store = parseInt($(this).val());

            $('#view-setting').hide();
            $('#load-setting').show();

            if (store === 0) {
                $('#load-setting').hide();
                return false;
            }

            $('#formUpdateRentSetting button[type="submit"], #stores').prop('disabled', true);

            $.get(`${window.location.origin}/admin/ajax/aluguel/configuracao/buscar/${store}`, async function (data) {
                const selector = $('#view-setting');

                selector.find(`[name="visible_type"][value="${data.visible_type}"]`).prop('checked', true);
                selector.show();
                $('#load-setting').hide();
                $('#formUpdateRentSetting button[type="submit"], #stores').prop('disabled', false)
            });
        });

        $('#formUpdateRentSetting').on('submit', function (e) {
            e.preventDefault();

            const data = $(this).serialize();
            const store = parseInt($('#stores').val());
            const btn = $('#formUpdateRentSetting button[type="submit"], #stores');

            if (store === 0) {
                Toast.fire({
                    icon: 'error',
                    title: 'Selecione uma loja'
                });
                return false;
            }

            btn.prop('disabled', true);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: $(this).attr('method'),
                url: $(this).attr('action'),
                data,
                dataType: 'json',
                enctype: 'multipart/form-data',
                success: response => {
                    console.log(response);
                    Toast.fire({
                        icon: response.success ? 'success' : 'error',
                        title: response.message
                    });

                }, error: (e) => {
                    console.log(e);
                }
            }).always(function() {
                btn.prop('disabled', false);
            });
        });
    </script>
@endsection
@section('css_pre')
    <style>
    </style>
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/jquery-image-uploader/src/image-uploader.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
@endsection
@section('css')
    @yield('css_form_store')
@endsection
