let tblTransacciones;
document.addEventListener('DOMContentLoaded', function(){
    tblTransacciones = $('#tblTransacciones').DataTable({
        ajax: {
            url: base_url + 'transacciones/list',
            dataSrc: ''
        },
        columns: [
            { data: 'id' },
            { data: 'accion' },
            { data: 'descripcion' },
            { data: 'usuario' },
            { data: 'created_at' }
        ],
        responsive: true,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json",
        },
        dom,
        buttons
    });
});
