document.addEventListener("DOMContentLoaded", function () {
  const variableSelect = document.getElementById("variables-select");

  if (variableSelect) {
    variableSelect.addEventListener("change", function () {
      const content = this.value;
      if (!content) return;
  
      // Insertar en el editor
      if (typeof tinymce !== "undefined" && tinymce.get("content")) {
        tinymce.get("content").execCommand("mceInsertContent", false, content);
      } else {
        const textarea = document.getElementById("message");
        const startPos = textarea.selectionStart;
        const endPos = textarea.selectionEnd;
  
        textarea.value =
          textarea.value.substring(0, startPos) +
          content +
          textarea.value.substring(endPos);
  
        textarea.selectionStart = textarea.selectionEnd =
          startPos + content.length;
        textarea.dispatchEvent(new Event("input"));
      }
  
      // Resetear el selector después de la inserción
      this.value = "";
    });
  }

  var select = document.getElementById("templates-select");
  if (select) {
    select.addEventListener("change", function () {
      const content = this.value;
      const editor = tinymce.get("message");

      if (editor) {
        // Limpiar el editor primero
        editor.setContent("");
        // Insertar el nuevo contenido (solo si hay un template seleccionado)
        if (content) {
          editor.setContent(content);
        }
      } else {
        const textarea = document.getElementById("message");
        textarea.value = ""; // Limpiar
        if (content) {
          textarea.value = content; // Setear solo si hay contenido
        }
      }
    });
  }
});

const segmentButtons = document.querySelectorAll(".segment-button");
segmentButtons.forEach((button) => {
  button.addEventListener("click", (event) => {
    // Remove active class from all buttons
    segmentButtons.forEach((btn) => btn.classList.remove("active"));

    // Add active class to the clicked button
    event.target.classList.add("active");

    // Get the currently selected option
    const selectedOption = event.target.getAttribute("data-option");
    if (selectedOption == "group") {
      const formEmail = document.getElementById("by_email");
      formEmail.style.display = "none";

      const formGroup = document.getElementById("by_group");
      formGroup.style.display = "grid";

      document.querySelector('input[name="type"]').value = "1";

      document.querySelector('select[name="academic_period"]').required = true;
      document.querySelector(
        'select[name="academic_period_cut_filter"]'
      ).required = true;
      document.querySelector(
        'select[name="academic_period_cut"]'
      ).required = true;
      document.querySelector('input[name="email_student"]').required = false;

      document.getElementById("email_parent_container").style.display = "flex";
    } else if (selectedOption == "email") {
      const formOthers = document.getElementById("by_group");
      formOthers.style.display = "none";

      const formMe = document.getElementById("by_email");
      formMe.style.display = "block";

      document.querySelector('input[name="type"]').value = "2";

      document.querySelector('select[name="academic_period"]').required = false;
      document.querySelector(
        'select[name="academic_period_cut_filter"]'
      ).required = false;
      document.querySelector(
        'select[name="academic_period_cut"]'
      ).required = false;
      document.querySelector('input[name="email_student"]').required = true;

      document.getElementById("email_parent_container").style.display = "flex";
    } else {
      
      if (selectedOption == "alliances") {
        document.querySelector('input[name="type"]').value = "3";
      } else if (selectedOption == "institutes") {
        document.querySelector('input[name="type"]').value = "4";
      }

      const formOthers = document.getElementById("by_group");
      formOthers.style.display = "none";
      document.querySelector('select[name="academic_period"]').required = false;
      document.querySelector(
        'select[name="academic_period_cut_filter"]'
      ).required = false;
      document.querySelector(
        'select[name="academic_period_cut"]'
      ).required = false;
      document.querySelector('input[name="email_student"]').required = true;

      const formMe = document.getElementById("by_email");
      formMe.style.display = "none";
      document.querySelector('input[name="email_student"]').required = false;

      document.getElementById("email_parent_container").style.display = "none";
    }
  });
});

var modalCloseElementSummaryEmail = document.querySelectorAll(
  "#summary-email-exit-icon, #summary-email-exit-button"
);
if (modalCloseElementSummaryEmail) {
  modalCloseElementSummaryEmail.forEach(function (element) {
    element.addEventListener("click", function () {
      document.getElementById("list-students-email").innerHTML = "";
      document.getElementById("summary-email-modal").style.display = "none";
    });
  });
}

var summaryEmailButton = document.getElementById("summary-email");
if (summaryEmailButton) {
  summaryEmailButton.addEventListener("click", function () {
    document.getElementById("summary-email-button").disabled = true;
    document.getElementById("summary-email-modal").style.display = "block";
    document.getElementById("list-students-email").innerHTML = "";
    var studentsSend = document.getElementById("list-students-email");
    var li = document.createElement("li");
    li.innerText = `Loading...`;
    studentsSend.appendChild(li);
    var total_send = document.getElementById("total-send");
    total_send.innerText = 0;

    let academic_period = document.querySelector(
      'select[name="academic_period"]'
    ).value;
    let graduating_students = document.querySelector(
      'select[name="graduating_students"]'
    ).value;
    let cut = document.querySelector(
      'select[name="academic_period_cut"]'
    ).value;
    let filter = document.querySelector(
      'select[name="academic_period_cut_filter"]'
    ).value;
    let email_student = document.querySelector(
      'input[name="email_student"]'
    ).value;
    let type = document.querySelector('input[name="type"]').value;

    const XHR = new XMLHttpRequest();
    XHR.open("POST", summary_email.url, true);
    XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    XHR.responseType = "json";
    XHR.send(
      `action=${summary_email.action}&academic_period=${academic_period}&cut=${cut}&filter=${filter}&email_student=${email_student}&type=${type}&graduating_students=${graduating_students}`
    );
    XHR.onload = function () {
      if (this.readyState == "4" && XHR.status === 200) {
        document.getElementById("list-students-email").innerHTML = "";
        let students = XHR.response.students;
        students.forEach((student) => {
          var li = document.createElement("li");
          if(type == 1 || type == 2) {
            li.innerText = `${student.last_name} ${student.middle_last_name ? student.middle_last_name : ''} ${student.name} ${student.middle_name ? student.middle_name : ''} (${student.email})`;
          } else if(type == 3) {
            li.innerText = `${student.last_name} ${student.name} (${student.email})`;
          } else if(type == 4) {
            li.innerText = `${student.name} (${student.email})`;
          }
          studentsSend.appendChild(li);
        });

        if (students.length == 0) {
          var li = document.createElement("li");
          li.innerText = `No users found.`;
          studentsSend.appendChild(li);
          document.getElementById("summary-email-button").disabled = true;
        } else {
          document.getElementById("summary-email-button").disabled = false;
        }

        var total_send = document.getElementById("total-send");
        total_send.innerText = students.length;
      }
    };
  });
}

var summaryEmailButtonConfirm = document.getElementById("summary-email-button");
if (summaryEmailButtonConfirm) {
  summaryEmailButtonConfirm.addEventListener("click", function () {
    document.getElementById("list-students-email").innerHTML = "";
    document.getElementById("summary-email-modal").style.display = "none";
    document.getElementById("summary-email-send").click();
  });
}

// Dynamic loading of cuts when academic period changes (replicates behavior from document.js)
document.addEventListener("DOMContentLoaded", function () {
  let selectorAcademicPeriod = document.querySelector("select[name=academic_period]");
  let selectorCuts = document.querySelector("select[name=academic_period_cut]");

  if (!selectorCuts) return;

  const initialCut = selectorCuts.dataset.initialCut;
  const textoption = selectorCuts.dataset.textoption;

  if (selectorAcademicPeriod && selectorCuts) {
    selectorAcademicPeriod.addEventListener("change", function (e) {
      const XHR = new XMLHttpRequest();
      XHR.open("POST", summary_email.url, true);
      XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      XHR.responseType = "json";
      XHR.send("action=load_cuts_period&period=" + e.target.value);
      XHR.onload = function () {
        if (this.readyState == "4" && XHR.status === 200) {
          let cuts = this.response;

          // Limpia el select de cortes
          selectorCuts.innerHTML = "";

          // Opción por defecto
          let defaultOption = document.createElement("option");
          defaultOption.value = "";
          defaultOption.text = textoption ?? "Select term to filter";
          if (initialCut === "nocut" || initialCut === "out" || initialCut === "") {
            defaultOption.selected = true;
          }
          selectorCuts.appendChild(defaultOption);

          // Agrega las nuevas opciones
          cuts.forEach((cut) => {
            let option = document.createElement("option");
            option.value = cut.cut;
            option.text = cut.cut;
            if (option.value === initialCut) {
              option.selected = true;
            }
            selectorCuts.appendChild(option);
          });
        }
      };
    });
    // Inicialización: si ya hay un academic_period seleccionado, disparar carga; si no, mantener solo la opción por defecto
    if (selectorAcademicPeriod.value && selectorAcademicPeriod.value.trim() !== "") {
      // dispara el evento para cargar los cortes del periodo seleccionado
      selectorAcademicPeriod.dispatchEvent(new Event('change'));
    } else {
      // asegurar que solo exista la opción por defecto cuando no hay periodo seleccionado
      selectorCuts.innerHTML = '';
      let defaultOptionInit = document.createElement('option');
      defaultOptionInit.value = '';
      defaultOptionInit.text = textoption ?? 'Select term to filter';
      defaultOptionInit.selected = true;
      selectorCuts.appendChild(defaultOptionInit);
    }
  }
});
