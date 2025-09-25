let formEstado = document.querySelectorAll(".formEstado");
const btnCorreo = document.querySelector("#btnCorreo");
const myModal = new bootstrap.Modal(document.getElementById('modalMensaje'));
document.addEventListener("DOMContentLoaded", function () {
  for (let i = 0; i < formEstado.length; i++) {
    formEstado[i].addEventListener("submit", function (e) {
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
    title: "¿Mensaje?",
    text: "¿Está seguro de que desea realizar el pago?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "¡Sí!",
  }).then((result) => {
    if (result.isConfirmed) {
      form.submit();
    }
  });
}
