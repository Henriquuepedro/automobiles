{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Banner Página Inicial', 'no-active' => []]])
{{-- Título da página --}}
@section('title', 'Banner Página Inicial')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if (session('success'))
                <div class="alert alert-success mt-2">{{session('success')}}</div>
            @endif
            @if (session('warning'))
                <div class="alert alert-danger mt-2">{{session('warning')}}</div>
            @endif
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Cadastrar Novo Banner</h3>
                </div>
                <div class="card-body">
                    <div class="row @if (count($stores) === 1) d-none @endif">
                        <div class="col-md-12 form-group">
                            <label for="autos">Loja</label>
                            <select class="form-control select2" id="stores" name="stores" required>
                                @if (count($stores) > 1)
                                    <option value="0">Selecione uma Loja</option>
                                @endif
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->store_fancy }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <form action="{{ route('admin.ajax.banner.insert') }}" method="post" enctype="multipart/form-data" id="formNewBanner">
                                <label>Selecione o banner</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="banner" name="banner" required>
                                        <label class="custom-file-label" for="exampleInputFile">Alterar</label>
                                    </div>
                                    <div class="input-group-append">
                                        <button class="input-group-text btn btn-success" id="sendBanner">Enviar</button>
                                    </div>
                                </div>
                                <small class="text-danger">O padrão das dimensões dos banners devem ser sempre as mesmas para todos os banners. Serão aceitas jpg, jpeg e png</small>
                                {!! csrf_field() !!}
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Banners Cadastrados</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="text-center">Ordem de como será listado os banners ao cliente.</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 banner-body">

                        </div>
                    </div>
                </div>
                <div class="card-footer d-none justify-content-between btns-banner">
                    <a class="btn btn-danger" href="{{ route('admin.config.banner.index') }}"><i class="fa fa-times"></i> Ignorar Alterações</a>
                    <button class="btn btn-success" id="saveOrderBanner"><i class="fa fa-save"></i> Salvar Alteraçoes</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-delete" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.ajax.banner.remove') }}" method="post" enctype="multipart/form-data" id="formRemoveBanner">
                    <div class="modal-header">
                        <h4 class="modal-title">Excluir Banner</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body text-center col-md-12">
                        <h5 class="text-danger font-weight-bold">Você tem certeza que deseja remover o banner: </h5>
                        <img class="text-danger image-banner col-md-8 mt-3">
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-primary col-md-5" data-dismiss="modal">Cancelar operação</button>
                        <button type="submit" class="btn btn-danger col-md-5">Excluir permanentemente</button>
                    </div>
                    <input type="hidden" name="banner_id">
                    {!! csrf_field() !!}
                </form>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $( function() {
            $('#stores').trigger('change');
        });

        const initSortable = () => {
            $( "#sortable" ).sortable({
                scroll: true,
                revert: true
            }).disableSelection();
        }

        $('#stores').on('change', async function () {
            const store = parseInt($(this).val());
            const bodyBanner = $('.banner-body');
            bodyBanner.empty();

            if (store === 0) {
                bodyBanner.append('<h5 class="text-danger text-center mt-5 mb-5">Selecione uma loja para realizar a configuração!</h5>');
                return false;
            }

            await $.get(`${window.location.origin}/admin/ajax/banner/buscar/${store}`, async function (data) {
                let htmlBanner = '';

                if (data.length === 0) {
                    $('.btns-banner').removeClass('d-flex').addClass('d-none');
                    bodyBanner.append('<h5 class="text-danger text-center mt-5 mb-5">Adicione pelo menos um banner, caso contrário a página inicial ficará desproporcional!</h5>');
                } else {
                    htmlBanner += '<ul id="sortable" class="banners d-flex justify-content-center flex-wrap">';

                    $(data).each(function (key, value) {
                        htmlBanner += `
                            <li class="banner col-md-6 mt-2 d-flex align-items-center" style="margin: 0 1px" banner-id="${value.id}">
                                <i class="fa fa-trash col-md-2 btnRequestDeleteBanner"></i>
                                <img class="col-md-10 img-thumbnail" src="${value.path}">
                            </li>
                        `;
                    });
                    htmlBanner += '</ul>';

                    $('.btns-banner').removeClass('d-none').addClass('d-flex');
                    bodyBanner.append(htmlBanner);
                    initSortable();
                }

            });
        });

        $(document).on('click', '.btnRequestDeleteBanner', function () {
            const banner_id  = $(this).closest('.banner').attr('banner-id');
            const banner_img = $(this).closest('.banner').find('img').attr('src');

            $('#modal-delete img.image-banner').attr('src', banner_img);
            $('#modal-delete [name="banner_id"]').val(banner_id);
            $('#modal-delete').modal();
        });

        $('#formRemoveBanner').on('submit', function () {

            const stores    = $('#stores').val();
            const banner_id = $('[name="banner_id"]', this).val();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: $(this).attr('method'),
                url: $(this).attr('action'),
                data: { stores, banner_id },
                dataType: 'json',
                success: response => {

                    Toast.fire({
                        icon: response.success ? 'success' : 'error',
                        title: response.message
                    });

                    if (response.success) {
                        $('#modal-delete').modal('hide');
                        $('#stores').trigger('change');
                    }

                }, error: (e) => {
                    console.log(e);
                }
            });
            return false;
        });

        $('#formNewBanner').on('submit', function () {

            const formData = new FormData($(this)[0]);
            formData.append('stores', $('#stores').val());

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: $(this).attr('method'),
                url: $(this).attr('action'),
                data: formData,
                dataType: 'json',
                enctype: 'multipart/form-data',
                processData: false,
                contentType: false,
                success: response => {

                    Toast.fire({
                        icon: response.success ? 'success' : 'error',
                        title: response.message
                    });

                    if (response.success) {
                        $('#stores').trigger('change');
                        $("#banner").val('');
                    }

                }, error: (e) => {
                    console.log(e);
                }
            });
            return false;
        });

        $('#saveOrderBanner').on('click', function () {
            let banner_id;
            let order_banners = [];
            const stores = $('#stores').val();

            $('.banners .banner').each(function () {
                banner_id = parseInt($(this).attr('banner-id'));
                order_banners.push(banner_id);
            });


            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: `${window.location.origin}/admin/ajax/banner/rearrangeOrderBanners`,
                data: { order_banners, stores },
                dataType: 'json',
                success: response => {
                    Toast.fire({
                        icon: response.success ? 'success' : 'error',
                        title: response.message
                    });
                }, error: (e) => {
                    console.log(e);
                }
            });
        })
    </script>
@endsection
@section('css')
    <style>
        #sortable{
            padding: 0px
        }
        #sortable li{
            list-style: none;
        }
        .banners .banner{
            cursor: move;
        }
        .card-footer::after{
            display: none;
        }
        .input-group-text.btn-success{
            color: #fff;
            background-color: #28a745;
            border-color: #28a745;
        }
        .input-group-text.btn-success:hover{
            background-color: #1e7e34;
            border-color: #1c7430;
        }
        .custom-file-input{
            cursor: pointer;
        }
        .banner i{
            color: #cc0202;
            font-size: 25px;
        }
        .banner i:hover {
            color: #8b0202;
            cursor: pointer;
        }

        @media (max-width: 992px) {
            #sortable li {
                flex-wrap: wrap;
                text-align: center;
            }
            #sortable li img{
                margin-top: 5px
            }
        }
    </style>
@endsection
@section('css_pre')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection

