document.addEventListener("DOMContentLoaded", function () {
  if (document.getElementById("typeFilter")) {
    document.getElementById("typeFilter").addEventListener("change", (e) => {
      if (e.target.value == "custom") {
        document.getElementById("inputStartDate").style.display =
          "inline-block";
        document.getElementById("inputStartDate").required = true;
        document.getElementById("inputStartDate").focus();
      } else {
        document.getElementById("inputStartDate").style.display = "none";
        document.getElementById("inputStartDate").required = false;
      }
    });
  }

  if (document.getElementById("update_data")) {
    load_sales();
    document.getElementById("update_data").addEventListener("click", () => {
      load_sales();
    });
  }

  function load_sales() {
    
    let filter = document.getElementById("typeFilter").value;
    let custom = document.getElementById("inputStartDate").value;

    let htmlLoading = "";

    htmlLoading += "<tr>";
    htmlLoading +=
      "<td class='column-primary id column-id' colspan='6' style='text-align:center;float:none;'><span class='spinner is-active' style='float:none;'></span></td>";
    htmlLoading += "</tr>";

    document.getElementById("table-institutes-payment").innerHTML =
      htmlLoading;

    const XHR = new XMLHttpRequest();
    XHR.open("POST", list_orders_sales.url, true);
    XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    XHR.responseType = "text";
    XHR.send(
      "action=" +
        list_orders_sales.action +
        "&filter=" +
        filter +
        "&custom=" +
        custom
    );
    XHR.onload = function () {
      if (this.readyState == "4" && XHR.status === 200) {
        let result = JSON.parse(XHR.responseText);

        if (result.status == "success") {
          document.getElementById("table-institutes-payment").innerHTML =
            result.html;
            document.getElementById("gross").innerHTML = result.data.gross;
            document.getElementById("orders").innerHTML = result.data.orders.length;
            document.getElementById("net").innerHTML = result.data.net;
            document.getElementById("a_fee").innerHTML = result.data.alliance_fee;
            document.getElementById("i_fee").innerHTML = result.data.institute_fee;
            document.getElementById("p_fees").innerHTML = result.data.fee_payment;
            document.getElementById("e_fees").innerHTML = result.data.fee_system;
            document.getElementById("tax").innerHTML =
            result.data.tax;
            document.getElementById("receivable").innerHTML =
            result.data.receivable;

          // Eliminar todos los elementos con id payment-options
          var paymentOptions = document.querySelectorAll("#payment-options");
          for (var i = 0; i < paymentOptions.length; i++) {
            paymentOptions[i].remove();
          }

          // Crear nuevos elementos dentro de card-totals-sales
          var cardTotalsSales = document.getElementById("card-totals-sales");
          Object.entries(result.data.payment_methods).forEach((element) => {
            var newElement = document.createElement("div");
            newElement.className = "card-report-sales tooltip";
            newElement.style = "background-color: #d4c6e7;";
            newElement.id = "payment-options";
            newElement.title = `Payments made with ${element[0]}`
            newElement.innerHTML = `
                          <div>${element[0]}</div>
                          <div style="margin-top: 10px;"><strong id="${element[0]}">${element[1]}</strong></div>
                        `;
            cardTotalsSales.appendChild(newElement);
          });
        }
      }
    };
  }

  if (document.getElementById("update_data_chart")) {
    var chart;
    load_chart();
    document
      .getElementById("update_data_chart")
      .addEventListener("click", () => {
        load_chart();
      });
  }

  function load_chart() {
    document.getElementById("loading").style = 'display: block; text-align: center; margin-bottom: 18px';
    let filter = document.getElementById("typeFilter").value;
    let custom = document.getElementById("inputStartDate").value;
    const XHR = new XMLHttpRequest();
    XHR.open("POST", load_chart_data.url, true);
    XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    XHR.responseType = "text";
    XHR.send(
      "action=" +
        load_chart_data.action +
        "&filter=" +
        filter +
        "&custom=" +
        custom
    );
    XHR.onload = function () {
      if (this.readyState == "4" && XHR.status === 200) {
        let result = JSON.parse(XHR.responseText);

        if (result.status == "success") {
          document.getElementById("gross").innerHTML = result.orders.gross;
          document.getElementById("orders").innerHTML = result.orders.orders.length;
          document.getElementById("net").innerHTML = result.orders.net;
          document.getElementById("fees").innerHTML = result.orders.fees;
          document.getElementById("tax").innerHTML =
          result.orders.tax;
          document.getElementById("receivable").innerHTML =
          result.orders.receivable;

          // reload chart
          const chartData = result.chart_data;
          const ctx = document.getElementById("myChart").getContext("2d");
          if (chart) {
            chart.data.labels = chartData.labels;
            chart.data.datasets = chartData.datasets;
            chart.update(); // Update the chart
          } else {
            chart = new Chart(ctx, {
              type: "bar",
              data: chartData,
              options: {
                title: {
                  display: true,
                  text: "Gender Comparison Graph",
                },
              },
            });
          }

          document.getElementById("loading").style = 'display: none';
        }
      }
    };
  }
});
