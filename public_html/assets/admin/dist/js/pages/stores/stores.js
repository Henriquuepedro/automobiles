let latlng;
let map;
let marker;
let target;
let icon;
let element;

$(function (){

    //Colorpicker
    $('.colorpicker-primary, .colorpicker-secundary')
    .colorpicker()
    .on('colorpickerChange', function(event) {
        $('.fa-square', this).css('color', event.color.toString());
    });
})

// Where you want to render the map.
element = document.getElementById('mapStore');
// Create Leaflet map on map element.
map = L.map(element, {
    // fullscreenControl: true,
    // OR
    fullscreenControl: {
        pseudoFullscreen: false // if true, fullscreen to page width and height
    }
});
// Add OSM tile leayer to the Leaflet map.
L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

const getLocation = () => {
    map.on('locationfound', onLocationFound);
    map.on('locationerror', onLocationError);
    map.locate({setView: true, maxZoom: 12});
}
// Callback success getLocation
const onLocationFound = e => {
    startMarker(e.latlng);
}
// Callback error getLocation
async function onLocationError(e){
    if(parseInt(e.code) === 1){
        const address = await deniedLocation();
        let latCenter, lngCenter;
        console.log(address);
        if(address){
            $.get(`https://dev.virtualearth.net/REST/v1/Locations?query=${address}&key=ApqqlD_Jap1C4pGj114WS4WgKo_YbBBY3yXu1FtHnJUdmCUOusnx67oS3M6UGhor`, latLng => {
                let latCenter = 0;
                let lngCenter = 0;
                if (latLng.resourceSets[0].resources.length) {
                    latLng = latLng.resourceSets[0].resources[0].geocodePoints[0].coordinates;
                    latCenter = latLng[0];
                    lngCenter = latLng[1];
                }

                const center = L.latLng(latCenter, lngCenter);
                startMarker(center);
            });
        }
    }
}

async function deniedLocation(){
    return getAddressStore($('#stores'));
}

const startMarker = latLng => {
    target  = latLng;
    // icon    = L.icon({
    //     iconUrl: 'dist/img/marcadores/cacamba.png',
    //     iconSize: [40, 40],
    // });
    // marker = L.marker(target, { draggable:'true', icon }).addTo(map);
    marker = L.marker(target, { draggable:'true' }).addTo(map);
    marker.on('dragend', () => {
        const position = marker.getLatLng();
        const element = $('#stores');
        element.find('[name="store_lat"]').val(position.lat);
        element.find('[name="store_lng"]').val(position.lng);
    });
    map.setView(target, 13);
    setTimeout(() => {
        map.invalidateSize();
    }, 1000);
}

const getAddressStore = findDiv => {
    const endereco  = findDiv.find('[name="address_public_place"]').val();
    const numero    = findDiv.find('[name="address_number"]').val();
    const cep       = findDiv.find('[name="address_zipcode"]').val().replace(/[^0-9]/g, "");
    const bairro    = findDiv.find('[name="address_neighborhoods"]').val();
    const cidade    = findDiv.find('[name="address_city"]').val();
    const estado    = findDiv.find('[name="address_state"]').val();

    return `${endereco},${numero}-${cep}-${bairro}-${cidade}-${estado}`;
}

const updateLocation = (findDiv) => {
    loadAddressMap(getAddressStore(findDiv), findDiv);
}
// Atualiza mapa com a nota localização
const locationLatLng = (lat, lng) => {
    const newLatLng = new L.LatLng(lat, lng);
    marker.setLatLng(newLatLng);
    map.setView(newLatLng, 15);
    map.invalidateSize();
}

// CONSULTA LAT E LNG PELO ENDEREÇO E DEPOIS JOGA O ENDEREÇO CORRETO NO MAPA
const loadAddressMap = (address, findDiv) => {
    let lat;
    let lng;
    $.get(`https://dev.virtualearth.net/REST/v1/Locations?query=${address}&key=ApqqlD_Jap1C4pGj114WS4WgKo_YbBBY3yXu1FtHnJUdmCUOusnx67oS3M6UGhor`, latLng => {
        if (!latLng.resourceSets[0].resources.length) return locationLatLng(0,0);

        latLng = latLng.resourceSets[0].resources[0].geocodePoints[0].coordinates;
        lat = latLng[0];
        lng = latLng[1];

        locationLatLng(lat, lng);

        findDiv.find('[name="store_lat"]').val(lat);
        findDiv.find('[name="store_lng"]').val(lng);
    });
}

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
        $('[name="document_primary"]', form).val(dataStore.store_document_primary ?? '');
        $('[name="document_secondary"]', form).val(dataStore.store_document_secondary ?? '');
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
        $('[name="store_lat"]', form).val(dataStore.address_lat ?? 0);
        $('[name="store_lng"]', form).val(dataStore.address_lng ?? 0);
        $('.img-preview-logo img', form).attr('src', dataStore.hasOwnProperty('store_logo') ? `${window.location.origin}/assets/admin/dist/images/stores/${dataStore.id}/${dataStore.store_logo ?? ''}` : '');
        $('[name="color-primary"]', form).val(dataStore.color_layout_primary).trigger('change');;
        $('[name="color-secundary"]', form).val(dataStore.color_layout_secondary).trigger('change');;

        $('#social_network_store', form).empty();

        if (dataStore.hasOwnProperty('social_networks') && dataStore.social_networks) {
            $.each(JSON.parse(dataStore.social_networks), function (key, network) {
                createLinkSocialNetwork(network.type, network.value);
            });
        }

        CKEDITOR.replace('descriptionService', {
            toolbar: [
                { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline' ] },
                { name: 'colors', items: [ 'TextColor' ] },
            ]
        });
        CKEDITOR.instances['descriptionService'].setData(dataStore.description_service);


    }, 'JSON').fail(function(e) {
        console.log(e);
    });
}

const createLinkSocialNetwork = (network, url = '') => {
    $('#social_network_store').append(`<div class="form-group col-md-12">
        <label>Link da Conta</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text pb-0 pt-0 pl-1 pr-1">
                    <label for="" class="no-margin">
                        <img src="${window.location.origin}/assets/admin/dist/images/redes-sociais/${network}.png" width="33">
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

$('#confirm-map').on('click', function (){

    if ($('#stores [name="store_lat"]').val() == 0 || $('#stores [name="store_lng"]').val() == 0)
        setTimeout(() => { updateLocation($('#stores')) }, 500);
    else
        setTimeout(() => { locationLatLng($('#stores [name="store_lat"]').val(), $('#stores [name="store_lng"]').val()) }, 500);

    $('#confirmAddress').modal();
    $(this).attr('data-map-active','true');
});

$('#updateLocationMap').click(function (){
    const element = $('#stores');
    updateLocation(element);
})

// adicionar link de rede social
$('#add_social_network_store').on('click', function(){
    const network = $(this).closest('.input-group').find('#social_networks').val();

    if ($(`input[name="social_networks_${network}"]`).length) {
        alert('Rede social já existente');
        return false;
    }

    createLinkSocialNetwork(network);
});

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

    setTimeout(async () => {
        $('#formStore #social_networks').select2('destroy').select2();
    }, 500);

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
        formData.append('descriptionService', CKEDITOR.instances.descriptionService.getData());

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
