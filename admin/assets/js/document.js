document.addEventListener("DOMContentLoaded", function () {
  button_export_xlsx = document.getElementById("button-export-xlsx");
  input_birth_date = document.querySelectorAll(".birth_date");
  modal = document.getElementById("decline-modal"); // assume you have a modal with id "decline-modal"
  documentId = null;
  statusId = null;

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
  if (initialized == false) {
    watchButtons();
  }

  function watchButtons() {
    initialized = true;
    buttons_change_status = document.querySelectorAll(".change-status");
    other_buttons_document = document.querySelectorAll(
      ".other-buttons-document"
    );
    buttons_change_status.forEach((button) => {
      button.addEventListener("click", (e) => {
        documentId = button.dataset.documentId;
        statusId = button.dataset.status;
        const action = button.textContent;

        const confirmMessage = `Are you sure you want to ${action.toLowerCase()} this document?`;
        if (confirm(confirmMessage)) {
          if (action.toLowerCase() === "decline") {
            // Open modal with textarea for description
            modal.style.display = "block";
          } else {
            buttons_status(button);
          }
        }
      });
    });
  }

  var modalCloseElements = document.querySelectorAll(
    "#decline-exit-icon, #decline-exit-button"
  );
  if (modalCloseElements) {
    modalCloseElements.forEach(function (element) {
      element.addEventListener("click", function () {
        document.getElementById("decline-modal").style.display = "none";
        const textarea = document.querySelector(
          'textarea[name="decline-description"]'
        );
        textarea.value = "";
      });
    });
  }

  var modalSaveElements = document.querySelectorAll("#decline-save");
  if (modalSaveElements) {
    modalSaveElements.forEach(function (element) {
      element.addEventListener("click", function () {
        const textarea = document.querySelector(
          'textarea[name="decline-description"]'
        );
        const description = textarea.value;
        if (description) {
          button = document.querySelector(
            `[data-document-id="${documentId}"][data-status="${statusId}"]`
          );
          buttons_status(button, description);

          modal.style.display = "none";
          textarea.value = "";
        } else {
          alert("The description is required");
        }
      });
    });
  }

  var modalCloseElementsUpload = document.querySelectorAll(
    "#upload-exit-icon, #upload-exit-button"
  );
  if (modalCloseElementsUpload) {
    modalCloseElementsUpload.forEach(function (element) {
      element.addEventListener("click", function () {
        document.getElementById("upload-form").reset();
        document.getElementById("upload-modal").style.display = "none";
      });
    });
  }

  var modalCloseElementsCertificateDocument = document.querySelectorAll(
    "#documentcertificate-exit-icon, #documentcertificate-exit-button"
  );
  if (modalCloseElementsCertificateDocument) {
    modalCloseElementsCertificateDocument.forEach(function (element) {
      element.addEventListener("click", function () {
        document.getElementById("documentcertificate-modal").style.display = "none";
        restoreButtonsCertificates(false);
      });
    });
  }

  var modalCloseElementsDetail = document.querySelectorAll(
    "#detail-exit-icon, #detail-exit-button"
  );
  if (modalCloseElementsDetail) {
    modalCloseElementsDetail.forEach(function (element) {
      element.addEventListener("click", function () {
        document.getElementById("detail-modal").style.display = "none";
      });
    });
  }

  function buttons_status(button, description = null) {
    // Deshabilitar todos los botones con la clase change-status
    buttons_change_status.forEach((btn) => {
      btn.disabled = true;
    });
    other_buttons_document.forEach((btn) => {
      btn.disabled = true;
    });
    document_id = button.getAttribute("data-document-id");
    status_id = button.getAttribute("data-status");
    student_id = button.getAttribute("data-student-id");

    let htmlLoading = "";
    htmlLoading +=
      "<td class='column-primary id column-id' colspan='12' style='text-align:center;float:none;'><span class='spinner is-active' style='float:none;'></span></td>";

    document.getElementById("tr_document_" + document_id).innerHTML =
      htmlLoading;
    document.getElementById("notice-status").style.display = "none";

    const XHR = new XMLHttpRequest();
    XHR.open("POST", update_status_documents.url, true);
    XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    XHR.responseType = "text";
    XHR.send(
      "action=" +
        update_status_documents.action +
        "&student_id=" +
        student_id +
        "&document_id=" +
        document_id +
        "&status=" +
        status_id +
        "&description=" +
        description
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

          if (result.rejected_document) {
            let rejected = result.rejected_document;
            rejected_documents(
              rejected.student_id,
              rejected.document_id,
              rejected.description
            );
          }

          // location.reload();
          // setTimeout(() => {
          //   document.getElementById("notice-status").style.display = "none";
          // }, 2000);
        } else {
          document.getElementById("tr_document_" + document_id).innerHTML =
            result.html;

          buttons_change_status.forEach((button) => {
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
          });
          watchButtons();

          if (
            confirm(`ERROR: ${result.message}`) ||
            !confirm(`ERROR: ${result.message}`)
          ) {
            location.reload();
          }
        }

        // Habilitar nuevamente los botones cuando se completa la solicitud
        buttons_change_status.forEach((btn) => {
          btn.disabled = false;
        });
        other_buttons_document.forEach((btn) => {
          btn.disabled = false;
        });
      }
    };
  }

  function rejected_documents(student_id, document_id, description) {
    const XHR = new XMLHttpRequest();
    XHR.open("POST", update_status_documents.url, true);
    XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    XHR.responseType = "text";
    XHR.send(
      "action=rejected_document_emails" +
        "&student_id=" +
        student_id +
        "&document_id=" +
        document_id +
        "&description=" +
        description
    );
    XHR.onload = function () {
      if (this.readyState == "4" && XHR.status === 200) {
      }
    };
  }

  const moodleActiveElements = document.querySelectorAll(".moodle-active");
  moodleActiveElements.forEach(function (element) {
    element.addEventListener("click", function () {
      if (element.dataset.moodle == "Yes") {
        loadLastAccessMoodle(element.dataset.student_id);
      }
    });
  });

  function loadLastAccessMoodle(student_id) {
    const XHR = new XMLHttpRequest();
    XHR.open("POST", last_access_moodle.url, true);
    XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    XHR.responseType = "text";
    XHR.send(
      "action=" + last_access_moodle.action + "&student_id=" + student_id
    );
    XHR.onload = function () {
      if (this.readyState == "4" && XHR.status === 200) {
        if (!JSON.parse(XHR.response).last_access) {
          alert("This user has not logged in to Moodle.");
        } else {
          alert(
            `This user's last login to Moodle was ${
              JSON.parse(XHR.response).last_access
            }`
          );
        }
      }
    };
  }

  let institute_id = document.getElementById("institute_id");
  institute_id.addEventListener("change", function (e) {
    document.querySelector("input[name=name_institute]").value = "";
    if (e.target.value == "other") {
      document.getElementById("institute_down").style.display = "contents";
      document.querySelector("input[name=name_institute]").required = true;
    } else {
      document.getElementById("institute_down").style.display = "none";
      document.querySelector("input[name=name_institute]").required = false;
    }
  });


  const buttons_certificate = document.querySelectorAll(
    ".download-document-certificate"
  );
  buttons_certificate.forEach((button) => {
    button.addEventListener("click", function () {
      restoreButtonsCertificates(true);

      let signature_required = this.dataset.signaturerequired;
      document.querySelector("input[name=document_certificate_id]").value = this.dataset.documentcertificate;
    
      if (signature_required == 1) {
        const modal = document.getElementById("documentcertificate-modal");
        modal.style.display = "block";
      } else {
        document.getElementById('documentcertificate-button').click();
      }
    });
  });

  function restoreButtonsCertificates(disabled = false) {
    const buttons_certificate = document.querySelectorAll(
      ".download-document-certificate"
    );
    buttons_certificate.forEach((button) => {
      button.disabled = disabled;
    });
  }

  let document_certificate_button = document.getElementById('documentcertificate-button');
  if (document_certificate_button) {
    document_certificate_button.addEventListener("click", function () {
      let document_certificate_id = document.querySelector("input[name=document_certificate_id]").value;
      let user_signature_id = document.querySelector("select[name=user_signature_id]").value;
      let student_id = document.querySelector("input[name=student_document_certificate_id]").value;
      
      const XHR = new XMLHttpRequest();
      XHR.open("POST", generate_document.url, true);
      XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      XHR.responseType = "json";
      XHR.send(
        "action=" + generate_document.action + "&document_certificate_id=" + document_certificate_id + "&user_signature_id=" + user_signature_id + "&student_id=" + student_id
      );
  
      XHR.onload = function () {
        if (this.readyState == 4 && XHR.status === 200) {
          (async () => {
            const modal_body = document.getElementById("modal-body-content");
            
            // Función simplificada sin CORS
            const convertToBase64 = async (url) => {
              try {
                const response = await fetch(url);
                const blob = await response.blob();
                return await new Promise((resolve, reject) => {
                  const reader = new FileReader();
                  reader.onloadend = () => resolve(reader.result);
                  reader.onerror = reject;
                  reader.readAsDataURL(blob);
                });
              } catch (error) {
                return url;
              }
            };
  
            let processedHtml = this.response.html;
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = this.response.html;
            
            const imgElement = tempDiv.querySelector('img');
            if (imgElement) {
              try {
                imgElement.src = await convertToBase64(imgElement.src);
                processedHtml = tempDiv.innerHTML;
              } catch (error) {
              }
            }
  
            modal_body.innerHTML = processedHtml;
  
            // Generar QR Code (versión simplificada)
            const qrCode = new QRCodeStyling({
              width: 100,
              height: 100,
              data: this.response.url,
              image: this.response.image_url,
              dotsOptions: { color: "#000000" },
              backgroundOptions: { color: "#ffffff" }
            });
            
            qrCode.append(document.getElementById("qrcode"));
  
            let modal = document.getElementById("modal-grades");
            modal.style.display = "block";
            document.body.classList.add("modal-open");
            
            setTimeout(() => window.scrollTo(0, 0), 100);
            document.getElementById("documentcertificate-modal").style.display = "none";
          })();
        }
      };
    });
  }

  let download_grades = document.getElementById("download-grades");
  if (download_grades) {
      download_grades.addEventListener("click", async (e) => {
        download_grades.disabled = true;
        var element = document.getElementById("content-pdf");
        var opt = {
          margin: [0.2, 0, 0, 0],
          filename: 'document.pdf',
          image: { type: "jpeg", quality: 0.98 },
          jsPDF: { unit: "in", format: "a4", orientation: "portrait" },
          html2canvas: { 
            scale: 3,
            useCORS: true // ¡Esto es clave para imágenes externas!
          }
        };
    
        html2pdf().set(opt).from(element).save();
        download_grades.disabled = false;
      });
  }

  let close_modal_grades = document.getElementById("close-modal-grades");
  if (close_modal_grades) {
      close_modal_grades.addEventListener("click", async (e) => {
        document.getElementById('modal-grades').style.display = 'none';
        document.body.classList.remove("modal-open");

        const input = document.querySelector("input[name='document_certificate_id']");
        const select = document.querySelector("select[name='user_signature_id']");
        const qrcode = document.getElementById("qrcode");
        
        if (input) input.value = '';
        if (select) select.value = '';
        if (qrcode) qrcode.innerHTML = '';

        restoreButtonsCertificates(false);

        setTimeout(() => {
          window.scrollTo(0, 0);
        }, 100);
      });
  }
});

function watchDetails(doc) {
  document.getElementById("date_user_registered").innerHTML =
    doc.created_at ?? "N/A";
  document.getElementById("date_upload_documents").innerHTML =
    doc.upload_at ?? "N/A";
  document.getElementById("date_status_change").innerHTML =
    doc.updated_at ?? "N/A";
  document.getElementById("description_status_changed").innerHTML =
    doc.description ?? "N/A";
  document.getElementById("status_changed_by").innerHTML = "Loading...";

  const XHR = new XMLHttpRequest();
  XHR.open("POST", get_approved_by.url, true);
  XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  XHR.responseType = "text";
  XHR.send(
    "action=" + get_approved_by.action + "&approved_by=" + doc.approved_by
  );
  XHR.onload = function () {
    if (this.readyState == "4" && XHR.status === 200) {
      document.getElementById("status_changed_by").innerHTML =
        JSON.parse(XHR.response).approved_by != " "
          ? JSON.parse(XHR.response).approved_by
          : "N/A";
    }
  };

  const modal = document.getElementById("detail-modal");
  modal.style.display = "block";
}

function uploadDocument(doc) {
  document.querySelector("input[name=document_upload_id]").value = doc.id;
  document.querySelector("input[name=document_upload_name]").value =
    doc.document_id;
  document.getElementById("document_upload_text").innerHTML = doc.document_id;

  const modal = document.getElementById("upload-modal");
  modal.style.display = "block";
}
