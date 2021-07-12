// adicionar link de rede social
$('#add_social_network_store').on('click', function(){
    const network = $(this).closest('.input-group').find('#social_networks').val();

    if ($(`input[name="social_networks_${network}"]`).length) {
        alert('Rede social já existente');
        return false;
    }

    createLinkSocialNetwork(network);
});

const createLinkSocialNetwork = (network, url = '') => {
    $('#social_network_store').append(`<div class="form-group col-md-12">
        <label>Link da Conta</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text pb-0 pt-0 pl-1 pr-1">
                    <label for="contact_secondary_phone_store_whatsapp" class="no-margin">
                        <img src="${window.location.origin}/admin/dist/images/redes-sociais/${network}.png" width="33">
                    </label>
                </span>
            </div>
            <input type="url" class="form-control" name="social_networks_${network}" value="${url}">
            <span class="input-group-append">
                <button type="button" class="btn btn-danger btn-flat remove-network-store"><i class="fa fa-trash"></i></button>
            </span>
        </div>
    </div>`);
}

$('#storesCompany').change(async function (){
    const store = parseInt($(this).val());

    if (!store) {
        $('#formStore').slideUp('slow');
        return false;
    }

    await loadStore(store);

    await $('#formStore').slideDown('slow');

    $('#formStore [name="type_store"]:checked').trigger('change');
    $('#formStore [name="domain"]:checked').trigger('change');
    $('#formStore input[name="contact_primary_phone_store"], #formStore input[name="contact_secondary_phone_store"]').unmask().mask(maskPhone, phoneOptions);
    $('#formStore [name="address_zipcode"]').unmask().mask('00.000-000');

});

$('.nav-item a.nav-link[href="#stores"]').on('shown.bs.tab', function (e) {
    setTimeout(async () => {
        $('#formStore #social_networks').select2('destroy').select2();
    }, 500);
})

// remover rede social
$(document).on('click', '.remove-network-store', function (){
    $(this).closest('.form-group').remove();
});
// load data store
const loadStore = async store => {

    let urlGetStore = `${window.location.origin}/admin/ajax/loja/buscar/${store}`;

    await $.get(urlGetStore, dataStore => {
        console.log(dataStore);

        const form = $('#formStore');

        $('[name="store_id_update"]', form).val(store);
        $('[name="store_name"]', form).val(dataStore.store_fancy ?? '');
        $('[name="store_fancy"]', form).val(dataStore.store_name ?? '');
        $(`[name="type_store"][value="${dataStore.type_store ?? 'pj'}"]`, form).prop('checked', true);
        $('[name="document_primary"]', form).val(dataStore.document_primary ?? '');
        $('[name="document_secondary"]', form).val(dataStore.document_secondary ?? '');
        $(`[name="domain"][value="${dataStore.type_domain ?? 0}"]`, form).prop('checked', true);
        $('[name="with_domain"]', form).val(dataStore.store_domain ?? '');
        $('[name="without_domain"]', form).val(dataStore.store_without_domain ?? '');
        $('[name="email_store"]', form).val(dataStore.mail_contact_email ?? '');
        //$('[name="password_store"]', form).val(dataStore.mail_contact_password ?? '');
        $('[name="mail_smtp"]', form).val(dataStore.mail_contact_smtp ?? '');
        $('[name="mail_port"]', form).val(dataStore.mail_contact_port ?? '');
        $('[name="mail_security"]', form).val(dataStore.mail_contact_security ?? '');
        $('[name="contact_email_store"]', form).val(dataStore.contact_email ?? '');
        $('[name="contact_primary_phone_store"]', form).val(dataStore.contact_primary_phone ?? '');
        $('[name="contact_secondary_phone_store"]', form).val(dataStore.contact_secondary_phone ?? '');
        $('[name="contact_primary_phone_store_whatsapp"]', form).prop('checked', parseInt(dataStore.contact_primary_phone_have_whatsapp ?? 0) === 1);
        $('[name="contact_secondary_phone_store_whatsapp"]', form).prop('checked', parseInt(dataStore.contact_secondary_phone_have_whatsapp ?? 0) === 1);
        $('[name="address_zipcode"]', form).val(dataStore.address_zipcode ?? '');
        $('[name="address_public_place"]', form).val(dataStore.address_public_place ?? '');
        $('[name="address_number"]', form).val(dataStore.address_number ?? '');
        $('[name="address_complement"]', form).val(dataStore.address_complement ?? '');
        $('[name="address_reference"]', form).val(dataStore.address_reference ?? '');
        $('[name="address_neighborhoods"]', form).val(dataStore.address_neighborhoods ?? '');
        $('[name="address_city"]', form).val(dataStore.address_city ?? '');
        $('[name="address_state"]', form).val(dataStore.address_state ?? '');
        $('.img-preview-logo img', form).attr('src', dataStore.hasOwnProperty('store_logo') ? `${window.location.origin}/admin/dist/images/stores/${dataStore.id}/${dataStore.store_logo ?? ''}` : '');

        $('#social_network_store', form).empty();

        if (dataStore.hasOwnProperty('social_networks') && dataStore.social_networks) {
            $.each(JSON.parse(dataStore.social_networks), function (key, network) {
                createLinkSocialNetwork(network.type, network.value);
            });
        }


    }, 'JSON').fail(function(e) {
        console.log(e);
    });
}

$('#ignoreUpdateStore').click(function (){
    $('#storesCompany').trigger('change');

    Toast.fire({
        icon: 'success',
        title: 'Alterações descartadas'
    });
});

$("#formStore").validate({
    errorContainer: $("div.error-form"),
    errorLabelContainer: $("ol", $("div.error-form")),
    wrapper: 'li',
    rules: {
        store_name: { required: true },
        store_fancy: { required: true },
        document_primary: { required: true }
    },
    highlight: function( element, errorClass, validClass ) {

    },
    unhighlight: function( element, errorClass, validClass ) {

    },
    invalidHandler: function(event, validator) {
        let arrErrors = [];
        $.each(validator.errorMap, function (key, val) {
            arrErrors.push(val);
        });
        setTimeout(() => {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção',
                html: '<ol><li>'+arrErrors.join('</li><li>')+'</li></ol>'
            });
        }, 150);
    },
    submitHandler: function(form) {
        let getForm = $('#formStore');
        const formData = new FormData(getForm[0]);

        getForm.find('button[type="submit"]').attr('disabled', true);
        $('.overlay.screen-company-store-user').removeClass('d-none');

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: getForm.attr('method'),
            url: getForm.attr('action'),
            data: formData,
            dataType: 'json',
            enctype: 'multipart/form-data',
            processData:false,
            contentType:false,
            success: response => {

                if (!response.success) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atenção',
                        html: `<p>${response.message}</p>`
                    });
                    return false;
                }

                Toast.fire({
                    icon: 'success',
                    title: response.message
                });
            }, error: e => {
                console.log(e);

                let arrErrors = []

                $.each(e.responseJSON.errors, function( index, value ) {
                    arrErrors.push(value);
                });
                console.log(arrErrors);

                if (!arrErrors.length && e.responseJSON.message !== undefined)
                    arrErrors.push('Não foi possível identificar o motivo do erro, recarregue a página e tente novamente!');

                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    html: '<ol><li>'+arrErrors.join('</li><li>')+'</li></ol>'
                });
            }, complete: () => {
                getForm.find('button[type="submit"]').attr('disabled', false);
                $('.overlay.screen-company-store-user').addClass('d-none');
            }
        });
    }
});

$('#formStore [name="type_store"]').on('change', function(){
    const type = $(this).val();
    const docPrimary = $('#formStore [name="document_primary"]').closest('.form-group');
    const docSecondary = $('#formStore [name="document_secondary"]').closest('.form-group');

    switch (type) {
        case 'pf':
            docPrimary.find('label').text('CPF');
            docSecondary.find('label').text('RG');

            docPrimary.find('input').unmask().mask('000.000.000-00');
            break;
        case 'pj':
            docPrimary.find('label').text('CNPJ');
            docSecondary.find('label').text('IE');

            docPrimary.find('input').unmask().mask('00.000.000/0000-00');
            break;
    }
});

$('#formStore [name="domain"]').on('change', function(){
    const type = parseInt($(this).val());
    const withoutDomain = $('#formStore [name="without_domain"]');
    const withDomain = $('#formStore [name="with_domain"]');

    switch (type) {
        case 0:
            withoutDomain.prop('disabled', false);
            withDomain.val('').prop('disabled', true);
            break;
        case 1:
            withoutDomain.val('').prop('disabled', true);
            withDomain.prop('disabled', false);
            break;
    }
});
