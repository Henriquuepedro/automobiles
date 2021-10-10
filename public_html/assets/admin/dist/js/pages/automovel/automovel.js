var pond;
// Validar dados
const container = $("div.error-form");

$(document).ready(function() {
    loadInit();
});

const loadInit = async () => {
    $('[name="autos"]').attr('disabled', false);
    // Formatar campos
    $('#placa').mask('SSS-0AA0');
    $('#quilometragem').mask('#.##0', { reverse: true });
    $('#valor').mask('#.##0,00', {reverse: true});

    await CKEDITOR.replace('observation', {
        filebrowserUploadUrl: `${window.location.origin}/admin/ajax/ckeditor/upload/obsAutos?_token=${$('meta[name="csrf-token"]').attr('content')}`,
        filebrowserUploadMethod: 'form'
    });

    $("input[data-bootstrap-switch]").each(function(){
        $(this).bootstrapSwitch('state', $(this).prop('checked'));
    });

    if ($('[name="idTipoAutomovel"]').length)
        $('#autos').val($('[name="idTipoAutomovel"]').val());

    //setTimeout(() => { $('#autos').trigger('change') }, 100);
    $('.overlay').remove();
    // se já está formatado não formata novamente
    if ($('#vlrFipe').val().includes(',') === false && $('#vlrFipe').val() !== '') {
        $('#vlrFipe').val(parseFloat($('#vlrFipe').val()).toLocaleString("pt-BR", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));
    }

    await getComplementarAuto();
    await getOptionalsAuto();
    await getFinancialsStatus();

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
        valor: {
            required: true
        },
        cor: {
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
$('#autos').change(async function () {
    const autos = $(this).val();
    if(autos === "") return false;

    $('#modelos, #anos').empty();

    $('#modelos').append('<option disabled selected>Selecione a marca</option>');
    $('#anos').append('<option disabled selected>Selecione o modelo</option>');

    if (!$('#marcasLoad').length)
        $('#marcas').next(".select2-container").hide().parent().append('<label class="col-md-12" id="marcasLoad">Aguarde <i class="fa fa-spin fa-spinner"></i></label>');

    await $.get(`${window.location.origin}/admin/ajax/fipe/${autos}/marcas`, async function (marcas) {

        $('#marcas').empty().append('<option disabled selected>SELECIONE</option>');

        await $.each(marcas, function (key, value) {
            selected = $('[name="idMarcaAutomovel"]').length === 1 && $('[name="idMarcaAutomovel"]').val() ==  value.id ? 'selected' : '';

            $('#marcas').append(`<option value='${value.id}' ${selected}>${value.name}</option>`);
        });

        $('#marcas').next(".select2-container").show();
        $('#marcasLoad').remove();
        $('#btnCadastrar').attr('disabled', false);
        if($('[name="idMarcaAutomovel"]').length === 1) $('#marcas').trigger('change');
    });

    $('#btnCadastrar').prop('disabled', true);
    await getComplementarAuto();
    await getOptionalsAuto();
    await getFinancialsStatus();
    $('#btnCadastrar').prop('disabled', false);
});

// Mostrar Modelo
$('#marcas').change(async function () {
    const autos = $('#autos').val()
    const marcas = $(this).val();
    if(autos === "" || autos === null || marcas === "" || marcas === null) return false;

    $('#anos').empty().append('<option disabled selected>Selecione o modelo</option>');

    if (!$('#modelosLoad').length)
        $('#modelos').next(".select2-container").hide().parent().append('<label class="col-md-12" id="modelosLoad">Aguarde <i class="fa fa-spin fa-spinner"></i></label>');

    $.get(`${window.location.origin}/admin/ajax/fipe/${autos}/marcas/${marcas}/modelos`, async function (modelos) {
        $('#modelos').empty().append('<option disabled selected>Selecione o Modelo</option>');
        await $.each(modelos, function (key, value) {
            selected = $('[name="idModeloAutomovel"]').length === 1 && $('[name="idModeloAutomovel"]').val() ==  value.id ? 'selected' : '';

            $('#modelos').append(`<option value='${value.id}' ${selected}>${value.name}</option>`);
        });

        $('#modelos').next(".select2-container").show();
        $('#modelosLoad').remove();
        if($('[name="idModeloAutomovel"]').length === 1) $('#modelos').trigger('change');
    });

    $('input[name="marcaTxt"]').val($('#marcas option:selected').text());
});

// Mostrar Anos
$('#modelos').change(async function () {
    const autos = $('#autos').val();
    const marcas = $('#marcas').val();
    const modelos = $(this).val();

    if(autos === "" || autos === null || marcas === "" || marcas === null || modelos === "" || modelos === null) return false;

    $('#anos').next(".select2-container").hide().parent().append('<label class="col-md-12" id="anosLoad">Aguarde <i class="fa fa-spin fa-spinner"></i></label>');

    $.get(`${window.location.origin}/admin/ajax/fipe/${autos}/marcas/${marcas}/modelos/${modelos}/anos`, async function (anos) {
        await $('#anos').empty().append('<option disabled selected>SELECIONE</option>');
        $.each(anos, function (key, value) {
            selected = $('[name="idAnoAutomovel"]').length === 1 && $('[name="idAnoAutomovel"]').val() ==  value.id ? 'selected' : '';

            $('#anos').append(`<option value='${value.id}' ${selected}>${value.name}</option>`);
        });

        $('#anos').next(".select2-container").show();
        $('#anosLoad').remove();
        if($('[name="idAnoAutomovel"]').length === 1){
            $('#anos').trigger('change');
            $('.overlay').remove();
        }
    });

    $('input[name="modeloTxt"]').val($('#modelos option:selected').text());
});

// Mostrar Fipe
$('#anos').change(function () {
    const autos = $('#autos').val();
    const marcas = $('#marcas').val();
    const modelos = $('#modelos').val();
    const anos = $(this).val();
    let valueFipe;
    if(autos === "" || autos === null || marcas === "" || marcas === null || modelos === "" || modelos === null || anos === "" || anos === null) return false;

    if (!$('#fipeLoad').length)
        $('#vlrFipe').parent().hide().parent().append('<label class="col-md-12" id="fipeLoad">Aguarde <i class="fa fa-spin fa-spinner"></i></label>');

    $.get(`${window.location.origin}/admin/ajax/fipe/${autos}/marcas/${marcas}/modelos/${modelos}/anos/${anos}`, function (fipe) {
        valueFipe = parseFloat(fipe.value);
        $('#vlrFipe').val(valueFipe.toLocaleString("pt-BR", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#codeFipe').val(fipe.id);
        $('#vlrFipe').parent().show();
        $('#fipeLoad').remove();
    });

    $('input[name="anoTxt"]').val($('#anos option:selected').text());
});

// Mostrar Fipe
$('#stores').change(async function () {
    $('#content-warning-store-not-selected').css('display', parseInt($(this).val()) === 0 ? 'block' : 'none');

    $('#btnCadastrar').prop('disabled', true);
    await getComplementarAuto();
    await getOptionalsAuto();
    await getFinancialsStatus();
    $('#btnCadastrar').prop('disabled', false);
});

// Validar select2 com validate jquery
$('select').on('change', function() {  // when the value changes
    $(this).valid(); // trigger validation on this element
});

const getOptionalsAuto = async () => {
    const autos = $('#autos').val();
    const store = parseInt($('#stores').val());
    let urlGetOptionals = `${window.location.origin}/admin/ajax/opcional/buscar/${autos}/store/${store}`;

    if ($('[name="idAuto"]').length) urlGetOptionals += `/${$('[name="idAuto"]').val()}`;

    await $.get(urlGetOptionals, async function (optionals) {

        $('#optional').empty();

        let checked;

        await $.each(optionals, function (key, value) {
            checked = value.checked ? 'checked' : '';

            $('#optional').append(`
                <div class="col-md-3">
                    <div class="form-group clearfix">
                        <div class="icheck-primary d-inline">
                            <input type="checkbox" id="optional_${value.id}" name="optional_${value.id}" ${checked}>
                            <label for="optional_${value.id}">${value.nome}</label>
                        </div>
                    </div>
                </div>
            `);
        });
    }, 'JSON').fail(function(e) {
        console.log(e);
    });
}

const getComplementarAuto = async () => {
    const autos = $('#autos').val();
    const store = parseInt($('#stores').val());
    let urlGetComplementar = `${window.location.origin}/admin/ajax/complementar/buscar/${autos}/store/${store}`;

    if ($('[name="idAuto"]').length) urlGetComplementar += `/${$('[name="idAuto"]').val()}`;

    await $.get(urlGetComplementar, async function (complementar) {

        $('#complements').empty();

        let value_selected;
        let options_select = '';

        await $.each(complementar, async function (key, value) {

            switch (value.tipo_campo) {
                case 'text':
                case 'number':

                    value_selected = value.valor_salvo ?? '';

                    $('#complements').append(`
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>${value.nome}</label>
                                <input type="${value.tipo_campo}" class="form-control" id="complement_${value.id}" name="complement_${value.id}" value="${value_selected}">
                            </div>
                        </div>
                    `);
                    break;
                case 'bool':

                    value_selected = value.valor_salvo ? 'checked' : '';

                    $('#complements').append(`
                        <div class="col-md-3">
                            <div class="form-group clearfix">
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" id="complement_${value.id}" name="complement_${value.id}" ${value_selected}>
                                    <label for="complement_${value.id}">${value.nome}</label>
                                </div>
                            </div>
                        </div>
                    `);

                    break;
                case 'select':

                    options_select = '<option value="">Selecione</option>';

                    $.each(value.valores_padrao, function (key_opt, value_opt) {

                        value_selected = value.valor_salvo !== null ? (
                            !isNaN(value.valor_salvo) ? (
                                parseInt(key_opt) === parseInt(value.valor_salvo) ? 'selected' : ''
                            ) : (
                                value.valor_salvo.includes(key_opt) ? 'selected' : ''
                            )
                        ) : '';

                        options_select += `<option value="${key_opt}" ${value_selected}>${value_opt}</option>`;
                    });

                    $('#complements').append(`
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>${value.nome}</label>
                                <select class="form-control select2_complement" id="complement_${value.id}" name="complement_${value.id}">
                                    ${options_select}
                                </select>
                            </div>
                        </div>
                    `);

                    break;
            }

        });

        $('.select2_complement').select2();

    }, 'JSON').fail(function(e) {
        console.log(e);
    });
}

const getFinancialsStatus = async () => {
    const store = parseInt($('#stores').val());
    let urlGetFinancialsStatus = `${window.location.origin}/admin/ajax/estadoFinanceiro/buscar/store/${store}`;
    if ($('[name="idAuto"]').length) urlGetFinancialsStatus += `/${$('[name="idAuto"]').val()}`;

    await $.get(urlGetFinancialsStatus, async function (financialsStatus) {

        $('#financialStatus').empty();

        let value_selected;

        await $.each(financialsStatus, async function (key, value) {

            value_selected = value.valor_salvo ? 'checked' : '';

            $('#financialStatus').append(`
                <div class="col-md-3">
                    <div class="form-group clearfix">
                        <div class="icheck-primary d-inline">
                            <input type="checkbox" id="financialStatus_${value.id}" name="financialStatus_${value.id}" ${value_selected}>
                            <label for="financialStatus_${value.id}">${value.nome}</label>
                        </div>
                    </div>
                </div>
            `);

        });

    }, 'JSON').fail(function(e) {
        console.log(e);
    });
}

const loadImages = async () => {

    let imagesLoad = [];

    if ($('[name="idAuto"]').length) {
        await $.get(`${window.location.origin}/admin/ajax/automoveis/upload-buscar/${$('[name="idAuto"]').val()}`, function (images) {

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
                            poster: `${window.location.origin}/assets/admin/dist/images/autos/${path}/${name}`
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
            url: `${window.location.origin}/admin/ajax/automoveis`,
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
                    // console.log('onerror', response);

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
            // console.log(files, origin, target)

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
