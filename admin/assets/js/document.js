document.addEventListener("DOMContentLoaded", function () {
  button_export_xlsx = document.getElementById("button-export-xlsx");
  input_birth_date = document.querySelectorAll(".birth_date");

  if (input_birth_date) {
    input_birth_date.forEach((input) => {
      flatpickr(input, {
        dateFormat: "m/d/Y",
      });
    });
  }

  if (button_export_xlsx) {
    button_export_xlsx.addEventListener("click", () => {
      student_id = button_export_xlsx.getAttribute("data-id");

      const XHR = new XMLHttpRequest();
      XHR.open("POST", get_student_details.url, true);
      XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      XHR.responseType = "text";
      XHR.send(
        "action=" + get_student_details.action + "&student_id=" + student_id
      );
      XHR.onload = function () {
        if (this.readyState == "4" && XHR.status === 200) {
          let result = JSON.parse(XHR.responseText);
          if (result.status == "success") {
            let workbook = XLSX.utils.book_new();
            let column = 1;
            worksheet = XLSX.utils.json_to_sheet([]);

            XLSX.utils.book_append_sheet(
              workbook,
              worksheet,
              "STUDENT DETAILS"
            );

            column += 2;

            XLSX.utils.sheet_add_json(
              worksheet,
              [["Program", result.data[0].program, ""]],
              { origin: "A" + column, skipHeader: true }
            );
            column += 1;
            XLSX.utils.sheet_add_json(
              worksheet,
              [["Grade", result.data[0].grade, ""]],
              { origin: "A" + column, skipHeader: true }
            );
            column += 2;

            XLSX.utils.sheet_add_json(worksheet, [["Person Information"]], {
              origin: "A " + column,
              skipHeader: true,
            });
            column += 2;

            XLSX.utils.sheet_add_json(
              worksheet,
              [["Type Document", result.data[0].type_document, ""]],
              { origin: "A" + column, skipHeader: true }
            );
            column += 1;
            XLSX.utils.sheet_add_json(
              worksheet,
              [["ID Document", result.data[0].id_document, ""]],
              { origin: "A" + column, skipHeader: true }
            );
            column += 1;
            XLSX.utils.sheet_add_json(
              worksheet,
              [["First Name", result.data[0].first_name, ""]],
              { origin: "A" + column, skipHeader: true }
            );
            column += 1;
            XLSX.utils.sheet_add_json(
              worksheet,
              [["Last Name", result.data[0].last_name, ""]],
              { origin: "A" + column, skipHeader: true }
            );
            column += 1;
            XLSX.utils.sheet_add_json(
              worksheet,
              [["Birth Date", result.data[0].birth_date, ""]],
              { origin: "A" + column, skipHeader: true }
            );
            column += 1;
            XLSX.utils.sheet_add_json(
              worksheet,
              [["Gender", result.data[0].gender, ""]],
              { origin: "A" + column, skipHeader: true }
            );
            column += 1;
            XLSX.utils.sheet_add_json(
              worksheet,
              [["Country", result.data[0].country, ""]],
              { origin: "A" + column, skipHeader: true }
            );
            column += 1;
            XLSX.utils.sheet_add_json(
              worksheet,
              [["City", result.data[0].city, ""]],
              { origin: "A" + column, skipHeader: true }
            );
            column += 1;
            XLSX.utils.sheet_add_json(
              worksheet,
              [["Postal code", result.data[0].postal_code, ""]],
              { origin: "A" + column, skipHeader: true }
            );
            column += 1;
            XLSX.utils.sheet_add_json(
              worksheet,
              [["Email", result.data[0].email, ""]],
              { origin: "A" + column, skipHeader: true }
            );
            column += 1;
            XLSX.utils.sheet_add_json(
              worksheet,
              [["Phone", result.data[0].phone, ""]],
              { origin: "A" + column, skipHeader: true }
            );

            column += 2;
            XLSX.utils.sheet_add_json(worksheet, [["Partner Information"]], {
              origin: "A " + column,
              skipHeader: true,
            });
            column += 2;

            XLSX.utils.sheet_add_json(
              worksheet,
              [["Type Document", result.data[0].type_document_parent, ""]],
              { origin: "A" + column, skipHeader: true }
            );
            column += 1;
            XLSX.utils.sheet_add_json(
              worksheet,
              [["ID Document", result.data[0].id_document_parent, ""]],
              { origin: "A" + column, skipHeader: true }
            );
            column += 1;
            XLSX.utils.sheet_add_json(
              worksheet,
              [["First Name", result.data[0].first_name_parent]],
              { origin: "A" + column, skipHeader: true }
            );
            column += 1;
            XLSX.utils.sheet_add_json(
              worksheet,
              [["Last Name", result.data[0].last_name_parent]],
              { origin: "A" + column, skipHeader: true }
            );
            column += 1;
            XLSX.utils.sheet_add_json(
              worksheet,
              [["Birth Date", result.data[0].birth_date_parent]],
              { origin: "A" + column, skipHeader: true }
            );
            column += 1;
            XLSX.utils.sheet_add_json(
              worksheet,
              [["Gender", result.data[0].gender_parent, ""]],
              { origin: "A" + column, skipHeader: true }
            );
            column += 1;
            XLSX.utils.sheet_add_json(
              worksheet,
              [["Country", result.data[0].country_parent, ""]],
              { origin: "A" + column, skipHeader: true }
            );
            column += 1;
            XLSX.utils.sheet_add_json(
              worksheet,
              [["City", result.data[0].city_parent, ""]],
              { origin: "A" + column, skipHeader: true }
            );
            column += 1;
            XLSX.utils.sheet_add_json(
              worksheet,
              [["Postal code", result.data[0].post_code_parent, ""]],
              { origin: "A" + column, skipHeader: true }
            );
            column += 1;
            XLSX.utils.sheet_add_json(
              worksheet,
              [["Email", result.data[0].email_parent, ""]],
              { origin: "A" + column, skipHeader: true }
            );
            column += 1;
            XLSX.utils.sheet_add_json(
              worksheet,
              [["Phone", result.data[0].phone_parent, ""]],
              { origin: "A" + column, skipHeader: true }
            );
            column += 1;
            XLSX.utils.sheet_add_json(
              worksheet,
              [["Occupation", result.data[0].occupation_parent, ""]],
              { origin: "A" + column, skipHeader: true }
            );

            column += 2;
            XLSX.utils.sheet_add_json(worksheet, [["Documents"]], {
              origin: "A " + column,
              skipHeader: true,
            });
            column += 2;
            XLSX.utils.sheet_add_json(
              worksheet,
              [["Documents", "Status", "URL"]],
              { origin: "A " + column, skipHeader: true }
            );
            column += 2;

            let student_document = result.data[0].documents;

            student_document.forEach((doc) => {
              let ws = XLSX.utils.sheet_add_json(
                worksheet,
                [[doc.name, doc.status, doc.url]],
                { origin: "A " + column, skipHeader: true }
              );
              worksheet["C" + column].l = { Target: doc.url };
              column += 1;
            });

            worksheet["!cols"] = [{ wch: 30 }, { wch: 30 }, { wch: 100 }];

            let name =
              result.data[0].first_name +
              "_" +
              result.data[0].last_name +
              "_STUDENT_DETAIL.xlsx";
            XLSX.writeFile(workbook, name, { compression: true });
          }
        }
      };
    });
  }


  let initialized = false;
  if(initialized == false) {
    watchButtons();
  }

  function watchButtons() {
    initialized = true;
    buttons_change_status = document.querySelectorAll(".change-status");
    buttons_change_status.forEach((button) => {
      button.addEventListener("click", (e) => {
        const action = button.textContent;

        const confirmMessage = `Are you sure you want to ${action.toLowerCase()} this document?`;

        if (confirm(confirmMessage)) {
          buttons_status(button);
        }
      });
    });
  }

  function buttons_status(button) {
      // Deshabilitar todos los botones con la clase change-status
      buttons_change_status.forEach((btn) => {
        btn.disabled = true;
      });
      document_id = button.getAttribute("data-document-id");
      status_id = button.getAttribute("data-status");
      student_id = button.getAttribute("data-student-id");

      let htmlLoading = "";
      htmlLoading +=
        "<td class='column-primary id column-id' colspan='3' style='text-align:center;float:none;'><span class='spinner is-active' style='float:none;'></span></td>";

      document.getElementById("tr_document_" + document_id).innerHTML =
        htmlLoading;
      document.getElementById("notice-status").style.display = "none";

      const XHR = new XMLHttpRequest();
      XHR.open("POST", update_status_documents.url, true);
      XHR.setRequestHeader(
        "Content-type",
        "application/x-www-form-urlencoded"
      );
      XHR.responseType = "text";
      XHR.send(
        "action=" +
          update_status_documents.action +
          "&student_id=" +
          student_id +
          "&document_id=" +
          document_id +
          "&status=" +
          status_id
      );
      XHR.onload = function () {
        if (this.readyState == "4" && XHR.status === 200) {
          let result = JSON.parse(XHR.responseText);
          if (result.status == "success") {
            // document.getElementById("notice-status").style.display = "block";
            document.getElementById("tr_document_" + document_id).innerHTML =
              result.html;

              buttons_change_status.forEach((button) => {
                const newButton = button.cloneNode(true);
                button.parentNode.replaceChild(newButton, button);
              });
              watchButtons();
            // setTimeout(() => {
            //   document.getElementById("notice-status").style.display = "none";
            // }, 2000);
          }

          // Habilitar nuevamente los botones cuando se completa la solicitud
          buttons_change_status.forEach((btn) => {
            btn.disabled = false;
          });
        }
      };
  }
});
