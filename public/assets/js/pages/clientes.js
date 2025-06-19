let tblClientes;
document.addEventListener('DOMContentLoaded', function(){
    tblClientes = $('#tblClientes').DataTable( {
        ajax: {
            url: base_url + 'clientes/list',
            dataSrc: ''
        },
        columns: [
            {
                data: null,
                render: function (data, type) {
                    if (type === 'display') {
                        const accion = data.estado == 1 ? 'desactivar' : 'activar';
                        const icon = data.estado == 1 ? 'fa-user-times' : 'fa-check';
                        const btnClass = data.estado == 1 ? 'btn-danger' : 'btn-success';
                        return `<a class="btn btn-primary" href="${ base_url + 'clientes/' + data.id + '/edit' }"><i class="fas fa-edit"></i></a>
                        <form action="${ base_url + 'clientes/' + data.id }" method="post" class="d-inline eliminar" data-accion="${accion}">
                            <input type="hidden" name="${csrf_token.getAttribute('content')}" value="${csrf_hash.getAttribute('content')}" />
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn ${btnClass}"><i class="fas ${icon}"></i></button>
                        </form>`;
                    }
                    return data;
                },
            },
            { data: 'id' },
            { data: 'identidad' },
            { data: 'num_identidad' },
            //nombre y apellido
            {
                data: null,
                render: function (data, type) {
                    if (type === 'display') { 
                        return `${data.nombre + ' ' + data.apellido}`;
                    }
                    return data;
                },
            },
            { data: 'telefono' },
            { data: 'correo' },
            { data: 'direccion' },
            { data: 'prestamo' },
            { data: 'prestamo_activo' },
            {
                data: null,
                render: function (data, type) {
                    if (type === 'display') {
                        if (data.estado == 1) {
                            return `<span class="badge bg-success">Activo</span>`;
                        }
                        return `<span class="badge bg-danger">Inactivo</span>`;
                    }
                    return data;
                },
            },
        ],
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json',
        },
        dom,
        buttons,
        order: [[1, 'desc']]
    } );

    tblClientes.on('draw', function () {
        let lista = document.querySelectorAll('.eliminar');
        for (let i = 0; i < lista.length; i++) {
            lista[i].addEventListener('submit', function(e){
                e.preventDefault();
                eliminarRegistro(this);
            });          
        }
    });
})

function eliminarRegistro(form){
    const accion = form.getAttribute('data-accion');
    const mensaje = accion === 'activar' ? 'Esta seguro de activar?' : 'Esta seguro de desactivar?';
    Swal.fire({
        title: 'Mensaje?',
        text: mensaje,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, Continuar!'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      })
}
