document.addEventListener('DOMContentLoaded', function(){
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
      var ctx = document.getElementById("movimiento").getContext("2d");
      var myChart = new Chart(ctx, {
        type: "pie",
        data: {
          datasets: [
            {
              data: [
                res.inicial,
                res.ingreso,
                res.egreso,
                res.saldo,
              ],
              backgroundColor: [
                "#6c757d",
                "#5da5da",
                "#faa43a",
                "#60bd68"
              ],
              borderColor: "#ffffff",
              borderWidth: 2,
              label: "Movimientos",
            },
          ],
          labels: [
            "Monto inicial: " + res.decimales.inicial,
            "Ingresos: " + res.decimales.ingreso,
            "Egresos: " + res.decimales.egreso,
            "Saldo: " + res.decimales.saldo,
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          legend: {
            position: "bottom",
            labels: {
              fontColor: "#343a40",
              boxWidth: 20,
              fontSize: 14,
            }
          },
        },
      });
    }
  };
}
