var dataTable;
$(function () {
    if($('.dataTable').length > 0) {
        dataTable = $(".dataTable").DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese-Brasil.json"
            }
        });
    }
    if($('.select2').length > 0) {
        $('.select2').select2();
    }
    if($('[data-toggle="tooltip"]').length > 0) {
        $('[data-toggle="tooltip"]').tooltip();
    }
});

var Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    target: 'body',
    showConfirmButton: false,
    timer: 5000,
    timerProgressBar: true,
    onOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

const inArray = (needle, haystack) => {
    const length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(typeof haystack[i] == 'object') {
            if(arrayCompare(haystack[i], needle)) return true;
        } else {
            if(haystack[i] == needle) return true;
        }
    }
    return false;
}

// select image logo for preview
const getImgData = () => {
    const files = $(".upload-image-logo .choose-file-logo")[0].files[0];
    const imgPreview = $(".upload-image-logo .img-preview-logo");
    if (files) {
        const fileReader = new FileReader();
        fileReader.readAsDataURL(files);
        fileReader.addEventListener("load", function () {
            imgPreview.show();
            imgPreview.html('<img src="' + this.result + '" />');
        });
    }
}

$(".upload-image-logo .choose-file-logo").on('change', function () {
    getImgData();
});

$(document).on('keyup', '.search-data-cep', function (){
    const cep = $(this).val().replace(/\D/g, '');
    let el = $(this).closest('form');

    if (cep.length !== 8) return false;

    $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/", function(dados) {

        if (!("erro" in dados)) {
            if(dados.logradouro !== '') el.find('[address-search-cep]').val(dados.logradouro);
            if(dados.bairro !== '')     el.find('[neigh-search-cep]').val(dados.bairro);
            if(dados.localidade !== '') el.find('[city-search-cep]').val(dados.localidade);
            if(dados.uf !== '')         el.find('[state-search-cep]').val(dados.uf);
        } //end if.
        else {
            Toast.fire({
                icon: 'error',
                title: 'CEP não encontrado'
            })
        }
    });
});

const maskPhone = val => {
    return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
}

const phoneOptions = {
    onKeyPress: function(val, e, field, options) {
        field.mask(maskPhone.apply({}, arguments), options);
    }
}

const getTableList = (
    url,
    dataRequest = {},
    varTable = 'dataTableList',
    stateSave = false,
    order = [0,'desc'],
    type = 'POST',
    complete = function() { $('[data-toggle="tooltip"]').tooltip() },
    initComplete = function( settings, json ) {},
    createdRow = function(row, data, index, cells) {}
) => {

    $('[data-toggle="tooltip"]').tooltip('dispose');

    if (typeof eval(varTable) !== 'undefined') {
        eval(varTable).destroy();
        $(`#${varTable} tbody`).empty();
    }

    let dataPre = {
        _token: $('meta[name="csrf-token"]').attr('content')
    };

    let data = {...dataPre, ...dataRequest};

    return $(`#${varTable}`).DataTable({
        responsive      : true,
        processing      : true,
        autoWidth       : false,
        serverSide      : true,
        sortable        : true,
        searching       : true,
        stateSave       : stateSave,
        serverMethod    : 'post',
        order           : [order],
        ajax            : {
            url: `${window.location.origin}/admin/${url}`,
            pages: 2,
            type,
            data,
            error: function(jqXHR, ajaxOptions, thrownError) {
                console.log(jqXHR, ajaxOptions, thrownError);
            },
            complete
        },
        language: {
            url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese-Brasil.json"
        },
        initComplete,
        createdRow
    });
}

const onlyNumbers = number => {
    return number.replace(/\D/g, '');
}

/**
 *  Multiplica dígitos vezes posições
 *
 * @param   {string}    digits      Os dígitos desejados.
 * @param   {number}    posiciones  A posição que iniciará a regressão.
 * @param   {number}    number      A soma das multiplicações entre posições e dígitos.
 * @return  {string}                Os dígitos enviados concatenados com o último dígito.
 */
const calcDigitPos = ( digits, posiciones = 10, number = 0 ) => {

    // Garante que o valor é uma ‘string’
    digits = digits.toString();

    // Faz a soma dos dígitos com a posição
    // Ex. para 10 posições:
    //   0    2    5    4    6    2    8    8   4
    // x10   x9   x8   x7   x6   x5   x4   x3  x2
    //   0 + 18 + 40 + 28 + 36 + 10 + 32 + 24 + 8 = 196
    for (let i = 0; i < digits.length; i++  ) {
        // Preenche a soma com o dígito vezes a posição.
        number = number + ( digits[i] * posiciones );

        // Subtrai 1 da posição
        posiciones--;

        // Parte específica para CNPJ
        // Ex.: 5-4-3-2-9-8-7-6-5-4-3-2
        if ( posiciones < 2 ) {
            // Retorno a posição para 9
            posiciones = 9;
        }
    }

    // Captura o resto da divisão entre number dividido por 11
    // Ex.: 196 % 11 = 9
    number = number % 11;

    // Verifica se number é menor que 2
    if ( number < 2 ) {
        // number agora será zero
        number = 0;
    } else {
        // Se for maior que 2, o resultado é 11 menos number
        // Ex.: 11 - 9 = 2
        // Nosso dígito procurado é 2
        number = 11 - number;
    }

    // Concatena mais um dígito aos primeiro nove dígitos.
    // Ex.: 025462884 + 2 = 0254628842
    // Retorna
    return digits + number;

}

/**
 * Valida o CPF ou CNPJ
 *
 * @param   {string}    valor   Data with error.
 * @return  {bool}              true para válido, false para inválido
 */
const checkDocument = valor => {

    let valida = '';

    // Garante que o valor é uma ‘string’
    valor = valor.toString();

    // Remove caracteres inválidos do valor
    valor = valor.replace(/[^0-9]/g, '');

    // Verifica CPF
    if ( valor.length === 11 ) {
        valida = 'CPF';
    }
    // Verifica CNPJ
    else if ( valor.length === 14 ) {
        valida = 'CNPJ';
    }

    // Garante que o valor é uma string
    valor = valor.toString();

    // Remove caracteres inválidos do valor
    valor = valor.replace(/[^0-9]/g, '');


    // Valida CPF.
    if ( valida === 'CPF' ) {
        // Retorna true para cpf válido.
        // Garante que o valor é uma ‘string’.
        valor = valor.toString();

        // Remove caracteres inválidos do valor
        valor = valor.replace(/[^0-9]/g, '');

        // Captura os 9 primeiros dígitos do CPF
        // Ex.: 02546288423 = 025462884
        const digits = valor.substr(0, 9);

        // Faz o cálculo dos 9 primeiros dígitos do CPF para obter o primeiro dígito
        let novo_cpf = calcDigitPos(digits);

        // Faz o cálculo dos 10 dígitos do CPF para obter o último dígito
        novo_cpf = calcDigitPos(novo_cpf, 11);

        // Verifica se o novo CPF gerado é idêntico ao CPF enviado
        return novo_cpf === valor;
    }

    // Valida CNPJ
    else if ( valida === 'CNPJ' ) {
        // Retorna true para CNPJ válido
        // Garante que o valor é uma string
        valor = valor.toString();

        // Remove caracteres inválidos do valor
        valor = valor.replace(/[^0-9]/g, '');


        // O valor original
        const cnpj_original = valor;

        // Captura os primeiros 12 números do CNPJ
        const primeiros_numeros_cnpj = valor.substr(0, 12);

        // Faz o primeiro cálculo
        const primeiro_calculo = calcDigitPos(primeiros_numeros_cnpj, 5);

        // O segundo cálculo é a mesma coisa do primeiro, porém, começa na posição 6
        const segundo_calculo = calcDigitPos(primeiro_calculo, 6);

        // Concatena o segundo dígito ao CNPJ
        // Verifica se o CNPJ gerado é idêntico ao enviado
        return segundo_calculo === cnpj_original;
    }
    // Não retorna nada
    else {
        return false;
    }

}

