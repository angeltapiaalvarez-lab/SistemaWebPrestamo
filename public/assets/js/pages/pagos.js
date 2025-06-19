let tblPagos;
document.addEventListener('DOMContentLoaded', function(){
    tblPagos = $('#tblPagos').DataTable({
        ajax: {
            url: base_url + 'pagos/list',
            dataSrc: ''
        },
        columns: [
            {
                data: null,
                render: function(data, type){
                    if(type === 'display'){
                        return `<a class="btn btn-primary" href="${base_url + 'pagos/' + data.id + '/recibo'}" target="_blank"><i class="fas fa-print"></i></a>`;
                    }
                    return data;
                }
            },
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
