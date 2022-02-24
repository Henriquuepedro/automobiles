let latlng;
let map;
let marker;
let target;
let icon;
let element;

$(function(){
    setTimeout(() => {
        getLocation();
    }, 2000);

    $('input[name="contact_primary_phone"], input[name="contact_secondary_phone"]').unmask().mask(maskPhone, phoneOptions);
    $('[name="address_zipcode"]').unmask().mask('00.000-000');
});

// Where you want to render the map.
element = document.getElementById('mapPlace');
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
async function onLocationError(e) {
    if (parseInt(e.code) === 1) {
        const address = await deniedLocation();
        if (address) {
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

async function deniedLocation() {
    return getAddressStore($('#formPlace'));
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
        const element = $('#formPlace');
        console.log(position);
        element.find('[name="address_lat"]').val(position.lat);
        element.find('[name="address_lng"]').val(position.lng);
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
    console.log(`https://dev.virtualearth.net/REST/v1/Locations?query=${address}&key=ApqqlD_Jap1C4pGj114WS4WgKo_YbBBY3yXu1FtHnJUdmCUOusnx67oS3M6UGhor`);
    $.get(`https://dev.virtualearth.net/REST/v1/Locations?query=${address}&key=ApqqlD_Jap1C4pGj114WS4WgKo_YbBBY3yXu1FtHnJUdmCUOusnx67oS3M6UGhor`, latLng => {
        if (!latLng.resourceSets[0].resources.length) return locationLatLng(0,0);

        latLng = latLng.resourceSets[0].resources[0].geocodePoints[0].coordinates;
        lat = latLng[0];
        lng = latLng[1];

        locationLatLng(lat, lng);

        findDiv.find('[name="address_lat"]').val(lat);
        findDiv.find('[name="address_lng"]').val(lng);
    });
}

$('#confirm-map').on('click', function () {

    if ($('[name="address_lat"]').val() == 0 || $('[name="address_lng"]').val() == 0) {
        setTimeout(() => {
            updateLocation($('#formPlace'))
        }, 500);
    } else {
        setTimeout(() => {
            locationLatLng($('[name="address_lat"]').val(), $('[name="address_lng"]').val())
        }, 500);
    }

    $('#confirmAddress').modal();
    $(this).attr('data-map-active','true');
});

$('#updateLocationMap').click(function () {
    const element = $('#formPlace');
    updateLocation(element);
})
