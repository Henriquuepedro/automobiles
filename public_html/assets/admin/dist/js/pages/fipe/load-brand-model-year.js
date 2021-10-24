const loadBrands = async (autos, loadDataAuto = true) => {
    if(autos === "") return false;

    $('#modelos, #anos').empty();

    $('#modelos').append('<option disabled selected>Selecione a marca</option>');
    $('#anos').append('<option disabled selected>Selecione o modelo</option>');

    if (!$('#marcasLoad').length) {
        $('#marcas').next(".select2-container").hide().parent().append('<label class="col-md-12" id="marcasLoad">Aguarde <i class="fa fa-spin fa-spinner"></i></label>');
    }

    await $.get(`${window.location.origin}/admin/ajax/fipe/${autos}/marcas`, async function (marcas) {

        $('#marcas').empty().append('<option disabled selected>SELECIONE</option>');

        await $.each(marcas, function (key, value) {
            selected = $('[name="idMarcaAutomovel"]').length === 1 && $('[name="idMarcaAutomovel"]').val() ==  value.id ? 'selected' : '';

            $('#marcas').append(`<option value='${value.id}' ${selected}>${value.name}</option>`);
        });

        $('#marcas').next(".select2-container").show();
        $('#marcasLoad').remove();
        if (loadDataAuto) {
            $('#btnCadastrar').attr('disabled', false);
        }
        if($('[name="idMarcaAutomovel"]').length === 1) $('#marcas').trigger('change');
    });

    if (loadDataAuto) {
        $('#btnCadastrar').prop('disabled', true);
        await getComplementarAuto();
        await getOptionalsAuto();
        await getFinancialsStatus();
        $('#btnCadastrar').prop('disabled', false);
    }
}

const loadModels = async marcas => {
    const autos = $('#autos').val()
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
}

const loadYears = async modelos => {
    const autos = $('#autos').val();
    const marcas = $('#marcas').val();

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
}

const loadAuto = async anos => {
    const autos = $('#autos').val();
    const marcas = $('#marcas').val();
    const modelos = $('#modelos').val();
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
}
