{{-- Extendendo o page de AdminLTE --}}
{{-- variável de breadcrumb ---- active{ 'Item Ativo' } ---- no-active{ 'route' => 'pag.teste', 'name' => 'teste' } --}}
@extends('adminlte::page', ['breadcrumb' => ['home' => false,'active' => 'Sobre a Loja', 'no-active' => []]])
{{-- Título da página --}}
@section('title', 'Sobre a Loja')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success mt-2">{{session('success')}}</div>
            @endif
            @if(session('warning'))
                <div class="alert alert-danger mt-2">{{session('warning')}}</div>
            @endif
            <form action="{{ route('admin.ajax.about.update') }}" method="POST" id="formUpdateAbout">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Sobre a Loja</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row @if(count($stores) === 1) d-none @endif">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="autos">Loja</label>
                                    <select class="form-control select2" id="stores" name="stores" title="Por favor, selecione uma loja." required>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}">{{ $store->store_fancy }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="conteudo">Conteúdo da Página</label>
                                    <textarea name="conteudo" id="conteudo"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="short_about">Sobre Resumido</label>
                                    <textarea name="short_about" id="short_about" class="form-control"></textarea>
                                    <small>Visualizado no rodapé das páginas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-none justify-content-between btns-about">
                        <a class="btn btn-danger" href="{{ route('admin.config.about.index') }}"><i class="fa fa-times"></i> Ignorar Alterações</a>
                        <button type="submit" class="btn btn-success" id="saveAboutStore"><i class="fa fa-save"></i> Salvar Alteraçoes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
@section('js')
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/ckeditor4/ckeditor.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/ckeditor4/config.js') }}"></script>
    <script>
        $( function() {
            $('#stores').trigger('change');

            CKEDITOR.replace( 'conteudo', {
                filebrowserUploadUrl: "{{ route('admin.ajax.ckeditor.uploadImages', ['_token' => csrf_token() ]) }}",
                filebrowserUploadMethod: 'form'
            } );
        });

        const initSortable = () => {
            $( "#sortable" ).sortable({
                scroll: true,
                revert: true
            }).disableSelection();
        }

        $('#stores').on('change', async function (){
            const store = parseInt($(this).val());
            const bodyAbout = $('.about-body');
            bodyAbout.empty();

            if (store === 0) {
                bodyAbout.append('<h5 class="text-danger text-center mt-5 mb-5">Selecione uma loja para realizar a configuração!</h5>');
                return false;
            }

            await $.get(`${window.location.origin}/admin/ajax/sobre-loja/buscar/${store}`, async function (data) {
                CKEDITOR.instances['conteudo'].setData(data.long);
                $('#short_about').val(data.short);
                $('.btns-about').removeClass('d-none').addClass('d-flex');
            });
        });


        $('#formUpdateAbout').on('submit', function (){

            const conteudo  = CKEDITOR.instances.conteudo.getData();
            const shortAbout= $('#short_about').val();
            const stores    = $('#stores').val();
            const btn       = $('[type="submit"]', this);

            btn.prop('disabled', true);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: $(this).attr('method'),
                url: $(this).attr('action'),
                data: { conteudo, stores, shortAbout },
                dataType: 'json',
                enctype: 'multipart/form-data',
                success: response => {
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
            return false;
        });
    </script>
@endsection
@section('css')
@endsection
@section('css_pre')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection

