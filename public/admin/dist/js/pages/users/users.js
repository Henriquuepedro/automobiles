$(function (){
    loadUsers();
})

// load data users
const loadUsers = async () => {
    let urlGetStore = `${window.location.origin}/admin/ajax/usuario/buscar/todos`;
    const body = $('#tableUsers tbody');
    let btnStatus;

    body.empty();

    await $.get(urlGetStore, dataStore => {
        $.each(dataStore, function (key, value) {
            btnStatus = value.active ? `<button class="btn btn-danger btn-sm btn-flat removeUser" user-id="${value.id}" user-status="${value.active}" data-toggle="tooltip" title="Inativar Usuário"><i class="fa fa-minus"></i></button>`
                : `<button class="btn btn-success btn-sm btn-flat removeUser" user-id="${value.id}" user-status="${value.active}" data-toggle="tooltip" title="Ativar Usuário"><i class="fa fa-plus"></i></button>`;
            body.append(`
                <tr>
                    <td>${value.name}</td>
                    <td>${value.email}</td>
                    <td>
                        <button class="btn btn-primary btn-sm btn-flat editUser" user-id="${value.id}" data-toggle="tooltip" title="Atualizar Cadastro"><i class="fa fa-edit"></i></button>
                        ${btnStatus}
                    </td>
                </tr>
            `);
        });
    });

    $('[data-toggle="tooltip"]').tooltip();
}

// load users
const loadUser = async store => {

    let urlGetStore = `${window.location.origin}/admin/ajax/usuario/buscar/${store}`;

    await $.get(urlGetStore, dataUsers => {
        console.log(dataUsers);

        const form = $('#formUpdateUser');
        let arrStores = [];
        let dataUser;

        $.each(dataUsers, function (key, user) {
            arrStores.push(user.store_id);
            dataUser = user;
        });

        $('[name="name_user"]', form).val(dataUser.user_name ?? '');
        $('[name="email_user"]', form).val(dataUser.user_email ?? '');
        $('[name="store_user[]"]', form).val(arrStores).trigger('change');
        $('[name="password_user"]', form).val('');
        $('[name="password_user_confirmation"]', form).val('');
        $('[name="user_id"]', form).val(dataUser.user_id);

        $('#updateUser').modal();

    }, 'JSON').fail(function(e) {
        console.log(e);
    });
}

// view data user
$(document).on('click', '.editUser', function () {
    const user_id = $(this).attr('user-id');
    loadUser(user_id);
})

// create new user
$("#formUser").validate({
    errorContainer: $("div.error-form"),
    errorLabelContainer: $("ol", $("div.error-form")),
    wrapper: 'li',
    rules: {
        name_user: { required: true },
        email_user: { required: true },
        store_user: { required: true },
        password_user: { required: true }
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
        let getForm = $('#formUser');
        const formData = new FormData(getForm[0]);
        const formNewUser = $('#newUser');
        const overlayForm = $('.overlay.screen-user-new');

        getForm.find('button[type="submit"]').attr('disabled', true);
        overlayForm.removeClass('d-none');

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

                formNewUser.modal('hide');
                formNewUser.find('[name="name_user"]').val('');
                formNewUser.find('[name="email_user"]').val('');
                formNewUser.find('[name="store_user[]"]').val('').trigger('change');
                formNewUser.find('[name="password_user"]').val('');
                formNewUser.find('[name="password_user_confirmation"]').val('');

                loadUsers();

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
                setTimeout(() => {
                    getForm.find('button[type="submit"]').attr('disabled', false);
                    overlayForm.addClass('d-none');
                }, 500);
            }
        });
    }
});

// update user
$("#formUpdateUser").validate({
    errorContainer: $("div.error-form"),
    errorLabelContainer: $("ol", $("div.error-form")),
    wrapper: 'li',
    rules: {
        name_user: { required: true },
        email_user: { required: true },
        store_user: { required: true }
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
        let getForm = $('#formUpdateUser');
        const formData = new FormData(getForm[0]);
        const formUpdateUser = $('#updateUser');
        const overlayForm = $('.overlay.screen-user-edit');

        getForm.find('button[type="submit"]').attr('disabled', true);
        overlayForm.removeClass('d-none');

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

                formUpdateUser.modal('hide');
                formUpdateUser.find('[name="name_user"]').val('');
                formUpdateUser.find('[name="email_user"]').val('');
                formUpdateUser.find('[name="store_user[]"]').val('').trigger('change');
                formUpdateUser.find('[name="password_user"]').val('');
                formUpdateUser.find('[name="password_user_confirmation"]').val('');

                loadUsers();

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
                setTimeout(() => {
                    getForm.find('button[type="submit"]').attr('disabled', false);
                    overlayForm.addClass('d-none');
                }, 500);
            }
        });
    }
});

$(document).on('click', '.removeUser', function (){
    const user_id = $(this).attr('user-id');
    const user_name = $(this).closest('tr').find('td:eq(0)').text();
    const alert_title = parseInt($(this).attr('user-status')) === 1 ? 'inativar' : 'ativar';
    const alert_btn_color = parseInt($(this).attr('user-status')) === 1 ? '#d33' : '#28a745';

    Swal.fire({
        title: 'Atualização de Status do Usuário',
        html: "Você está prestes a "+alert_title+" o usuário <br><strong>"+user_name+"</strong><br>Deseja continuar?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: alert_btn_color,
        cancelButtonColor: '#bbb',
        confirmButtonText: `Sim, ${alert_title}`,
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: window.location.origin+"/admin/ajax/usuario/inativar",
                data: { user_id },
                dataType: 'json',
                enctype: 'multipart/form-data',
                success: response => {
                    $('[data-toggle="tooltip"]').tooltip('dispose');

                    if (!response.success) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Atenção',
                            html: `<p>${response.message}</p>`
                        });
                        return false;
                    }

                    loadUsers();

                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });

                }, error: e => {
                    if (e.status !== 403 && e.status !== 422)
                        console.log(e);
                },
                complete: function(xhr) {

                    if (xhr.status === 422) {

                        let arrErrors = [];

                        $.each(xhr.responseJSON.errors, function( index, value ) {
                            arrErrors.push(value);
                        });

                        if (!arrErrors.length && xhr.responseJSON.message !== undefined)
                            arrErrors.push('Você não tem permissão para fazer essa operação!');

                        Swal.fire({
                            icon: 'warning',
                            title: 'Atenção',
                            html: '<ol><li>'+arrErrors.join('</li><li>')+'</li></ol>'
                        });
                    }
                }
            });
        }
    })
})
