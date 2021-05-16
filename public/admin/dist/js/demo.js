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
