const btnCorreo = document.querySelector("#btnCorreo");
const myModal = new bootstrap.Modal(document.getElementById('modalMensaje'));
const modalPago = new bootstrap.Modal(document.getElementById('modalPago'));
const formPago = document.getElementById('formPago');
const montoPago = document.getElementById('monto');
const metodoPago = document.getElementById('metodo');
const modoPago = document.getElementById('modo');
const tipoPago = document.getElementById('tipo');
const btnPagoParcial = document.querySelectorAll('.btnPagoParcial');
const btnPagoCompleto = document.querySelectorAll('.btnPagoCompleto');
const btnAdelantoCuota = document.querySelectorAll('.btnAdelantoCuota');

document.addEventListener("DOMContentLoaded", function () {
  btnPagoParcial.forEach(btn => {
    btn.addEventListener('click', function () {
      const id = this.getAttribute('data-id');
      const pendiente = this.getAttribute('data-total');
      formPago.setAttribute('action', base_url + 'prestamos/' + id);
      formPago.setAttribute('data-pendiente', pendiente);
      montoPago.value = '';
      montoPago.setAttribute('max', pendiente);
      metodoPago.value = 'EFECTIVO';
      if (modoPago) {
        modoPago.value = 'ADMIN';
      }
      if (tipoPago) {
        tipoPago.value = 'PARCIAL';
      }
      modalPago.show();
    });
  });

  btnPagoCompleto.forEach(btn => {
    btn.addEventListener('click', function () {
      const id = this.getAttribute('data-id');
      const pendiente = this.getAttribute('data-pendiente');
      formPago.setAttribute('action', base_url + 'prestamos/' + id);
      formPago.setAttribute('data-pendiente', pendiente);
      montoPago.value = pendiente;
      metodoPago.value = 'EFECTIVO';
      if (modoPago) {
        modoPago.value = 'ADMIN';
      }
      if (tipoPago) {
        tipoPago.value = 'COMPLETO';
      }
      cambiarEstado(formPago);
    });
  });

  btnAdelantoCuota.forEach(btn => {
    btn.addEventListener('click', function () {
      const id = this.getAttribute('data-id');
      const total = this.getAttribute('data-total');
      const cuota = this.getAttribute('data-cuota');
      formPago.setAttribute('action', base_url + 'prestamos/' + id);
      formPago.setAttribute('data-pendiente', total);
      montoPago.value = cuota;
      montoPago.setAttribute('max', total);
      metodoPago.value = 'EFECTIVO';
      if (modoPago) {
        modoPago.value = 'ADMIN';
      }
      if (tipoPago) {
        tipoPago.value = 'ADELANTO';
      }
      modalPago.show();
    });
  });

  if (formPago) {
    formPago.addEventListener("submit", function (e) {
      e.preventDefault();
      const pendiente = parseFloat(this.getAttribute('data-pendiente'));
      const monto = parseFloat(montoPago.value);
      if (isNaN(monto) || monto <= 0 || monto > pendiente) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'El monto debe ser mayor a 0 y no exceder el total del préstamo',
        });
        return;
      }
      cambiarEstado(this);
    });
  }

  // modal correo (may not exist)
  if (btnCorreo) {
    btnCorreo.addEventListener('click', function(){
      myModal.show();
    });
  }

});

function cambiarEstado(form) {
  Swal.fire({
    title: "Mensaje?",
    text: "Esta seguro de que desea realizar el pago!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si!",
  }).then((result) => {
    if (result.isConfirmed) {
      form.submit();
    }
  });
}

