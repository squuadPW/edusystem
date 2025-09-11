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
        const action = button.textContent.trim();

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


  var modalCloseElementsChangeDeadline = document.querySelectorAll(
    "#change-deadline-exit-icon, #change-deadline-exit-button"
  );
  if (modalCloseElementsChangeDeadline) {
    modalCloseElementsChangeDeadline.forEach(function (element) {
      element.addEventListener("click", function () {
        document.getElementById('date_input_container').style.display = 'block';
        document.getElementById("change-deadline-form").reset();
        document.getElementById("change-deadline-modal").style.display = "none";
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

  let marginHeaderDocument = 0;
  let marginFooterDocument = 0;
  let orientation = 'portrait';
  let widthDocument = '210mm';
  let heightDocument = '287mm';
  let margin = [0, 0];
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
            const modal_body = document.getElementById("content-pdf");
            if (modal_body && (this.response.header && this.response.header != '')) {
              // Eliminar header existente si ya está presente
              const existingHeader = document.getElementById('header-document');
              if (existingHeader) {
                  existingHeader.remove();
              }
          
              // Crear y agregar nuevo header
              const headerElement = document.createElement('div');
              headerElement.id = 'header-document';
              headerElement.innerHTML = this.response.header;
              modal_body.parentNode.insertBefore(headerElement, modal_body);
              
              // Forzar reflow y luego calcular altura
              setTimeout(() => {
                const headerCalculated = document.getElementById('header-document');
                void headerCalculated.offsetHeight; // Esto fuerza un reflow
                marginHeaderDocument = Math.round(((headerCalculated.offsetHeight + 10) * 0.264583333));
              }, 1000);
          }
            
            // Función simplificada sin CORS
            const convertToBase64 = async (url) => {
              try {
                // Intenta con CORS
                const img = await new Promise((resolve, reject) => {
                  const img = new Image();
                  img.crossOrigin = "Anonymous";
                  img.onload = () => resolve(img);
                  img.onerror = reject;
                  img.src = url;
                });
                
                const canvas = document.createElement('canvas');
                canvas.width = img.width;
                canvas.height = img.height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0);
                return canvas.toDataURL();
              } catch (error) {
                // Fallback a fetch si el servidor no permite CORS
                try {
                  const response = await fetch(url);
                  const blob = await response.blob();
                  return await new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onloadend = () => resolve(reader.result);
                    reader.onerror = reject;
                    reader.readAsDataURL(blob);
                  });
                } catch (fetchError) {
                  return url; // Devuelve la URL original como último recurso
                }
              }
            };
  
            let processedHtml = this.response.html;
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = this.response.html;
            orientation = this.response.document.orientation;
            widthDocument = this.response.document.width_size;
            heightDocument = this.response.document.height_size;

            const imgElement = tempDiv.querySelector('img');
            if (imgElement) {
              try {
                imgElement.src = await convertToBase64(imgElement.src);
                processedHtml = tempDiv.innerHTML;
              } catch (error) {
              }
            }
  
            modal_body.innerHTML = processedHtml;

            if (modal_body && (this.response.footer && this.response.footer != '')) {
              // Eliminar footer existente si ya está presente
              const existingFooter = document.getElementById('footer-document');
              if (existingFooter) {
                  existingFooter.remove();
              }
          
              // Crear y agregar nuevo footer
              const footerElement = document.createElement('div');
              footerElement.id = 'footer-document';
              footerElement.innerHTML = this.response.footer;
              modal_body.after(footerElement);
              
              // Forzar reflow y luego calcular altura
              setTimeout(() => {
                const footerCalculated = document.getElementById('footer-document');
                void footerCalculated.offsetHeight; // Esto fuerza un reflow
                marginFooterDocument = Math.round(((footerCalculated.offsetHeight + 10) * 0.264583333));
              }, 1000);
          }

            setTimeout(() => {
              margin = this.response.document.margin_required == 1 ? [marginHeaderDocument, 0, marginFooterDocument, 0] : margin;
            }, 1500);

            if (document.getElementById("qrcode") && this.response.url) {
              const qrCode = new QRCodeStyling({
                width: 100,
                height: 100,
                data: this.response.url,
                image: this.response.image_url,
                dotsOptions: { color: "#000000" },
                backgroundOptions: { color: "#ffffff" },
                imageOptions: {
                  crossOrigin: "anonymous"
                }
              });
              
              qrCode.append(document.getElementById("qrcode"));
            }

            if (orientation != 'portrait') {
              document.querySelector('.modal-document-export').style.minWidth = heightDocument;
              document.querySelector('.modal-document-export').style.minHeight = widthDocument;

              document.getElementById('content-pdf').style.minWidth = heightDocument;
              document.getElementById('content-pdf').style.minHeight = widthDocument;
            } else {
              document.querySelector('.modal-document-export').style.minWidth = widthDocument;
              // document.querySelector('.modal-document-export').style.minHeight = heightDocument;

              document.getElementById('content-pdf').style.minWidth = widthDocument;
              // document.getElementById('content-pdf').style.minHeight = heightDocument;
            }

            document.querySelector('.modal-document-export').style.padding = '0';

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
              margin: margin,
              filename: 'document.pdf',
              image: { type: "jpeg", quality: 1 },
              jsPDF: { 
                  unit: "mm", 
                  format: "a4", 
                  orientation: orientation
              },
              html2canvas: { 
                  scale: 3,
                  useCORS: true
              },
              pagebreak: { after: ".pagebreak" },
          };
  
          // Generar el PDF
          const pdf = await html2pdf().set(opt).from(element).toPdf().get('pdf');
  
          if (orientation == 'portrait') {
            const pageCount = pdf.internal.getNumberOfPages();

            // Capturar el contenido del header
            const headerElement = document.getElementById("header-document");
            let imgDataHeader = '';
            let canvasHeader = null;
            if (headerElement) {
              canvasHeader = await html2canvas(headerElement, { scale: 2 });
              imgDataHeader = canvasHeader.toDataURL("image/jpeg");
            }
    
            // Capturar el contenido del footer
            const footerElement = document.getElementById("footer-document");
            let imgData = '';
            let canvas = null;
            if (footerElement) {
              canvas = await html2canvas(footerElement, { scale: 2 });
              imgData = canvas.toDataURL("image/jpeg");
            }

            // Agregar el footer manualmente
            for (let i = 1; i <= pageCount; i++) {
                pdf.setPage(i);
                const imgWidth = pdf.internal.pageSize.width; // Ancho de la imagen igual al ancho de la página

                // Header
                if (headerElement) {
                  const imgHeightHeader = (canvasHeader.height * imgWidth) / canvasHeader.width; // Mantener la proporción
                  pdf.addImage(imgDataHeader, 'JPEG', 0, 0, imgWidth, imgHeightHeader);
                }

                // Footer
                if (footerElement) {
                  const imgHeight = (canvas.height * imgWidth) / canvas.width; // Mantener la proporción
                  const y = pdf.internal.pageSize.height - imgHeight; // Posición Y para que esté en la parte inferior
                  pdf.addImage(imgData, 'JPEG', 0, y, imgWidth, imgHeight);
                }
            }
          }
  
          // Guardar el PDF
          pdf.save('document.pdf'); // No se usa .then() aquí
          download_grades.disabled = false; // Habilitar el botón nuevamente
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

function changeDeadline(doc) {
  document.querySelector("input[name=document_change_deadline_id]").value = doc.id;
  document.querySelector("input[name=document_change_deadline_date]").value = doc.max_date_upload;
  document.querySelector("input[name=document_change_deadline_name]").value =
    doc.document_id;
  document.getElementById("document_change_deadline_text").innerHTML = doc.document_id;

  const modal = document.getElementById("change-deadline-modal");
  modal.style.display = "block";
}

function toggleDateInput() {
  // Obtiene referencias a los elementos del DOM
  var checkbox = document.getElementById('allow_empty_date');
  var dateContainer = document.getElementById('date_input_container');
  var dateInput = document.getElementById('document_change_deadline_date');
  
  if (checkbox.checked) {
      // Si el checkbox está marcado:
      dateContainer.style.display = 'none';  // Oculta el contenedor del input de fecha
      dateInput.removeAttribute('required');  // Quita el atributo required
      dateInput.value = '';  // Limpia el valor del input
  } else {
      // Si el checkbox está desmarcado:
      dateContainer.style.display = 'block';  // Muestra el contenedor del input de fecha
      dateInput.setAttribute('required', 'required');  // Agrega el atributo required
  }
}