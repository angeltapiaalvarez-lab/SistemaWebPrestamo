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
                        let btnEstado = '';
                        if (data.estado == 1) {
                            btnEstado = `<form action="${ base_url + 'clientes/' + data.id + '/estado' }" method="post" class="d-inline cambiarEstado">
                                <input type="hidden" name="${csrf_token.getAttribute('content')}" value="${csrf_hash.getAttribute('content')}" />
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="estado" value="0">
                                <button type="submit" class="btn btn-warning"><i class="fas fa-ban"></i></button>
                            </form>`;
                        } else {
                            btnEstado = `<form action="${ base_url + 'clientes/' + data.id + '/estado' }" method="post" class="d-inline cambiarEstado">
                                <input type="hidden" name="${csrf_token.getAttribute('content')}" value="${csrf_hash.getAttribute('content')}" />
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="estado" value="1">
                                <button type="submit" class="btn btn-success"><i class="fas fa-check"></i></button>
                            </form>`;
                        }
                        return `<a class="btn btn-primary" href="${ base_url + 'clientes/' + data.id + '/edit' }"><i class="fas fa-edit"></i></a> ${btnEstado}`;
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
            {
                data: null,
                render: function (data, type) {
                    if (type === 'display') {
                        if (data.prestamo_activo == 1) {
                            return `<span class="badge bg-success">Préstamo activo</span>`;
                        }
                        return `<span class="badge bg-secondary">Sin préstamo</span>`;
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
        let lista = document.querySelectorAll('.cambiarEstado');
        for (let i = 0; i < lista.length; i++) {
            lista[i].addEventListener('submit', function(e){
                e.preventDefault();
                cambiarEstado(this);
            });
        }
    });
})

function cambiarEstado(form){
    Swal.fire({
        title: 'Mensaje?',
        text: "Esta seguro de cambiar el estado!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, Cambiar!'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      })
}