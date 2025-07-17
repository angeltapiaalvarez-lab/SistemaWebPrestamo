const csrf_token = document.querySelector('meta[name="csrf_token"]');
const csrf_hash = document.querySelector('meta[name="csrf_hash"]');
const dom =
  "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
  "<'row'<'col-sm-12'tr>>" +
  "<'row'<'col-sm-5'i><'col-sm-7'p>>";
const buttons = [
  {
    //Botón para Excel
    extend: "excelHtml5",
    footer: true,
    //Aquí es donde generas el botón personalizado
    text: '<span class="badge bg-success"><i class="fas fa-file-excel"></i></span>',
  },
  //Botón para print
  {
    extend: "print",
    footer: true,
    text: '<span class="badge bg-dark"><i class="fas fa-print"></i></span>',
  },
  //Botón para cvs
  {
    extend: "csvHtml5",
    footer: true,
    text: '<span class="badge bg-success"><i class="fas fa-file-csv"></i></span>',
  },
  {
    extend: "colvis",
    text: '<span class="badge bg-info"><i class="fas fa-columns"></i></span>',
    postfixButtons: ["colvisRestore"],
  },
];
// Aplicar mascaras de entrada para telefonos e identidad
document.addEventListener('DOMContentLoaded', () => {
  const phoneInputs = document.querySelectorAll('.phone-number');
  phoneInputs.forEach((input) => {
    input.setAttribute('maxlength', '12');
    input.addEventListener('input', function () {
      let val = this.value.replace(/[^0-9+]/g, '');
      if (!val.startsWith('+505')) {
        val = '+505' + val.replace(/^\+?505?/, '');
      }
      this.value = val.slice(0, 12);
    });
  });

  const idInputs = document.querySelectorAll('.identidad-format');
  const tipoSelect = document.getElementById('tipoIdentidad');

  function ajustarMaxLength() {
    if (tipoSelect && tipoSelect.value === 'Cedula') {
      idInputs.forEach((input) => {
        input.setAttribute('maxlength', '16');
        input.setAttribute('placeholder', '000-000000-0000A');
      });
    } else {
      idInputs.forEach((input) => {
        input.setAttribute('maxlength', '9');
        input.setAttribute('placeholder', 'XXXXXXXXX');
      });
    }
  }

  function aplicarMascara(e) {
    let val = e.target.value.replace(/[^0-9a-zA-Z]/g, '').toUpperCase();
    if (tipoSelect && tipoSelect.value === 'Cedula') {
      if (val.length > 3) val = val.slice(0, 3) + '-' + val.slice(3);
      if (val.length > 10) val = val.slice(0, 10) + '-' + val.slice(10);
      e.target.value = val.slice(0, 16);
    } else {
      e.target.value = val.slice(0, 9);
    }
  }

  idInputs.forEach((input) => {
    input.addEventListener('input', aplicarMascara);
  });

  if (tipoSelect) {
    ajustarMaxLength();
    tipoSelect.addEventListener('change', () => {
      idInputs.forEach((input) => (input.value = ''));
      ajustarMaxLength();
    });
  }

  const rucInputs = document.querySelectorAll('.ruc-format');

  function aplicarMascaraRuc(e) {
    let val = e.target.value.replace(/[^0-9a-zA-Z]/g, '').toUpperCase();
    let digits = val.slice(0, 13);
    let letter = val.slice(13, 14);
    let check = val.slice(14, 15);
    let formatted = digits;
    if (letter) {
      formatted += letter + '-';
    }
    if (check) {
      formatted += check;
    }
    e.target.value = formatted.slice(0, 16);
  }

  rucInputs.forEach((input) => {
    input.setAttribute('maxlength', '16');
    input.addEventListener('input', aplicarMascaraRuc);
  });
});