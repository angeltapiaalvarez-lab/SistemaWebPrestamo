const csrf_token = document.querySelector('meta[name="csrf_token"]');
const csrf_hash = document.querySelector('meta[name="csrf_hash"]');
const dom =
  "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
  "<'row'<'col-sm-12'tr>>" +
  "<'row'<'col-sm-5'i><'col-sm-7'p>>";
const buttons = [
  {
    extend: "excelHtml5",
    footer: true,
    text: '<span class="btn btn-success btn-sm text-white fw-bold px-3 py-1" style="background-color: #198754; border-color: #198754;"><i class="fas fa-file-excel mx-1"></i> Excel</span>',
    className: "border-0 p-0 shadow-none bg-transparent",
  },
  {
    extend: "pdfHtml5",
    footer: true,
    text: '<span class="btn btn-danger btn-sm text-white fw-bold px-3 py-1" style="background-color: #dc3545; border-color: #dc3545;"><i class="fas fa-file-pdf mx-1"></i> PDF</span>',
    className: "border-0 p-0 shadow-none bg-transparent",
    orientation: "landscape",
    exportOptions: { columns: ":visible" }
  },
  {
    extend: "print",
    footer: true,
    text: '<span class="btn btn-dark btn-sm text-white fw-bold px-3 py-1" style="background-color: #212529; border-color: #212529;"><i class="fas fa-print mx-1"></i> Imprimir</span>',
    className: "border-0 p-0 shadow-none bg-transparent",
  },
  {
    extend: "colvis",
    text: '<span class="btn btn-info btn-sm text-dark fw-bold px-3 py-1" style="background-color: #0dcaf0; border-color: #0dcaf0;"><i class="fas fa-columns mx-1"></i> Columnas</span>',
    className: "border-0 p-0 shadow-none bg-transparent",
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

  const manualTrigger = document.querySelector('.manual-help-btn[target="_blank"]');

  if (manualTrigger) {
    manualTrigger.addEventListener('click', (event) => {
      const manualUrl = manualTrigger.getAttribute('href');

      if (manualUrl) {
        event.preventDefault();
        window.open(manualUrl, '_blank', 'noopener');
      }
    });
  }
});
