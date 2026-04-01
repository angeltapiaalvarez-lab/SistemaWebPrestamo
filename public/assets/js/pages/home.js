const year = document.querySelector('#year');
let myChart;
document.addEventListener("DOMContentLoaded", function () {
  movimientoGrafico(year.value);

  year.addEventListener('change', function(e){
    movimientoGrafico(e.target.value);
  })
});

function movimientoGrafico(anio) {
  if (myChart) {
    myChart.destroy();
  }
  const url = base_url + "prestamosMes/" + anio;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      
      // Update KPIs
      const formateador = new Intl.NumberFormat('es-NI', { style: 'currency', currency: 'NIO' });
      document.getElementById('kpi-prestamos').textContent = formateador.format(res.totales_year);
      document.getElementById('kpi-ingresos').textContent = formateador.format(res.ingresos_year);

      var options = {
        chart: {
          height: 380,
          type: "line",
          shadow: {
            enabled: true,
            color: "#000",
            top: 18,
            left: 7,
            blur: 10,
            opacity: 1,
          },
          toolbar: {
            show: true,
            tools: { download: true, selection: false, zoom: false, pan: false }
          },
        },
        colors: ["#0dcaf0", "#198754"],
        dataLabels: {
          enabled: false,
        },
        stroke: {
          curve: "smooth",
          width: [0, 3]
        },
        fill: {
          type: ['solid', 'gradient'],
          gradient: {
            shade: 'light',
            type: "vertical",
            shadeIntensity: 0.5,
            opacityFrom: 0.7,
            opacityTo: 0.2,
          }
        },
        series: [
          {
            name: "Préstamos Colocados",
            type: "column",
            data: [res.total.ene, res.total.feb, res.total.mar, res.total.abr, res.total.may,
              res.total.jun, res.total.jul, res.total.ago, res.total.sep, res.total.oct, res.total.nov, res.total.dic],
          },
          {
            name: "Abonos Pagados",
            type: "area",
            data: [res.ganancia.ene, res.ganancia.feb, res.ganancia.mar, res.ganancia.abr, res.ganancia.may,
              res.ganancia.jun, res.ganancia.jul, res.ganancia.ago, res.ganancia.sep, res.ganancia.oct, res.ganancia.nov, res.ganancia.dic],
          }
        ],
        title: {
          text: "Comparativa de Flujo Mensual",
          align: "left",
          style: {
            fontSize:  '16px',
            fontWeight:  'bold',
            color:  '#263238'
          },
        },
        grid: {
          borderColor: "#e7e7e7",
          row: {
            colors: ["#f3f3f3", "transparent"],
            opacity: 0.5,
          },
        },
        markers: {
          size: 4,
        },
        xaxis: {
          categories: [
            "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"
          ],
          title: {
            text: "Meses",
          },
          labels: {
            style: {
              colors: "#9aa0ac",
            },
          },
        },
        yaxis: {
          title: {
            text: "Efectivo (NIO)",
          },
          labels: {
            style: {
              color: "#9aa0ac",
            },
            formatter: (value) => { return formateador.format(value) },
          },
          min: 0,
          max: parseFloat(res.max.importe),
        },
        legend: {
          position: "top",
          horizontalAlign: "right",
          floating: true,
          offsetY: -30,
          offsetX: -5,
        },
        tooltip: {
          y: {
            formatter: function (val) {
              return formateador.format(val);
            }
          }
        }
      };

      myChart = new ApexCharts(document.querySelector("#prestamos"), options);

      myChart.render();
    }
  };
}
