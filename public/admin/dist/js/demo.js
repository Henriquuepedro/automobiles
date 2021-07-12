var dataTable;
$(function () {
    if($('.dataTable').length > 0)
        dataTable = $(".dataTable").DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese-Brasil.json"
            }
        });
    if($('.select2').length > 0) $('.select2').select2();
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
})
