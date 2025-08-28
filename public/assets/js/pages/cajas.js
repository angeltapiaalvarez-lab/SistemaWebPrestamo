document.addEventListener('DOMContentLoaded', function () {
  movimientoGrafico();
})

function movimientoGrafico() {
  const url = base_url + "cajas/movimientos";
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      const darkMode = document.body.classList.contains('dark');
      const legendFontColor = darkMode ? "#ffffff" : "#343a40";

      // Monto inicial vs Egresos
      var canvasInicial = document.getElementById("inicialEgreso");
      canvasInicial.height = 400;
      var ctxInicial = canvasInicial.getContext("2d");
      new Chart(ctxInicial, {
        type: "pie",
        data: {
          datasets: [
            {
              data: [
                res.inicial,
                res.egreso,
              ],
              backgroundColor: [
                "#6c757d",
                "#faa43a"
              ],
              borderColor: "#ffffff",
              borderWidth: 2,
              label: "Inicial vs Egresos",
            },
          ],
          labels: [
            "Monto inicial: " + res.decimales.inicial,
            "Egresos: " + res.decimales.egreso,
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          legend: {
            position: "bottom",
            labels: {
              fontColor: legendFontColor,
              boxWidth: 20,
              fontSize: 14,
            }
          },
        },
      });

      // Ingresos vs Saldo
      var canvasIngreso = document.getElementById("ingresoSaldo");
      canvasIngreso.height = 400;
      var ctxIngreso = canvasIngreso.getContext("2d");
      new Chart(ctxIngreso, {
        type: "pie",
        data: {
          datasets: [
            {
              data: [
                res.ingreso,
                res.saldo,
              ],
              backgroundColor: [
                "#5da5da",
                "#60bd68"
              ],
              borderColor: "#ffffff",
              borderWidth: 2,
              label: "Ingresos vs Saldo",
            },
          ],
          labels: [
            "Ingresos: " + res.decimales.ingreso,
            "Saldo: " + res.decimales.saldo,
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          legend: {
            position: "bottom",
            labels: {
              fontColor: legendFontColor,
              boxWidth: 20,
              fontSize: 14,
            }
          },
        },
      });
    }
  };
}
