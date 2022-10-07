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
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Aplicativos</h3><br/>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-warning alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h5><i class="icon fas fa-exclamation-triangle"></i> Atenção!</h5>
                                Após a instalação/desinstalação de qualquer aplicativo, atualize a página para visualizar a alteração.
                            </div>
                        </div>
                    </div>
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
                    <div id="apps" class="row"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-confirm" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Confirmar <span class="type_name"></span></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center col-md-12">
                    <h5 class="text-primary font-weight-bold">Você tem certeza que deseja realizar a <span class="type_name"></span> do aplicativo? </h5>
                    <p>Ao realizar instalação, você pode solicitar a desinstalação a qualquer momento!</p>
                    <p>Ao realizar desinstalação, será possível realizar uma nova instalação após 15 dias!</p>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group text-left">
                                <label for="password">Senha</label>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-primary col-md-5" data-dismiss="modal">Cancelar operação</button>
                    <button type="submit" app-id="" class="btn col-md-5">Confirmar <span class="type_name"></span></button>
                </div>
                <input type="hidden" name="banner_id">

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
                    $(response).each(function (key, data) {
                        btnActionText = data.active ? '<i class="fas fa-trash-alt"></i> Desinstalar App' : '<i class="fas fa-download"></i> Instalar App';
                        btnActionColor = data.active ? 'danger' : 'success';
                        content += `<div class="col-md-4">
                            <div class="card card-widget widget-user-2">
                                <div class="widget-user-header bg-primary"><h3 class="text-center">${data.name}</h3></div>
                                <div class="widget-user-body mt-2 mb-2">${data.description}</div>
                                <div class="card-footer p-0 text-center p-3"><button class="btn btn-${btnActionColor} col-md-9" app-id-confirm="${data.id}">${btnActionText}</button></div>
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
            $('[app-id], [app-id-confirm]').prop('disabled', false);
        }

        const disabledActions = (app_id = null, install = null) => {
            if (app_id !== null && install !== null) {
                const nameAction = install ? 'Instalando' : 'Desinstalando';
                $(`[app-id="${app_id}"], [app-id-confirm="${app_id}"]`).html(`<i class="fa fa-spin fa-spinner"></i> ${nameAction}`);
            }
            $('[app-id], [app-id-confirm]').prop('disabled', true);
        }

        $(document).on('click', '[app-id-confirm]', function() {
            const app_id = $(this).attr('app-id-confirm');
            const uninstall = $(`[app-id-confirm="${app_id}"]`).hasClass('btn-danger');

            $('#modal-confirm').modal()
                .find('.type_name')
                .text(uninstall ? 'Desinstalação' : 'Instalação')
                .closest('.modal-content')
                .find('button[app-id]')
                .attr('app-id', app_id)
                .removeClass(uninstall ? 'btn-success' : 'btn-danger')
                .addClass(uninstall ? 'btn-danger' : 'btn-success')
                .parents('.modal-content')
                .find('[name="password"]').val('');
        });

        $(document).on('click', '[app-id]', function() {
            const app_id        = parseInt($(this).attr('app-id'));
            const card          = $(`[app-id="${app_id}"], [app-id-confirm="${app_id}"]`);
            const store         = parseInt($('[name="stores"]').val());
            const password      = card.parents('.modal-content').find('[name="password"]').val();
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
                    data: { app_id, store, password },
                    dataType: 'json',
                    success: response => {
                        btnActionText = response.active ? '<i class="fas fa-trash-alt"></i> Desinstalar App' : '<i class="fas fa-download"></i> Instalar App';
                        card.html(btnActionText);

                        if (response.success) {
                            card.toggleClass('btn-success btn-danger');
                            $('#modal-confirm').modal('hide');
                            card.parents('.modal-content').find('[name="password"]').val('');
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
