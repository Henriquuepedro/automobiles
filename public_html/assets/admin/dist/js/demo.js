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
})

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
    complete = function() {},
    initComplete = function( settings, json ) { $('[data-toggle="tooltip"]').tooltip() },
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
