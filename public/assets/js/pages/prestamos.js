const id_cliente = document.querySelector('#id_cliente');
const importe_credito = document.querySelector('#importe_credito');
const tasa_interes = document.querySelector('#tasa_interes');
const cuotas = document.querySelector('#cuotas');
const importe_cuota = document.querySelector('#importe_cuota');
const total_pagar = document.querySelector('#total_pagar');
const interes_generado = document.querySelector('#interes_generado');
const errorCliente = document.querySelector('#errorCliente');
const cliente = document.querySelector('#cliente');
const moneda = document.querySelector('#moneda');
const symbolTargets = document.querySelectorAll('[data-symbol-target]');

const obtenerSimbolo = (codigo) => (codigo === 'USD' ? '$' : 'C$');

const actualizarSimbolos = (codigo) => {
  const simbolo = obtenerSimbolo(codigo);
  symbolTargets.forEach((elemento) => {
    elemento.textContent = simbolo;
  });
};
document.addEventListener('DOMContentLoaded', function(){
    $("#cliente").autocomplete({
        source: function( request, response ) {
          $.ajax( {
            url: base_url + 'prestamos/buscarCliente',
            dataType: "json",
            data: {
              term: request.term
            },
            success: function( data ) {
              response( data );
              if (data.length > 0) {
                errorCliente.textContent = '';
              } else {
                errorCliente.textContent = 'NO EXISTE EL CLIENTE';
              }
            }
          } );
        },
        minLength: 2,
        select: function( event, ui ) {
            id_cliente.value = ui.item.id;
        }
      } );

      cliente.addEventListener('keydown', function(e){
        if (e.key === 'Enter' && id_cliente.value === '') {
            e.preventDefault();
        }
      });

      cliente.addEventListener('keyup', function(e){
        if (e.key !== 'Enter') {
            id_cliente.value = '';
        }
      });

      if (moneda) {
        actualizarSimbolos(moneda.value);
        moneda.addEventListener('change', function(e){
          actualizarSimbolos(e.target.value);
        });
      }

      //calcular importe
      importe_credito.addEventListener('keyup', function(e){
        if (e.target.value != '') {
            const interes = Math.max(parseFloat(tasa_interes.value) || 0, 0);
            const cuotas_total = Math.max(parseInt(cuotas.value, 10) || 0, 0);
            calcularTotal(e.target.value, cuotas_total, interes);
        } else {
            limpiarCampos()
        }
      })

      // calcular cuotas
      cuotas.addEventListener('change', function(e){
        if (e.target.value != '') {
            const interes = Math.max(parseFloat(tasa_interes.value) || 0, 0);
            const importe = Math.max(parseFloat(importe_credito.value) || 0, 0);
            calcularTotal(importe, e.target.value, interes);
        } else {
            limpiarCampos()
        }
      })

      // calcular interes
      tasa_interes.addEventListener('keyup', function(e){
        if (e.target.value != '') {
            const cuotas_total = Math.max(parseInt(cuotas.value, 10) || 0, 0);
            const importe = Math.max(parseFloat(importe_credito.value) || 0, 0);
            calcularTotal(importe, cuotas_total, e.target.value);
        } else {
            limpiarCampos();
        }
      })
})

function calcularTotal(importe, cuotas, interes) {
    const principal = Math.max(parseFloat(importe) || 0, 0);
    const numeroCuotas = Math.max(parseInt(cuotas, 10) || 0, 0);
    const tasa = Math.max(parseFloat(interes) || 0, 0) / 100;

    if (principal <= 0 || numeroCuotas <= 0) {
        limpiarCampos();
        return;
    }

    let importeCuota = 0;
    if (tasa > 0) {
        importeCuota = principal * (tasa / (1 - Math.pow(1 + tasa, -numeroCuotas)));
    } else {
        importeCuota = principal / numeroCuotas;
    }

    const totalPagar = importeCuota * numeroCuotas;
    const interesTotal = totalPagar - principal;

    importe_cuota.value = importeCuota.toFixed(2);
    total_pagar.value = totalPagar.toFixed(2);
    interes_generado.value = interesTotal.toFixed(2);
}

function limpiarCampos() {
    importe_cuota.value = '0.00';
    total_pagar.value = '0.00';
    interes_generado.value = '0.00';
}