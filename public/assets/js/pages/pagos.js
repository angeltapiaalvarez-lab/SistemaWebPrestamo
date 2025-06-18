let tblPagos;
document.addEventListener('DOMContentLoaded', function(){
    tblPagos = $('#tblPagos').DataTable({
        ajax: {
            url: base_url + 'pagos/list',
            dataSrc: ''
        },
        columns: [
            { data: 'id' },
            { data: 'prestamo' },
            { data: 'cuota' },
            { data: 'monto' },
            { data: 'metodo' },
            { data: 'fecha_pago' }
        ],
        responsive: true,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json",
        },
        dom,
        buttons
    });
});
