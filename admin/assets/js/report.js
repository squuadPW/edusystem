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

    document.getElementById("table-institutes-payment").innerHTML = htmlLoading;

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
          document.getElementById("orders").innerHTML =
            result.data.orders.length;
          document.getElementById("net").innerHTML = result.data.net;
          document.getElementById("a_fee").innerHTML = result.data.alliance_fee;
          document.getElementById("i_fee").innerHTML =
            result.data.institute_fee;
          document.getElementById("p_fees").innerHTML = result.data.fee_payment;
          document.getElementById("e_fees").innerHTML = result.data.fee_system;
          document.getElementById("tax").innerHTML = result.data.tax;
          document.getElementById("receivable").innerHTML =
            result.data.receivable;
          document.getElementById("discount").innerHTML =
            result.data.discount;
          document.getElementById("adjusted_gross").innerHTML =
            result.data.adjusted_gross;

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
            newElement.title = `Payments made with ${element[0]}`;
            newElement.innerHTML = `
                          <div>${element[0] ? element[0] : 'Payments made with split'}</div>
                          <div style="margin-top: 10px;"><strong id="${element[0]}">${element[1]}</strong></div>
                        `;
            cardTotalsSales.appendChild(newElement);
          });
        }
      }
    };
  }

  if (document.getElementById("update_data_sales_product")) {
    load_sales_product();
    document.getElementById("update_data_sales_product").addEventListener("click", () => {
      load_sales_product();
    });
  }

  function load_sales_product() {
    let filter = document.getElementById("typeFilter").value;
    let custom = document.getElementById("inputStartDate").value;

    let htmlLoading = "";

    htmlLoading += "<tr>";
    htmlLoading +=
      "<td class='column-primary id column-id' colspan='7' style='text-align:center;float:none;'><span class='spinner is-active' style='float:none;'></span></td>";
    htmlLoading += "</tr>";

    document.getElementById("table-institutes-payment").innerHTML = htmlLoading;

    const XHR = new XMLHttpRequest();
    XHR.open("POST", list_sales_product.url, true);
    XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    XHR.responseType = "text";
    XHR.send(
      "action=" +
        list_sales_product.action +
        "&filter=" +
        filter +
        "&custom=" +
        custom
    );
    XHR.onload = function () {
      if (this.readyState == "4" && XHR.status === 200) {
        let result = JSON.parse(XHR.responseText);

        if (result.status == "success") {
          document.getElementById("table-institutes-payment").innerHTML = result.html;
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
    document.getElementById("loading").style =
      "display: block; text-align: center; margin-bottom: 18px";
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
          document.getElementById("orders").innerHTML =
            result.orders.orders.length;
          document.getElementById("net").innerHTML = result.orders.net;
          document.getElementById("fees").innerHTML = result.orders.fees;
          document.getElementById("tax").innerHTML = result.orders.tax;
          document.getElementById("receivable").innerHTML =
            result.orders.receivable;
          document.getElementById("discount").innerHTML =
            result.orders.discount;
          document.getElementById("adjusted_gross").innerHTML =
            result.orders.adjusted_gross;

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

          document.getElementById("loading").style = "display: none";
        }
      }
    };
  }

  if (document.getElementById("update_data_accounts_receivable")) {
    load_accounts_receivables();
    document
      .getElementById("update_data_accounts_receivable")
      .addEventListener("click", () => {
        load_accounts_receivables();
      });
  }

  function load_accounts_receivables() {
    let filter = document.getElementById("typeFilter").value;
    let custom = document.getElementById("inputStartDate").value;

    let htmlLoading = "";

    htmlLoading += "<tr>";
    htmlLoading +=
      "<td class='column-primary id column-id' colspan='7' style='text-align:center;float:none;'><span class='spinner is-active' style='float:none;'></span></td>";
    htmlLoading += "</tr>";

    document.getElementById("table-institutes-payment").innerHTML = htmlLoading;

    const XHR = new XMLHttpRequest();
    XHR.open("POST", list_accounts_receivables.url, true);
    XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    XHR.responseType = "text";
    XHR.send(
      "action=" +
        list_accounts_receivables.action +
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
          document.getElementById("orders").innerHTML =
            result.data.cuotes.length;
          document.getElementById("receivable").innerHTML =
            result.data.receivable;
        }
      }
    };
  }

  if (document.getElementById("update_data_report_students")) {
    load_report_students();
    document
      .getElementById("update_data_report_students")
      .addEventListener("click", () => {
        load_report_students();
      });
  }

  function load_report_students() {
    let academic_period = document.getElementById("academic_period").value;
    let academic_period_cut = document.getElementById("academic_period_cut").value;
    let grade = document.getElementById("grade").value;

    let htmlLoading = "";

    htmlLoading += "<tr>";
    htmlLoading +=
      "<td class='column-primary id column-id' colspan='9' style='text-align:center;float:none;'><span class='spinner is-active' style='float:none;'></span></td>";
    htmlLoading += "</tr>";

    document.getElementById("table-institutes-payment").innerHTML = htmlLoading;

    const XHR = new XMLHttpRequest();
    XHR.open("POST", list_report_students.url, true);
    XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    XHR.responseType = "text";
    XHR.send(
      "action=" +
        list_report_students.action +
        "&academic_period=" +
        academic_period +
        "&period=" +
        grade +
        "&academic_period_cut=" +
        academic_period_cut
    );
    XHR.onload = function () {
      if (this.readyState == "4" && XHR.status === 200) {
        let result = JSON.parse(XHR.responseText);

        if (result.status == "success") {
          document.getElementById("table-institutes-payment").innerHTML =
            result.html;
          document.getElementById("students").innerHTML =
            result.data.length;
          // document.getElementById("receivable").innerHTML =
          //   result.data.receivable;
        }
      }
    };
  }

  if (document.getElementById("update_data_report_current_students")) {
    load_report_current_students();
  }

  function load_report_current_students() {
    let htmlLoading = "";

    htmlLoading += "<tr>";
    htmlLoading +=
      "<td class='column-primary id column-id' colspan='3' style='text-align:center;float:none;'><span class='spinner is-active' style='float:none;'></span></td>";
    htmlLoading += "</tr>";

    document.getElementById("table-current-student").innerHTML = htmlLoading;

    htmlLoading = "";
    htmlLoading += "<tr>";
    htmlLoading +=
      "<td class='column-primary id column-id' style='text-align:center;float:none;'><span class='spinner is-active' style='float:none;'></span></td>";
    htmlLoading += "</tr>";
    document.getElementById("table-not-current-student").innerHTML = htmlLoading;

    const XHR = new XMLHttpRequest();
    XHR.open("POST", list_report_current_students.url, true);
    XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    XHR.responseType = "text";
    XHR.send(
      "action=" +
      list_report_current_students.action
    );
    XHR.onload = function () {
      if (this.readyState == "4" && XHR.status === 200) {
        let result = JSON.parse(XHR.responseText);

        if (result.status == "success") {
          document.getElementById("table-current-student").innerHTML =
            result.html;
          document.getElementById("current-students").innerHTML =
            result.data.length;

            document.getElementById("table-not-current-student").innerHTML =
            result.html_not_current;
          document.getElementById("not-current-students").innerHTML =
            result.students_not_current.length;
          // document.getElementById("receivable").innerHTML =
          //   result.data.receivable;
        }
      }
    };
  }

  export_excel_students = document.getElementById("export_excel_students");
  if (export_excel_students) {
    export_excel_students.addEventListener("click", () => {
      // Selecciona la tabla por su ID
      const table = document.querySelector('#table-institutes-payment');
      const data = [];

      // Definir los encabezados fijos
      const headers = [
          'Academic period',
          'Student',
          'Student document',
          'Student email',
          'Parent',
          'Parent email',
          'Country',
          'Grade',
          'Program',
          'Institute'
      ];
      
      data.push(headers); // Agrega los encabezados al array de datos

      // Itera sobre las filas de la tabla (empezando desde la primera fila de datos)
      for (let i = 0; i < table.rows.length; i++) {
          const rowData = [];
          const row = table.rows[i];

          // Itera sobre las celdas de cada fila
          for (let j = 0; j < row.cells.length; j++) {
              rowData.push(row.cells[j].textContent.trim()); // Obtiene el contenido de cada celda
          }

          data.push(rowData); // Agrega los datos de la fila al array
      }

      // Crea un nuevo libro de trabajo
      const wb = XLSX.utils.book_new();
      // Convierte los datos a una hoja de cálculo
      const ws = XLSX.utils.aoa_to_sheet(data);
      // Agrega la hoja al libro
      XLSX.utils.book_append_sheet(wb, ws, 'Report students');

      // Exporta el archivo XLSX
      XLSX.writeFile(wb, 'report-students.xlsx');
    });
  }
});
