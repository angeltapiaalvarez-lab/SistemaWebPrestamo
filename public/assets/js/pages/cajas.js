let chartInicial = null;
let chartIngreso = null;

document.addEventListener('DOMContentLoaded', function () {
  const monedaSelect = document.getElementById('monedaSelect');
  const monedaInicial = monedaSelect ? monedaSelect.value : 'NIO';
  movimientoGrafico(monedaInicial);

  if (monedaSelect) {
    monedaSelect.addEventListener('change', function (e) {
      movimientoGrafico(e.target.value);
    });
  }
});

function getChartOptions(legendFontColor) {
  const baseOptions = {
    responsive: true,
    maintainAspectRatio: false
  };

  // Chart.js v3+ uses the `plugins` key, while v2 uses `legend` directly
  const major = parseInt((Chart.version || '2').split('.')[0]);
  if (major >= 3) {
    baseOptions.plugins = {
      legend: {
        position: 'bottom',
        labels: {
          color: legendFontColor,
          boxWidth: 20,
          font: { size: 14 }
        }
      }
    };
  } else {
    baseOptions.legend = {
      position: 'bottom',
      labels: {
        fontColor: legendFontColor,
        boxWidth: 20,
        fontSize: 14
      }
    };
  }

  return baseOptions;
}

function movimientoGrafico(moneda) {
  const url = base_url + 'cajas/movimientos?moneda=' + encodeURIComponent(moneda);
  const http = new XMLHttpRequest();
  http.open('GET', url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      const darkMode = document.body.classList.contains('dark');
      const legendFontColor = darkMode ? '#ffffff' : '#343a40';

      const badgeInicial = document.getElementById('badgeMonedaInicial');
      const badgeSaldo = document.getElementById('badgeMonedaSaldo');
      if (badgeInicial) {
        badgeInicial.textContent = `${res.simbolo ?? ''} ${res.moneda_nombre ?? ''}`.trim();
      }
      if (badgeSaldo) {
        badgeSaldo.textContent = `${res.simbolo ?? ''} ${res.moneda_nombre ?? ''}`.trim();
      }

      // Monto inicial vs Egresos
      const canvasInicial = document.getElementById('inicialEgreso');
      canvasInicial.height = 400;
      const ctxInicial = canvasInicial.getContext('2d');
      if (chartInicial) {
        chartInicial.destroy();
      }
      chartInicial = new Chart(ctxInicial, {
        type: 'pie',
        data: {
          datasets: [
            {
              data: [
                res.inicial,
                res.egreso
              ],
              backgroundColor: [
                '#6c757d',
                '#faa43a'
              ],
              borderColor: '#ffffff',
              borderWidth: 2,
              label: 'Inicial vs Egresos'
            }
          ],
          labels: [
            'Monto inicial: ' + res.simbolo + ' ' + res.decimales.inicial,
            'Egresos: ' + res.simbolo + ' ' + res.decimales.egreso
          ]
        },
        options: getChartOptions(legendFontColor)
      });

      // Ingresos vs Saldo
      const canvasIngreso = document.getElementById('ingresoSaldo');
      canvasIngreso.height = 400;
      const ctxIngreso = canvasIngreso.getContext('2d');
      if (chartIngreso) {
        chartIngreso.destroy();
      }
      chartIngreso = new Chart(ctxIngreso, {
        type: 'pie',
        data: {
          datasets: [
            {
              data: [
                res.ingreso,
                res.saldo
              ],
              backgroundColor: [
                '#5da5da',
                '#60bd68'
              ],
              borderColor: '#ffffff',
              borderWidth: 2,
              label: 'Ingresos vs Saldo'
            }
          ],
          labels: [
            'Ingresos: ' + res.simbolo + ' ' + res.decimales.ingreso,
            'Saldo: ' + res.simbolo + ' ' + res.decimales.saldo
          ]
        },
        options: getChartOptions(legendFontColor)
      });
    }
  };
}
