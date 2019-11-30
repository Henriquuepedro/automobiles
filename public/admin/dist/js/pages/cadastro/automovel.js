var urlApiFipe = "https://parallelum.com.br/fipe/api/v1/";

$(document).ready(function() {
    $('[name="autos"]').attr('disabled', false);
    // Formatar campos
    $('#placa').mask('SSS-0AA0');
    $('#quilometragem').mask('#.##0', { reverse: true });
    $('#valor').mask('#.##0,00', {reverse: true});

    // Validar dados
    const container = $("div.error-form");
    // validate the form when it is submitted
    $("#formCadastroAutos").validate({
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
            unicoDono: {
                required: true
            },
            aceitaTroca: {
                required: true
            },
            placa: {
                required: true
            },
            finalPlaca: {
                required: true,
                number: true
            },
            quilometragem: {
                required: true
            },
            cambio: {
                required: true
            },
            combustivel: {
                required: true
            },
            direcao: {
                required: true
            },
            potenciaMotor: {
                required: true
            },
            tipoVeiculo: {
                required: true
            },
            portas: {
                required: true,
                number: true
            },
            marcaTxt: {
                required: true,
                number: true
            },
            modeloTxt: {
                required: true,
                number: true
            },
            anoTxt: {
                required: true,
                number: true
            },
            primaryImage: {
                required: true,
                number: true
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
            form.submit();
        }
    });

    // Renderiz imageUploader
    $('.input-images').imageUploader();
});

// Mostrar Marcas
$('#autos').change(function () {
    const autos = $(this).val();
    if(autos === "") return false;

    $('#modelos, #anos').empty().append('<option disabled selected>SELECIONE</option>');
    $('#marcas').next(".select2-container").hide().parent().append('<label class="col-md-12" id="marcasLoad">Aguarde <i class="fa fa-spin fa-spinner"></i></label>');
    $.get(`${urlApiFipe}${autos}/marcas`, function (marcas) {
        $('#marcas').empty().append('<option disabled selected>SELECIONE</option>');
        $.each(marcas, function (key, value) {
            $('#marcas').append(`<option value='${value.codigo}'>${value.nome}</option>`);
        });
        $('#marcas').next(".select2-container").show();
        $('#marcasLoad').remove();
        $('#btnCadastrar').attr('disabled', false);
    });
});

// Mostrar Modelo
$('#marcas').change(function () {
    const autos = $('#autos').val()
    const marcas = $(this).val();
    if(marcas === "") return false;

    $('#anos').empty().append('<option disabled selected>SELECIONE</option>');
    $('#modelos').next(".select2-container").hide().parent().append('<label class="col-md-12" id="modelosLoad">Aguarde <i class="fa fa-spin fa-spinner"></i></label>');

    $.get(`${urlApiFipe}${autos}/marcas/${marcas}/modelos`, function (modelos) {
        $('#modelos').empty().append('<option disabled selected>Selecione o Modelo</option>');
        $.each(modelos.modelos, function (key, value) {
            $('#modelos').append(`<option value='${value.codigo}'>${value.nome}</option>`);
        });

        $('#modelos').next(".select2-container").show();
        $('#modelosLoad').remove();
    });

    $('input[name="marcaTxt"]').val($('#marcas option:selected').text());
});

// Mostrar Anos
$('#modelos').change(function () {
    const autos = $('#autos').val();
    const marcas = $('#marcas').val();
    const modelos = $(this).val();
    if(modelos === "") return false;

    $('#anos').next(".select2-container").hide().parent().append('<label class="col-md-12" id="anosLoad">Aguarde <i class="fa fa-spin fa-spinner"></i></label>');

    $.get(`${urlApiFipe}${autos}/marcas/${marcas}/modelos/${modelos}/anos`, function (anos) {
        $('#anos').empty().append('<option disabled selected>SELECIONE</option>');
        $.each(anos, function (key, value) {
            $('#anos').append(`<option value='${value.codigo}'>${value.nome.replace('32000', 'Zero Km')}</option>`);
        });

        $('#anos').next(".select2-container").show();
        $('#anosLoad').remove();
    });

    $('input[name="modeloTxt"]').val($('#modelos option:selected').text());
});

// Mostrar Fipe
$('#anos').change(function () {
    const autos = $('#autos').val();
    const marcas = $('#marcas').val();
    const modelos = $('#modelos').val();
    const anos = $(this).val();
    if(anos === "") return false;

    $('#vlrFipe').parent().hide().parent().append('<label class="col-md-12" id="fipeLoad">Aguarde <i class="fa fa-spin fa-spinner"></i></label>');

    $.get(`${urlApiFipe}${autos}/marcas/${marcas}/modelos/${modelos}/anos/${anos}`, function (fipe) {
        $('#vlrFipe').val(fipe.Valor.replace('R$', ''));

        $('#vlrFipe').parent().show();
        $('#fipeLoad').remove();
    });

    $('input[name="anoTxt"]').val($('#anos option:selected').text());
});

// Validar select2 com validate jquery
$('select').on('change', function() {  // when the value changes
    $(this).valid(); // trigger validation on this element
});

