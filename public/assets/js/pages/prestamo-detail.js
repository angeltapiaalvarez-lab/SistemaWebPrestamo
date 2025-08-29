const btnCorreo = document.querySelector("#btnCorreo");
const myModal = new bootstrap.Modal(document.getElementById('modalMensaje'));
const modalPago = new bootstrap.Modal(document.getElementById('modalPago'));
const formPago = document.getElementById('formPago');
const montoPago = document.getElementById('monto');
const btnPago = document.querySelectorAll('.btnPago');

document.addEventListener("DOMContentLoaded", function () {
  btnPago.forEach(btn => {
    btn.addEventListener('click', function () {
      const id = this.getAttribute('data-id');
      const pendiente = this.getAttribute('data-pendiente');
      formPago.setAttribute('action', base_url + 'prestamos/' + id);
      montoPago.value = pendiente;
      modalPago.show();
    });
  });

  if (formPago) {
    formPago.addEventListener("submit", function (e) {
      e.preventDefault();
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

