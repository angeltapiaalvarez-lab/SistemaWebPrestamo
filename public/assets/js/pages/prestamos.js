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

const redondear = (valor, precision = 2) => {
  const factor = 10 ** precision;
  return Math.round((Number.parseFloat(valor) || 0) * factor) / factor;
};

const calcularTablaAmortizacion = (principal, tasaPeriodo, cuotasTotales, precision = 2) => {
  const principalNormalizado = Math.max(Number.parseFloat(principal) || 0, 0);
  const cuotas = Math.max(Number.parseInt(cuotasTotales, 10) || 0, 0);
  const tasa = Math.max(Number.parseFloat(tasaPeriodo) || 0, 0);

  if (principalNormalizado <= 0 || cuotas === 0) {
    return {
      pago: 0,
      total: 0,
      interes: 0,
    };
  }

  const pagoBase = tasa > 0
    ? principalNormalizado * (tasa / (1 - Math.pow(1 + tasa, -cuotas)))
    : principalNormalizado / cuotas;

  const pagoRedondeado = redondear(pagoBase, precision);

  let saldo = principalNormalizado;
  let capitalAcumulado = 0;
  let interesAcumulado = 0;
  let totalPagos = 0;

  for (let numero = 1; numero <= cuotas; numero++) {
    const interesCuota = tasa > 0 ? saldo * tasa : 0;
    let interesRedondeado = redondear(interesCuota, precision);
    let capitalRedondeado = redondear(pagoRedondeado - interesRedondeado, precision);

    if (capitalRedondeado < 0) {
      capitalRedondeado = 0;
    }

    if (capitalRedondeado > saldo) {
      capitalRedondeado = saldo;
    }

    let pagoCuota = pagoRedondeado;
    if (numero === cuotas) {
      capitalRedondeado = redondear(principalNormalizado - capitalAcumulado, precision);
      if (capitalRedondeado < 0) {
        capitalRedondeado = 0;
      }
      interesRedondeado = redondear(pagoRedondeado - capitalRedondeado, precision);
      pagoCuota = redondear(capitalRedondeado + interesRedondeado, precision);
      saldo = 0;
    } else {
      saldo = redondear(Math.max(saldo - capitalRedondeado, 0), precision);
    }

    capitalAcumulado = redondear(capitalAcumulado + capitalRedondeado, precision);
    interesAcumulado = redondear(interesAcumulado + interesRedondeado, precision);
    totalPagos = redondear(totalPagos + pagoCuota, precision);
  }

  return {
    pago: pagoRedondeado,
    total: totalPagos,
    interes: interesAcumulado,
  };
};

const recalcularImportes = () => {
  const importe = Math.max(Number.parseFloat(importe_credito.value) || 0, 0);
  const cuotasTotal = Math.max(Number.parseInt(cuotas.value, 10) || 0, 0);
  const interes = Math.max(Number.parseFloat(tasa_interes.value) || 0, 0);

  if (importe <= 0 || cuotasTotal <= 0) {
    limpiarCampos();
    return;
  }

  const tasaPeriodo = interes / 100;
  const tabla = calcularTablaAmortizacion(importe, tasaPeriodo, cuotasTotal);

  importe_cuota.value = tabla.pago.toFixed(2);
  total_pagar.value = tabla.total.toFixed(2);
  interes_generado.value = tabla.interes.toFixed(2);
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

      const recalcularSiHayDatos = () => {
        if (importe_credito.value !== '' && cuotas.value !== '') {
          recalcularImportes();
        }
      };

      //calcular importe
      importe_credito.addEventListener('input', recalcularSiHayDatos);

      // calcular cuotas
      cuotas.addEventListener('change', recalcularSiHayDatos);

      // calcular interes
      tasa_interes.addEventListener('input', recalcularSiHayDatos);

      recalcularSiHayDatos();
})

function limpiarCampos() {
    importe_cuota.value = '0.00';
    total_pagar.value = '0.00';
    interes_generado.value = '0.00';
}