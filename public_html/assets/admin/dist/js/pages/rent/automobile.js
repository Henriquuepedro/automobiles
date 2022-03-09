var pond;
// Validar dados
const container = $("div.error-form");

$(function() {
    loadInit();
});

const loadInit = async () => {
    $('[name="autos"]').attr('disabled', false);
    // Formatar campos
    $('#placa').mask('SSS-0AA0');
    $('#quilometragem').mask('#.##0', { reverse: true });

    await CKEDITOR.replace('observation', {
        filebrowserUploadUrl: `${window.location.origin}/admin/ajax/ckeditor/upload/obsRentAutos?_token=${$('meta[name="csrf-token"]').attr('content')}`,
        filebrowserUploadMethod: 'form'
    });

    $("input[data-bootstrap-switch]").each(function(){
        $(this).bootstrapSwitch('state', $(this).prop('checked'));
    });

    if ($('[name="idTipoAutomovel"]').length) {
        $('#autos').val($('[name="idTipoAutomovel"]').val());
    }

    // setTimeout(() => { $('#autos').trigger('change') }, 100);

    await getColorAuto();
    await getCharacteristics();

    await loadImages();
    setTimeout(async () => {
        await loadOrderFiles();
    }, 1500);

    $('#btnCadastrar').prop('disabled', false);
}

// validate the form when it is submitted
$("#formCadastroAutos, #formAlteraAutos").validate({
    errorContainer: container,
    errorLabelContainer: $("ol", container),
    wrapper: 'li',
    rules: {
        marcas: {
            required: true
        },
        modelos: {
            required: true
        },
        anos: {
            required: true
        },
        color: {
            required: true
        },
        placa: {
            required: true
        },
        quilometragem: {
            required: true
        },
        combustivel: {
            required: true
        },
        stores: {
            required: true,
            min: 1
        }
    },
    highlight: function( element, errorClass, validClass ) {
        if ( element.type === "radio" ) {
            this.findByName( element.name ).addClass( errorClass ).removeClass( validClass );
        } else {
            $( element ).addClass( errorClass ).removeClass( validClass );
        }

        // select2
        if( $(element).hasClass('select2-hidden-accessible') ){
            dzik = $(element).next('span.select2');
            if(dzik)
                dzik.addClass( errorClass ).removeClass( validClass );
        }

    },
    unhighlight: function( element, errorClass, validClass ) {
        if ( element.type === "radio" ) {
            this.findByName( element.name ).removeClass( errorClass ).addClass( validClass );
        } else {
            $( element ).removeClass( errorClass ).addClass( validClass );
        }

        // select2
        if( $(element).hasClass('select2-hidden-accessible') ){
            dzik = $(element).next('span.select2');
            if(dzik)
                dzik.removeClass( errorClass ).addClass( validClass );
        }
    },
    submitHandler: function(form) {
        loadOrderFiles();

        setTimeout(() => {
            form.submit();
        }, 500);
    }
});

// Mostrar Marcas
$('#autos').change(function () {
    getCharacteristics();
    loadBrands($(this).val(), true, () => { $('#marcas').select2('open') });
});

// Mostrar Modelo
$('#marcas').change(function () {
    loadModels($(this).val(), () => { $('#modelos').select2('open') });
});

// Mostrar Anos
$('#modelos').change(function () {
    loadYears($(this).val(), () => { $('#anos').select2('open') });
});

// Mostrar Fipe
$('#anos').change(function () {
    loadAuto($(this).val());
});

// Mostrar Fipe
$('#stores').change(async function () {
    $('#content-warning-store-not-selected').css('display', parseInt($(this).val()) === 0 ? 'block' : 'none');

    await $('#btnCadastrar').prop('disabled', true);
    await getColorAuto();
    await getCharacteristics();
    await $('#btnCadastrar').prop('disabled', false);
});

// Validar select2 com validate jquery
$('select').on('change', function() {  // when the value changes
    $(this).valid(); // trigger validation on this element
});

const getCharacteristics = async () => {
    const autos = $('#autos').val();
    const store = parseInt($('#stores').val());
    let urlGetCharacteristics = `${window.location.origin}/admin/ajax/aluguel/caracteristica/buscar/${autos}/loja/${store}`;

    if (!autos || store === 0) {
        return false;
    }

    if ($('[name="idAuto"]').length) {
        urlGetCharacteristics += `/${$('[name="idAuto"]').val()}`;
    }

    await $.get(urlGetCharacteristics, async function (characteristics) {

        $('#characteristics').empty();

        let checked;

        await $.each(characteristics, function (key, value) {
            checked = value.checked ? 'checked' : '';

            $('#characteristics').append(`
                <div class="col-md-3">
                    <div class="form-group clearfix">
                        <div class="icheck-primary d-inline">
                            <input type="checkbox" id="characteristic_${value.id}" name="characteristic[]" value="${value.id}" ${checked}>
                            <label for="characteristic_${value.id}">${value.name}</label>
                        </div>
                    </div>
                </div>
            `);
        });
    }, 'JSON').fail(function(e) {
        console.log(e);
    });
}

const getColorAuto = async () => {
    const store         = parseInt($('#stores').val());
    const urlGetColors  = `${window.location.origin}/admin/ajax/cores-automoveis/buscar-ativas/${store}`;
    const el            = $('#color');

    if (store === 0) {
        el.empty().append(`<option value='0'>Selecione a loja</option>`);
    }

    await $.get(urlGetColors, async function (color) {
        let selected = '';

        el.empty().append(`<option value='0'>SELECIONE</option>`);

        await $.each(color, async function (key, value) {selected = '';
            if ($('[name="idColor"]').length === 1 && $('[name="idColor"]').val() == value.id) {
                selected = 'selected';
            }

            el.append(`<option value='${value.id}' ${selected}>${value.nome}</option>`);
        });

        $('.select2_complement').select2();

    }, 'JSON').fail(function(e) {
        console.log(e);
    });
}

const loadImages = async () => {

    let imagesLoad = [];

    if ($('[name="idAuto"]').length) {
        await $.get(`${window.location.origin}/admin/ajax/aluguel/automovel/upload-buscar/${$('[name="idAuto"]').val()}`, function (images) {

            let name, path, size, type;

            $.each(images, async function (key, value) {

                name = value.file;
                path = value.folder;
                size = value.size;
                type = 'image/*';

                imagesLoad.push({
                    source: JSON.stringify({key: `${path}/${name}`}),
                    options: {
                        type: 'limbo',
                        file: {name, type, size},
                        metadata: {
                            poster: `${window.location.origin}/assets/admin/dist/images/rent/autos/${path}/${name}`
                        }
                    }
                })
            });
        });
    }

    $.fn.filepond.registerPlugin(
        FilePondPluginImagePreview,
        FilePondPluginFileMetadata,
        FilePondPluginFilePoster,
        FilePondPluginImageCrop
    );

    pond = $.fn.filepond.create($('[name="filepond"]')[0], {
        allowMultiple: true,
        allowReorder: true,
        imagePreviewMarkupShow: true,
        server: {
            url: `${window.location.origin}/admin/ajax/aluguel/automovel`,
            process: {
                url: '/upload-processar',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                withCredentials: false,
                onload: (response) => {
                    console.log('onload', response);
                    loadOrderFiles();
                    return response
                },
                onerror: (response) => {
                    setTimeout(() => {
                        $(pond.getFiles()).each(function(key, value) {
                            if (value.status === 6) pond.removeFile(key);
                        });
                        loadOrderFiles();
                    }, 750);

                    Swal.fire({
                        icon: 'warning',
                        title: 'Atenção',
                        html: '<h5>Não foi possível realizar o upload. Tente enviar outra imagem!</h5>'
                    });
                    return response
                },
                ondata: (formData) => {
                    formData.append('path', $('[name="path-file-image"]').val());
                    return formData;
                },
            },
            revert: {
                url: '/upload-reverter',
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                withCredentials: false,
                onload: (response) => {
                    console.log('onload', response);
                    loadOrderFiles();
                    return response
                },
                onerror: (response) => {
                    console.log('error', response);
                    loadOrderFiles();
                    return response
                },
                ondata: (formData) => {
                    formData.append('path', $('[name="path-file-image"]').val());
                    return formData;
                },
            },
            load: './load/',
            fetch: './fetch/',
            restore: './restore/'
        },
        files: imagesLoad,
        onreorderfiles: (files, origin, target) => {
            let newOrder = [];

            $(files).each(function(k, v){
                newOrder.push(v.filename);
            });

            $('[name="order-file-image"]').val(JSON.stringify(newOrder));

            console.log(newOrder);
        },
        imageCropAspectRatio: '4:3',
        imageResizeTargetWidth: 400,
        imageResizeTargetHeight: 300,
        acceptedFileTypes: ['image/*'],
        maxFiles: 20
    });
}

const loadOrderFiles = () => {

    setTimeout(() => {
        let newOrder = [];

        $(pond.getFiles()).each(function (key, value) {
            newOrder.push(value.filename);
        });

        console.log(newOrder);

        $('[name="order-file-image"]').val(JSON.stringify(newOrder));
    },500);
}
