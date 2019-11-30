$(function () {
    if($('.dataTable').length > 0) $(".dataTable").DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese-Brasil.json"
        }
    });
    if($('.select2').length > 0) $('.select2').select2();
});
