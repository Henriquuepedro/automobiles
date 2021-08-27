$("#formCompany").validate({
    errorContainer: $("div.error-form"),
    errorLabelContainer: $("ol", $("div.error-form")),
    wrapper: 'li',
    rules: {
        company_fancy: { required: true },
        company_name: { required: true },
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
        let getForm = $('#formCompany');
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
