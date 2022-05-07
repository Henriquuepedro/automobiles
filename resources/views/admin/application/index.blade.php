{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Aplicativos', 'no-active' => []]])
{{-- Título da página --}}
@section('title', 'Aplicativos')

@section('content')
    @if (session('message'))
        <div class="alert {{ session('typeMessage') === 'success' ? 'alert-success' : 'alert-warning' }}">
            <p>{{ session('message') }}</p>
        </div>
    @endif
    <div class="box">
        <div class="box-body">
            <div class="row @if (count($dataStores) === 1) d-none @endif">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="stores">Loja</label>
                        <select class="form-control select2" id="stores" name="stores" required>
                            @if (count($dataStores) > 1)
                                <option value="0">Selecione uma loja</option>
                            @endif
                            @foreach ($dataStores as $store)
                                <option value="{{ $store->id }}" {{ old('stores') == $store->id ? 'selected' : ''}}>{{ $store->store_fancy }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div id="apps">


            </div>

        </div>
    </div>
@stop
@section('js')
    <script src="{{ asset('assets/admin/plugins/ion-rangeslider/js/ion.rangeSlider.min.js') }}"></script>
    <script>
        $(function () {
            setTimeout(() => { loadApplications(); }, 500);
        });

        $('[name="stores"]').on('change', function(){
            loadApplications();
        });

        const loadApplications = () => {
            const store = parseInt($('[name="stores"]').val());
            let content = '';
            let btnActionText = '';
            let btnActionColor = '';

            if (store === 0) {
                return false;
            }

            disabledActions();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                url: `${window.location.origin}/admin/ajax/aplicativos/consultar-todos/${store}`,
                dataType: 'json',
                success: response => {
                    console.log(response);

                    $(response).each(function (key, data) {
                        btnActionText = data.active ? '<i class="fas fa-trash-alt"></i> Desinstalar' : '<i class="fas fa-download"></i> Instalar';
                        btnActionColor = data.active ? 'danger' : 'success';
                        content += `<div class="col-md-4">
                            <div class="card card-widget widget-user-2">
                                <div class="widget-user-header bg-primary"><h3 class="text-center">${data.name}</h3></div>
                                <div class="widget-user-body mt-2 mb-2">${data.description}</div>
                                <div class="card-footer p-0 text-center p-3"><button class="btn btn-${btnActionColor} col-md-9" app-id="${data.id}">${btnActionText}</button></div>
                            </div>
                        </div>`;
                    });

                    $('#apps').html(content);

                }, error: e => {
                    console.log(e);
                }, complete: () => {
                    enabledActions();
                }
            });
        }

        const enabledActions = () => {
            $('[app-id]').prop('disabled', false);
        }

        const disabledActions = (app_id = null, install = null) => {
            if (app_id !== null && install !== null) {
                const nameAction = install ? 'Instalando' : 'Desinstalando';
                $(`[app-id="${app_id}"]`).html(`<i class="fa fa-spin fa-spinner"></i> ${nameAction}`);
            }
            $('[app-id]').prop('disabled', true);
        }

        $(document).on('click', '[app-id]', function() {
            const card          = $(this);
            const app_id        = parseInt(card.attr('app-id'));
            const store         = parseInt($('[name="stores"]').val());
            let btnActionText   = '';
            disabledActions(app_id, card.hasClass('btn-success'));

            if (store === 0 || app_id === 0) {
                return false;
            }

            setTimeout(() => {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: `${window.location.origin}/admin/ajax/aplicativos/instala-desinstala-app`,
                    data: { app_id, store },
                    dataType: 'json',
                    success: response => {
                        if (response.success) {
                            btnActionText = response.active ? '<i class="fas fa-trash-alt"></i> Desinstalar' : '<i class="fas fa-download"></i> Instalar';
                            card.html(btnActionText);
                            card.toggleClass('btn-success btn-danger');
                        }

                        Toast.fire({
                            icon: response.success ? 'success' : 'error',
                            title: response.message
                        });
                    }, error: (e) => {
                        console.log(e);
                    }, complete: () => {
                        enabledActions();
                    }
                });
            }, 1500);
        });
    </script>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/ion-rangeslider/css/ion.rangeSlider.min.css') }}">
@endsection
