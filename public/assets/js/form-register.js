document.addEventListener("DOMContentLoaded", function () {
  let subprograms_arr = [];
  let grades_country_arr = [];
  let grades_institute = [];
  let grades_default = [];
  const grade = document.getElementById("grade");
  const program = document.getElementById("program");
  const career = document.getElementById("career");
  const mention = document.getElementById("mention");
  const plan = document.getElementById("plan");
  const not_institute = document.getElementById("institute_id");
  const not_institute_others = document.getElementById("institute_id_others");
  const productIdInput = document.getElementById("product_id_input");
  const programIdShortcode = document.getElementById("program_shortcode");
  const careerIdShortcode = document.getElementById("career_shortcode");
  const mentionIdShortcode = document.getElementById("mention_shortcode");
  const planIdShortcode = document.getElementById("plan_shortcode");

  loadGradesDefault();
  function loadGradesDefault() {
    const XHR = new XMLHttpRequest();
    XHR.open(
      "POST",
      `${ajax_object.ajax_url}?action=load_grades_institute`,
      true
    );
    XHR.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    XHR.responseType = "json";

    const params = new URLSearchParams({
      action: "load_grades_institute",
      institute_id: null,
    });

    XHR.onload = () => {
      if (XHR.status === 200 && XHR.response && XHR.response.data) {
        grades_default = XHR.response.data.default_grades;
        // grades_institute = XHR.response.data.institute_grades;
      }
    };

    XHR.send(params.toString());
  }

  if (document.getElementById("birth_date")) {
    flatpickr(document.getElementById("birth_date"), {
      dateFormat: "m/d/Y",
      maxDate: "today",
      disableMobile: "true",
    });
  }

  if (not_institute) {
    not_institute.addEventListener("change", (e) => {
      grades_institute = [];
      const gradeSelect = document.querySelector('select[name="grade"]');
      gradeSelect.value = "";

      // Clear existing options
      while (gradeSelect.options.length > 1) {
        gradeSelect.remove(1);
      }
      // Hide grade select initially
      document.getElementById("grade_select").style.display = "none";
      document.getElementById("grade").required = false;

      if (e.target.value == "other") {
        document.getElementById("name-institute-field").style.display = "block";
        document.getElementById("name_institute").required = true;
        document.getElementById("institute_id").required = false;
        document.getElementById("institute_id_required").textContent = "";

      document.getElementById("grade_select").style.display = "block";
      document.getElementById("grade").required = true;

      subprograms_arr.forEach((subprogram, index) => {
        const optionText = getOptionText(index, subprogram);
        const option = createOption(optionText, index + 1);
        gradeSelect.appendChild(option);
      });
      } else {
        document.getElementById("name-institute-field").style.display = "none";
        document.getElementById("name_institute").required = false;
        document.getElementById("institute_id").required = true;
        document.getElementById("institute_id_required").textContent = "*";

        // Proceed with AJAX call only if an institute is actually selected (not "other" or empty)
        if (e.target.value) {
          const XHR = new XMLHttpRequest();
          XHR.open(
            "POST",
            `${ajax_object.ajax_url}?action=load_grades_institute`,
            true
          );
          XHR.setRequestHeader(
            "Content-Type",
            "application/x-www-form-urlencoded"
          );
          XHR.responseType = "json";

          const params = new URLSearchParams({
            action: "load_grades_institute",
            institute_id: e.target.value,
          });

          XHR.onload = () => {
            if (XHR.status === 200 && XHR.response && XHR.response.data) {
              // grades_default = XHR.response.data.default_grades;
              grades_institute = XHR.response.data.institute_grades;

              document.getElementById("grade_select").style.display = "block";
              document.getElementById("grade").required = true;

              subprograms_arr.forEach((subprogram, index) => {
                const optionText = getOptionText(index, subprogram);
                const option = createOption(optionText, index + 1);
                gradeSelect.appendChild(option);
              });
            }
          };

          XHR.send(params.toString());
        }
      }
    });
  }

  if (program) {
    program.addEventListener("change", async (e) => {
      document.getElementById("institute_id").value = "";
      document.getElementById("institute-id-select").style.display = "none";
      document.getElementById("name-institute-field").style.display = "none";
      document.getElementById("grade_select").style.display = "none";

      // Limpiar y ocultar el select de planes de pago en cada cambio
      const planElement = document.getElementById("plan");
      const planSelectContainer = document.getElementById("plans_select");
      if (planElement && planElement.tagName === "SELECT") {
        while (planElement.options.length > 1) {
          planElement.remove(1);
        }
        planSelectContainer.style.display = "none";
        planElement.required = false;
      }

      const programIdentificator = e.target.value;
      const careerSelectContainer = document.getElementById("careers_select");
      const careerElement = document.getElementById("career");

      // Lógica para limpiar el select de carreras si es un <select>
      if (careerElement && careerElement.tagName === "SELECT") {
        while (careerElement.options.length > 1) {
          careerElement.remove(1);
        }
        careerSelectContainer.style.display = "none";
        careerElement.required = false;
      }

      if (!programIdentificator) {
        return;
      }

      const isCareerSelect =
        careerElement && careerElement.tagName === "SELECT";
      const isCareerShortcodeValid =
        careerIdShortcode && careerIdShortcode.value;
      const isPlanSelect = planElement && planElement.tagName === "SELECT";
      const isPlanShortcodeValid = planIdShortcode && planIdShortcode.value;

      // Si el shortcode de la carrera existe y no es un select, no se hace nada.
      // if (isCareerShortcodeValid && !isCareerSelect) {
      //     return;
      // }

      // Si el shortcode del plan existe y no es un select, no se hace nada.
      // if (
      //   isPlanShortcodeValid &&
      //   !isPlanSelect &&
      //   isCareerShortcodeValid &&
      //   !isCareerSelect
      // ) {
      //   return;
      // }

      const params = new URLSearchParams({
        action: "load_data_program",
        program_identificator: programIdentificator,
      });

      try {
        const response = await fetch(
          `${ajax_object.ajax_url}?${params.toString()}`,
          {
            method: "POST",
          }
        );

        if (!response.ok) {
          throw new Error("La respuesta de la red no fue exitosa.");
        }

        const data = await response.json();
        const careers = data.data.careers || [];
        const paymentPlans = data.data.payment_plans || []; // Nuevo dato del backend

        // --- Lógica para el select de Carreras ---
        if (isCareerSelect) {
          careerSelectContainer.style.display = "block";
          careerElement.required = true;

          careers.forEach((career) => {
            const option = document.createElement("option");
            option.value = career.identificator;
            option.textContent = career.name;
            careerElement.appendChild(option);
          });

          if (isCareerShortcodeValid) {
            careerElement.value = careerIdShortcode.value;
          }
        }

        // --- Lógica para el select de Planes de Pago ---
        if (isPlanSelect) {
          planSelectContainer.style.display = "block";
          planElement.required = true;

          paymentPlans.forEach((plan) => {
            const option = document.createElement("option");
            option.value = plan.identificator;
            option.textContent = plan.name;
            planElement.appendChild(option);
          });

          if (isPlanShortcodeValid) {
            planElement.value = planIdShortcode.value;
          }
        }
      } catch (error) {
        console.error("Error al cargar las carreras o planes de pago:", error);
      }
    });

    if (
      (program.value && program.value !== "") ||
      (programIdShortcode && programIdShortcode.value)
    ) {
      program.dispatchEvent(new Event("change"));
    }
  }

  if (career) {
    career.addEventListener("change", async (e) => {
      document.getElementById("institute_id").value = "";
      document.getElementById("institute-id-select").style.display = "none";
      document.getElementById("name-institute-field").style.display = "none";
      document.getElementById("grade_select").style.display = "none";

      const careerId = e.target.value;
      const mentionSelectContainer = document.getElementById("mentions_select");
      const mentionElement = document.getElementById("mention");

      // Limpiar el select de menciones si existe y es un <select>
      if (mentionElement && mentionElement.tagName === "SELECT") {
        while (mentionElement.options.length > 1) {
          mentionElement.remove(1);
        }
        mentionSelectContainer.style.display = "none";
        mentionElement.required = false;
      }

      if (!careerId) {
        return;
      }

      const isMentionSelect =
        mentionElement && mentionElement.tagName === "SELECT";
      const isMentionShortcodeValid =
        mentionIdShortcode && mentionIdShortcode.value;

      // Si el shortcode de la mención existe y no es un select, no se hace nada.
      if (isMentionShortcodeValid && !isMentionSelect) {
        return;
      }

      // Si el shortcode de la mención no existe o es un select, procedemos a cargar.
      const params = new URLSearchParams({
        action: "load_mentions_by_career",
        career_identificator: careerId,
      });

      try {
        const response = await fetch(
          `${ajax_object.ajax_url}?${params.toString()}`,
          {
            method: "POST",
          }
        );

        if (!response.ok) {
          throw new Error("La respuesta de la red no fue exitosa.");
        }

        const data = await response.json();
        const mentions = data.data.mentions || [];

        if (isMentionSelect) {
          mentionSelectContainer.style.display = "block";
          mentionElement.required = true;

          mentions.forEach((mention) => {
            const option = document.createElement("option");
            option.value = mention.identificator;
            option.textContent = mention.name;
            mentionElement.appendChild(option);
          });

          // Si hay un shortcode válido, seleccionamos la opción correspondiente
          if (isMentionShortcodeValid) {
            mentionElement.value = mentionIdShortcode.value;
          }
        }
      } catch (error) {
        console.error("Error al cargar las menciones:", error);
      }
    });

    if (careerIdShortcode && careerIdShortcode.value) {
      career.dispatchEvent(new Event("change"));
    }
  }

  if (mention) {
    mention.addEventListener("change", (e) => {
      document.getElementById("institute_id").value = "";
      document.getElementById("institute-id-select").style.display = "none";
      document.getElementById("name-institute-field").style.display = "none";
      document.getElementById("grade_select").style.display = "none";

      const numberOfOptions =
        document.getElementById("institute_id").options.length;
      if (numberOfOptions == 3) {
        document.getElementById("institute_id").required = false;
        document.getElementById("institute-id-select").style.display = "none";
        document.getElementById("institute_id").selectedIndex = 1;
        document
          .getElementById("institute_id")
          .dispatchEvent(new Event("change"));
      } else {
        document.getElementById("institute_id").required = true;
        document.getElementById("institute-id-select").style.display = "block";
      }
    });

    if (mentionIdShortcode && mentionIdShortcode.value) {
      mention.dispatchEvent(new Event("change"));
    }
  }

  // copiado de career
  function createOption(text, value) {
    const option = document.createElement("option");
    option.textContent = text;
    option.value = value;
    return option;
  }

  /**
   * Determina el texto de una opción de select basándose en la jerarquía de prioridades.
   * @param {number} index El índice del elemento en los arrays de grados.
   * @param {Object} subprogram El objeto subprograma actual.
   * @returns {string} El texto final para la opción.
   */
  function getOptionText(index, subprogram) {
    // Lógica de prioridad: grades_institute > grades_country_arr > grades_default
    let gradeData = null;

    if (grades_institute && grades_institute[index]) {
      gradeData = grades_institute[index];
    } else if (grades_country_arr && grades_country_arr[index]) {
      gradeData = grades_country_arr[index];
    }
    // } else if (grades_default && grades_default[index]) {
    //   gradeData = grades_default[index];
    // }

    // Formatear el texto de la opción si se encontró un grado.
    if (gradeData) {
      if (
        typeof gradeData === "object" &&
        gradeData !== null &&
        gradeData.name
      ) {
        return gradeData.description
          ? `${gradeData.name} ${gradeData.description}`
          : gradeData.name;
      } else {
        // Si el dato es un string simple, usarlo directamente.
        return gradeData;
      }
    }

    // Fallback: Si no se encontró ningún grado, usar el nombre del subprograma.
    return subprogram.name || "N/A";
  }

  if (plan) {
    plan.addEventListener("change", async (e) => {
      const gradeSelect = document.querySelector('select[name="grade"]');
      const gradeSelectContainer = document.getElementById("grade_select");
      const gradeInput = document.getElementById("grade");
      const instituteInputValue = document.getElementById("institute_id");
      const planId = e.target.value;

      // Lógica para limpiar y salir temprano
      gradeSelect.value = "";
      while (gradeSelect.options.length > 1) {
        gradeSelect.remove(1);
      }

      if (!planId) {
        gradeSelectContainer.style.display = "none";
        gradeInput.required = false;
        return;
      }

      try {
        const params = new URLSearchParams({
          action: "load_subprograms_by_program",
          program_id: planId,
        });

        const response = await fetch(
          `${ajax_object.ajax_url}?${params.toString()}`,
          {
            method: "POST",
          }
        );

        if (!response.ok) {
          throw new Error("La respuesta de la red no fue exitosa.");
        }

        const data = await response.json();
        subprograms_arr = data.data.subprograms || [];

        const shouldShowGradeSelect =
          subprograms_arr.length > 0 && instituteInputValue.value === "other";

        if (shouldShowGradeSelect) {
          gradeSelectContainer.style.display = "block";
          gradeInput.required = true;

          subprograms_arr.forEach((subprogram, index) => {
            const optionText = getOptionText(index, subprogram);
            const option = createOption(optionText, index + 1);
            gradeSelect.appendChild(option);
          });
        } else {
          gradeSelectContainer.style.display = "none";
          gradeInput.required = false;
        }
      } catch (error) {
        console.error("Error al cargar las menciones:", error);
        // Opcionalmente, mostrar un mensaje de error al usuario
      }
    });

    if (planIdShortcode && planIdShortcode.value) {
      plan.dispatchEvent(new Event("change"));
    }
  }

  const countrySelect = document.getElementById("country-select");
  const instituteSelect = document.getElementById("institute_id");
  if (countrySelect && instituteSelect) {
    countrySelect.addEventListener("change", function (e) {
      const XHR = new XMLHttpRequest();
      XHR.open(
        "POST",
        `${ajax_object.ajax_url}?action=load_grades_by_country`,
        true
      );
      XHR.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      XHR.responseType = "json";

      const params = new URLSearchParams({
        action: "load_grades_by_country",
        country: e.target.value,
      });

      XHR.onload = () => {
        if (XHR.status === 200 && XHR.response && XHR.response.data) {
          grades_country_arr = XHR.response.data.grades ?? [];
        }
      };

      XHR.send(params.toString());

      if (document.getElementById("institute_id")) {
        document.getElementById("institute_id").value = "";
      }
      if (document.getElementById("name_institute")) {
        document.getElementById("name_institute").value = "";
      }
      if (document.getElementById("name-institute-field")) {
        document.getElementById("name-institute-field").style.display = "none";
      }
      if (document.getElementById("name_institute")) {
        document.getElementById("name_institute").required = false;
      }
      if (document.getElementById("institute_id")) {
        document.getElementById("institute_id").required = true;
      }
      if (document.getElementById("institute_id_required")) {
        document.getElementById("institute_id_required").textContent = "*";
      }

      const selectedCountry = countrySelect.value;
      const options = instituteSelect.options;
      for (let i = 0; i < options.length; i++) {
        const option = options[i];
        if (option.dataset.others == "0") {
          if (
            option.dataset.country === selectedCountry ||
            option.value === ""
          ) {
            option.style.display = "block";
          } else {
            option.style.display = "none";
          }
        }
      }

      program.dispatchEvent(new Event("change"));
      career.dispatchEvent(new Event("change"));
      mention.dispatchEvent(new Event("change"));
      plan.dispatchEvent(new Event("change"));
    });
  }

  if (grade) {
    grade.addEventListener("change", (e) => {
      // Subtract 1 because option values are index + 1
      const selectedIndex = parseInt(e.target.value, 10) - 1;

      if (productIdInput) {
        if (selectedIndex >= 0 && selectedIndex < subprograms_arr.length) {
          const selectedProgram = subprograms_arr[selectedIndex];

          if (selectedProgram && selectedProgram.product_id !== undefined) {
            productIdInput.value = selectedProgram.product_id;
          } else {
            productIdInput.value = "";
          }
        } else {
          productIdInput.value = "";
        }
      }
    });
  }

  if (not_institute_others) {
    not_institute_others.addEventListener("change", (e) => {
      if (e.target.value == "other") {
        document.getElementById("name-institute-field-others").style.display =
          "block";
        document.getElementById("name_institute_others").required = true;
        document.getElementById("institute_id_others").required = false;
        document.getElementById("institute_id_required").textContent = "";
      } else {
        document.getElementById("name-institute-field-others").style.display =
          "none";
        document.getElementById("name_institute_others").required = false;
        document.getElementById("institute_id_required_others").required = true;
        document.getElementById("institute_id_required").textContent = "*";
      }
    });
  }

  const crm_id = getUrlParameter("crm_id");
  if (crm_id) {
    // 1. Configurar solicitud
    let token = document.getElementById("x-api-key").value;
    let url = document.getElementById("x-api-url").value;
    let api = document.getElementById("x-api").value;

    const XHR = new XMLHttpRequest();
    const endpoint = `${url}${api}/${crm_id}`;

    XHR.open("GET", endpoint, true);
    XHR.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    XHR.setRequestHeader("x-api-key", token);
    XHR.responseType = "json";

    // 4. Manejar respuesta
    XHR.onload = () => {
      // 5. Manejar errores HTTP
      if (XHR.status !== 200) {
        return;
      }

      let custom = XHR.response.entity.custom;
      let parent = XHR.response.entity.personal;

      //setamos valores
      document.getElementById("agent_name").value = parent.firstName;
      document.getElementById("agent_last_name").value = parent.lastName;
      document.getElementById("number_partner").value = parent.phoneNumber;
      document.getElementById("email_partner").value = parent.email;
      document.getElementById("email_partner").readonly = true;

      document.getElementById("name_student").value =
        custom.cf_contact_nombre_del_alumno_znku_text;
      document.getElementById("lastname_student").value =
        custom.cf_contact_apellido_alumno_zgxz_text;
    };

    // 11. Enviar solicitud
    XHR.send();
  }

  if (document.getElementById("billing_first_name")) {
    document
      .getElementById("billing_first_name")
      .addEventListener("input", (e) => {
        if (!getCookie("is_older")) {
          return;
        }

        if (!getCookie("name_student")) {
          return;
        }

        value = e.target.value;
        setCookie("name_student", value);
      });
  }

  if (document.getElementById("billing_last_name")) {
    document
      .getElementById("billing_last_name")
      .addEventListener("input", (e) => {
        if (!getCookie("is_older")) {
          return;
        }

        if (!getCookie("last_name_student")) {
          return;
        }

        value = e.target.value;
        setCookie("last_name_student", value);
      });
  }

  if (document.getElementById("billing_country")) {
    jQuery(function ($) {
      $(document.body).on(
        "change",
        "select[name=billing_country]",
        function () {
          if (!getCookie("is_older")) {
            return;
          }

          if (!getCookie("billing_country")) {
            return;
          }

          value = $(this).val();
          setCookie("billing_country", value);
        }
      );
    });
  }

  if (document.getElementById("billing_city")) {
    document.getElementById("billing_city").addEventListener("input", (e) => {
      if (!getCookie("is_older")) {
        return;
      }

      if (!getCookie("billing_city")) {
        return;
      }

      value = e.target.value;
      setCookie("billing_city", value);
    });
  }

  if (document.getElementById("billing_phone_hidden")) {
    document.getElementById("billing_phone").addEventListener("input", (e) => {
      if (!getCookie("is_older")) {
        return;
      }

      setTimeout(() => {
        value = document.getElementById("billing_phone_hidden").value;
        setCookie("phone_student", value);
      }, 1000);
    });
  }

  if (document.getElementById("billing_email")) {
    document.getElementById("billing_email").addEventListener("input", (e) => {
      if (!getCookie("is_older")) {
        return;
      }

      if (!getCookie("email_student")) {
        return;
      }

      value = e.target.value;
      setCookie("email_student", value);
    });
  }
  const fileInputs = document.querySelectorAll(".custom-file-input");
  const fileLabels = document.querySelectorAll(".custom-file-label");

  fileInputs.forEach((fileInput, index) => {
    fileInput.addEventListener("change", () => {
      // Obtener los tipos permitidos desde el atributo data-fileallowed
      const allowedExtensions = fileInput
        .getAttribute("data-fileallowed")
        .split(",")
        .map((ext) => ext.trim());

      // Mapa de extensiones a tipos MIME
      const extensionToMime = {
        ".pdf": "application/pdf",
        ".jpeg": "image/jpeg",
        ".jpg": "image/jpeg",
        ".png": "image/png",
      };

      // Crear un array de tipos MIME permitidos
      const allowedTypes = allowedExtensions
        .map((ext) => extensionToMime[ext])
        .filter(Boolean);

      if (!allowedTypes.includes(fileInput.files[0]?.type)) {
        alert("Only allowed file types: " + allowedExtensions.join(", "));
        fileInput.value = "";
        fileLabels[index].textContent = "Select file";
      } else {
        const fileName = fileInput.files[0].name;
        fileLabels[index].textContent = fileName ? fileName : "Select file";
      }
    });
  });

  let modalContinueCheckout = document.getElementById(
    "modal-continue-checkout"
  );
  if (modalContinueCheckout) {
    let formParam = getUrlParameter("form");
    if (
      (getCookie("name_student") &&
        getCookie("last_name_student") &&
        getCookie("birth_date") &&
        getCookie("initial_grade") &&
        getCookie("program_id") &&
        getCookie("email_partner") &&
        getCookie("number_partner")) ||
      formParam === "1"
    ) {
      if (formParam == 1) {
        document.querySelector("input[name=birth_date_student]").value =
          getCookie("birth_date");
        document.querySelector("select[name=document_type]").value =
          getCookie("document_type");
        document.querySelector("input[name=id_document]").value =
          getCookie("id_document");
        document.querySelector("input[name=name_student]").value =
          getCookie("name_student");
        document.querySelector("input[name=middle_name_student]").value =
          getCookie("middle_name_student");
        document.querySelector("input[name=lastname_student]").value =
          getCookie("last_name_student");
        document.querySelector("input[name=middle_last_name_student]").value =
          getCookie("middle_last_name_student");
        document.querySelector("input[name=number_phone]").value =
          getCookie("phone_student");
        document.querySelector("input[name=email_student]").value =
          getCookie("email_student");
        document.querySelector("select[name=gender]").value =
          getCookie("gender");
        document.querySelector("select[name=etnia]").value =
          getCookie("ethnicity");
        document.querySelector("input[name=birth_date_parent]").value =
          getCookie("birth_date_parent");
        document.querySelector("select[name=parent_document_type]").value =
          getCookie("parent_document_type");
        document.querySelector("input[name=id_document_parent]").value =
          getCookie("id_document_parent");
        document.querySelector("input[name=agent_name]").value =
          getCookie("agent_name");
        document.querySelector("input[name=agent_last_name]").value =
          getCookie("agent_last_name");
        document.querySelector("input[name=number_partner]").value =
          getCookie("number_partner");
        document.querySelector("select[name=gender_parent]").value =
          getCookie("gender_parent");
        document.querySelector("input[name=email_partner]").value =
          getCookie("email_partner");
        document.querySelector("input[name=password]").value =
          getCookie("password");
        document.querySelector("select[name=grade]").value =
          getCookie("initial_grade");
        document.querySelector("select[name=program]").value =
          getCookie("program_id");
        document.querySelector("select[name=institute_id]").value =
          getCookie("institute_id");
      } else {
        modalContinueCheckout.style.display = "block";
      }
    }
  }
});

let timer = null;
let form = document.querySelector(".form-aes");
let buttonSave = document.querySelector("#buttonsave");
let sameEmailStudent = document.querySelector("#sameemailstudent");
let sameEmailParent = document.querySelector("#sameemailparent");
let existStudentEmail = document.querySelector("#existstudentemail");
let existParentEmail = document.querySelector("#existparentemail");
let existStudentId = document.querySelector("#exisstudentid");
let emailStudentInput = form?.querySelector('input[name="email_student"]');
let emailPartnerInput = form?.querySelector('input[name="email_partner"]');
let idDocument = form?.querySelector('input[name="id_document"]');
let idDocumentParent = form?.querySelector('input[name="id_document_parent"]');
let typeDocument = form?.querySelector('select[name="document_type"]');
let typeDocumentParent = form?.querySelector(
  'select[name="parent_document_type"]'
);
let dont_allow_adult = document.getElementById("dont_allow_adult");
let dontBeAdult = document.querySelector("#dontBeAdult");

emailStudentInput?.addEventListener("input", checkEmails);
emailPartnerInput?.addEventListener("input", checkEmails);

function validateIDs(validating = true) {
  if (
    typeDocument.value &&
    typeDocumentParent.value &&
    idDocument.value &&
    idDocumentParent.value
  ) {
    if (
      typeDocument.value != typeDocumentParent.value ||
      idDocument.value != idDocumentParent.value
    ) {
      let samestudentsids = document.querySelectorAll(".sameids");
      samestudentsids.forEach((element) => {
        element.style.display = "none";
      });
      if (!validating) {
        buttonSave.disabled = false;
      }
      return true;
    }

    let samestudentsids = document.querySelectorAll(".sameids");
    samestudentsids.forEach((element) => {
      element.style.display = "block";
    });
    if (!validating) {
      buttonSave.disabled = true;
    }
    return false;
  }

  return true;
}

function checkEmails() {
  const emailStudent = emailStudentInput?.value;
  const emailPartner = emailPartnerInput?.value;
  if (
    emailStudent === emailPartner &&
    emailPartner != "" &&
    emailPartner != ""
  ) {
    buttonSave.disabled = true;
    if (sameEmailStudent) {
      sameEmailStudent.style.display = "block";
    }

    if (sameEmailParent) {
      sameEmailParent.style.display = "block";
    }
  } else {
    if (sameEmailStudent) {
      sameEmailStudent.style.display = "none";
    }

    if (sameEmailParent) {
      sameEmailParent.style.display = "none";
    }
    if (dont_allow_adult && dont_allow_adult?.value == 1) {
      if (
        existStudentEmail?.style.display === "none" &&
        existStudentId?.style.display === "none" &&
        sameEmailStudent?.style.display === "none" &&
        dontBeAdult?.style.display === "none"
      ) {
        buttonSave.disabled = false;
      }
    } else {
      if (
        existParentEmail?.style.display === "none" &&
        existStudentEmail?.style.display === "none" &&
        existStudentId?.style.display === "none" &&
        sameEmailParent?.style.display === "none" &&
        sameEmailStudent?.style.display === "none"
      ) {
        buttonSave.disabled = false;
      }
    }
  }
}

function checkScholarship(scholarship = 0) {
  // if (timer) {
  //   clearTimeout(timer);
  // }
  // timer = setTimeout(() => {
  //   if (idDocument.value && typeDocument.value) {
  //     sendAjax(
  //       "action=check_scholarship",
  //       idDocument.value,
  //       1,
  //       typeDocument.value,
  //       scholarship
  //     );
  //   }
  // }, 1000);
}

const urlParams = new URLSearchParams(window.location.search);
const id = urlParams.get("id");
const type = urlParams.get("type");

if (id && type) {
  const typeSelect = document.querySelector("select[name=document_type]");
  const idInput = document.querySelector("input[name=id_document]");

  // Asignar valores
  typeSelect.value = type;
  idInput.value = id;

  // Disparar eventos input manualmente
  // typeSelect.dispatchEvent(new Event('input'));
  idInput.dispatchEvent(new Event("input"));
}

function sendAjaxIdDocument(scholarship = 0) {
  if (timer) {
    clearTimeout(timer);
  }
  timer = setTimeout(() => {
    if (idDocument.value && typeDocument.value) {
      sendAjax(
        "action=exist_user_id",
        idDocument.value,
        1,
        typeDocument.value,
        scholarship
      );
    }
  }, 1000);
}

function sendAjaxPartnerEmailDocument(scholarship = 0) {
  if (timer) {
    clearTimeout(timer);
  }
  timer = setTimeout(() => {
    if (emailPartnerInput?.value) {
      sendAjax(
        "action=exist_user_email",
        emailPartnerInput?.value,
        2,
        null,
        scholarship
      );
    }
  }, 1000);
}

function sendAjaxStudentEmailDocument(scholarship = 0) {
  if (timer) {
    clearTimeout(timer);
  }
  timer = setTimeout(() => {
    if (emailStudentInput.value) {
      sendAjax(
        "action=exist_user_email",
        emailStudentInput.value,
        3,
        null,
        scholarship
      );
    }
  }, 1000);
}

function sendAjax(action, value, input, second_value = null, scholarship = 0) {
  const XHR = new XMLHttpRequest();
  XHR.open("POST", `${ajax_object.ajax_url}?${action}`, true);
  XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  XHR.responseType = "text";
  let params = `${action}&option=${value}`;
  if (second_value) {
    params += `&type=${second_value}`;
  }
  if (scholarship) {
    params += `&scholarship=${scholarship}`;
  }
  XHR.send(params);
  XHR.onload = function () {
    if (XHR.status === 200) {
      if (action === "action=exist_user_id") {
        if (XHR.response === "0") {
          existStudentId.style.display = "none";
          if (dont_allow_adult && dont_allow_adult?.value == 1) {
            if (
              existStudentEmail?.style.display === "none" &&
              existStudentId?.style.display === "none" &&
              sameEmailStudent?.style.display === "none" &&
              validateIDs() &&
              dontBeAdult?.style.display === "none"
            ) {
              buttonSave.disabled = false;
            }
          } else {
            if (
              existParentEmail?.style.display === "none" &&
              existStudentEmail?.style.display === "none" &&
              existStudentId?.style.display === "none" &&
              validateIDs() &&
              sameEmailParent?.style.display === "none" &&
              sameEmailStudent?.style.display === "none"
            ) {
              buttonSave.disabled = false;
            }
          }
        } else {
          existStudentId.style.display = "block";
          buttonSave.disabled = true;
        }
      } else if (action === "action=exist_user_email" && input === 2) {
        if (XHR.response === "0") {
          existParentEmail.style.display = "none";
          if (dont_allow_adult && dont_allow_adult?.value == 1) {
            if (
              existStudentEmail?.style.display === "none" &&
              existStudentId?.style.display === "none" &&
              validateIDs() &&
              sameEmailStudent?.style.display === "none" &&
              dontBeAdult?.style.display === "none"
            ) {
              buttonSave.disabled = false;
            }
          } else {
            if (
              existParentEmail?.style.display === "none" &&
              existStudentEmail?.style.display === "none" &&
              validateIDs() &&
              existStudentId?.style.display === "none" &&
              sameEmailParent?.style.display === "none" &&
              sameEmailStudent?.style.display === "none"
            ) {
              buttonSave.disabled = false;
            }
          }
        } else {
          existParentEmail.style.display = "block";
          buttonSave.disabled = true;
        }
      } else if (action === "action=exist_user_email" && input === 3) {
        if (XHR.response === "0") {
          existStudentEmail.style.display = "none";
          if (dont_allow_adult && dont_allow_adult?.value == 1) {
            if (
              existStudentEmail?.style.display === "none" &&
              existStudentId?.style.display === "none" &&
              validateIDs() &&
              sameEmailStudent?.style.display === "none" &&
              dontBeAdult?.style.display === "none"
            ) {
              buttonSave.disabled = false;
            }
          } else {
            if (
              existParentEmail?.style.display === "none" &&
              existStudentEmail?.style.display === "none" &&
              existStudentId?.style.display === "none" &&
              validateIDs() &&
              sameEmailParent?.style.display === "none" &&
              sameEmailStudent?.style.display === "none"
            ) {
              buttonSave.disabled = false;
            }
          }
        } else {
          existStudentEmail.style.display = "block";
          buttonSave.disabled = true;
        }
      } else if (action === "action=check_scholarship") {
        document.getElementById("scholarship_assigned").style.display = "block";
        if (XHR.response === "0") {
          document.getElementById(
            "scholarship_assigned"
          ).innerText = `No scholarship assigned or already signed`;
          document.getElementById("scholarship_assigned").style.color = "gray";

          changeFieldsDisabled(true);
        } else {
          document.getElementById(
            "scholarship_assigned"
          ).innerHTML = `We have found that you have the following scholarship assigned to you: <strong>${XHR.response}</strong>`;
          document.getElementById("scholarship_assigned").style.color = "green";

          changeFieldsDisabled(false);
        }
      }
    }
  };
}

if (document.getElementById("birth_date_student")) {
  document
    .getElementById("birth_date_student")
    .addEventListener("change", (e) => {
      let max_age = parseInt(document.getElementById("max_age").value);
      let limit_age = parseInt(document.getElementById("limit_age").value);
      let date = e.target.value;
      date = date.split("/");

      const start = new Date(date[2], date[0] - 1, date[1]);
      const today = new Date();
      const diff = diff_years(today, start);

      if (diff > limit_age) {
        alert(`The maximum age is ${limit_age} years old.`);
        // Reset the input field
        e.target.value = "";
        return;
      }

      if (diff >= max_age) {
        var accessDataTitle = document.getElementById("access_data");
        if (accessDataTitle) {
          accessDataTitle.innerHTML = "Platform access data of student";
        }

        if (dont_allow_adult && dont_allow_adult?.value == 1) {
          if (dontBeAdult) {
            dontBeAdult.style.display = "block";
          }
          buttonSave.disabled = true;
        } else {
          if (dontBeAdult) {
            dontBeAdult.style.display = "none";
          }
          if (dont_allow_adult && dont_allow_adult?.value == 1) {
            if (
              existStudentEmail?.style.display === "none" &&
              existStudentId?.style.display === "none" &&
              sameEmailStudent?.style.display === "none" &&
              dontBeAdult?.style.display === "none"
            ) {
              buttonSave.disabled = false;
            }
          } else {
            if (
              existParentEmail?.style.display === "none" &&
              existStudentEmail?.style.display === "none" &&
              existStudentId?.style.display === "none" &&
              sameEmailParent?.style.display === "none" &&
              sameEmailStudent?.style.display === "none"
            ) {
              buttonSave.disabled = false;
            }
          }

          // Obtén el elemento div que contiene el input
          const studentEmailDiv = document.getElementById("student-email");

          if (studentEmailDiv) {
            // Crea una copia del elemento div
            const studentEmailDivClone = studentEmailDiv.cloneNode(true);
            document.getElementById("student-email-access").innerHTML = "";
            document.getElementById("student-email-detail").innerHTML = "";
            document
              .getElementById("student-email-access")
              .appendChild(studentEmailDivClone);
          }

          var parentTitle = document.getElementById("parent-title");
          if (parentTitle) {
            parentTitle.style.display = "none";
          }

          var parentBirthDateField = document.getElementById(
            "parent_birth_date_field"
          );
          if (parentBirthDateField) {
            parentBirthDateField.style.display = "none";
          }

          var parentDocumentTypeField = document.getElementById(
            "parent_document_type_field"
          );
          if (parentDocumentTypeField) {
            parentDocumentTypeField.style.display = "none";
          }

          var parentIdDocumentField = document.getElementById(
            "parent_id_document_field"
          );
          if (parentIdDocumentField) {
            parentIdDocumentField.style.display = "none";
          }

          var parentNameField = document.getElementById("parent_name_field");
          if (parentNameField) {
            parentNameField.style.display = "none";
          }

          var parentLastNameField = document.getElementById(
            "parent-lastname-field"
          );
          if (parentLastNameField) {
            parentLastNameField.style.display = "none";
          }

          var parentPhoneField = document.getElementById("parent-phone-field");
          if (parentPhoneField) {
            parentPhoneField.style.display = "none";
          }

          var parentEmailField = document.getElementById("parent-email-field");
          if (parentEmailField) {
            parentEmailField.style.display = "none";
          }

          var parentGenderField = document.getElementById(
            "parent-gender-field"
          );
          if (parentGenderField) {
            parentGenderField.style.display = "none";
          }

          var parentDocumentType = document.getElementById(
            "parent_document_type"
          );
          if (parentDocumentType) {
            parentDocumentType.required = false;
          }

          var birthDateParent = document.getElementById("birth_date_parent");
          if (birthDateParent) {
            birthDateParent.required = false;
          }

          var parentIdDocument = document.getElementById("id_document_parent");
          if (parentIdDocument) {
            parentIdDocument.required = false;
          }

          var agentName = document.getElementById("agent_name");
          if (agentName) {
            agentName.required = false;
          }

          var agentLastName = document.getElementById("agent_last_name");
          if (agentLastName) {
            agentLastName.required = false;
          }

          var numberPartner = document.getElementById("number_partner");
          if (numberPartner) {
            numberPartner.required = false;
          }

          var emailPartner = document.getElementById("email_partner");
          if (emailPartner) {
            emailPartner.required = false;
          }

          var parentGenderField = document.getElementById("gender_parent");
          if (parentGenderField) {
            parentGenderField.required = false;
          }
        }
      } else {
        var accessDataTitle = document.getElementById("access_data");
        if (accessDataTitle) {
          accessDataTitle.innerHTML = "Platform access data of parent";
        }

        if (dontBeAdult) {
          dontBeAdult.style.display = "none";
        }
        if (dont_allow_adult && dont_allow_adult?.value == 1) {
          if (
            existStudentEmail?.style.display === "none" &&
            existStudentId?.style.display === "none" &&
            sameEmailStudent?.style.display === "none" &&
            dontBeAdult?.style.display === "none"
          ) {
            buttonSave.disabled = false;
          }
        } else {
          if (
            existParentEmail?.style.display === "none" &&
            existStudentEmail?.style.display === "none" &&
            existStudentId?.style.display === "none" &&
            sameEmailParent?.style.display === "none" &&
            sameEmailStudent?.style.display === "none"
          ) {
            buttonSave.disabled = false;
          }
        }

        // Obtén el elemento div que contiene el input
        const studentEmailDiv = document.getElementById("student-email");

        if (studentEmailDiv) {
          // Crea una copia del elemento div
          const studentEmailDivClone = studentEmailDiv.cloneNode(true);
          document.getElementById("student-email-access").innerHTML = "";
          document.getElementById("student-email-detail").innerHTML = "";
          document
            .getElementById("student-email-detail")
            .appendChild(studentEmailDivClone);
        }

        var parentTitle = document.getElementById("parent-title");
        if (parentTitle) {
          parentTitle.style.display = "block";
        }

        var parentBirthDateField = document.getElementById(
          "parent_birth_date_field"
        );
        if (parentBirthDateField) {
          parentBirthDateField.style.display = "block";
        }

        var parentDocumentTypeField = document.getElementById(
          "parent_document_type_field"
        );
        if (parentDocumentTypeField) {
          parentDocumentTypeField.style.display = "block";
        }

        var parentIdDocumentField = document.getElementById(
          "parent_id_document_field"
        );
        if (parentIdDocumentField) {
          parentIdDocumentField.style.display = "block";
        }

        var parentNameField = document.getElementById("parent_name_field");
        if (parentNameField) {
          parentNameField.style.display = "block";
        }

        var parentLastNameField = document.getElementById(
          "parent-lastname-field"
        );
        if (parentLastNameField) {
          parentLastNameField.style.display = "block";
        }

        var parentPhoneField = document.getElementById("parent-phone-field");
        if (parentPhoneField) {
          parentPhoneField.style.display = "block";
        }

        var parentGenderField = document.getElementById("parent-gender-field");
        if (parentGenderField) {
          parentGenderField.style.display = "block";
        }

        var parentEmailField = document.getElementById("parent-email-field");
        if (parentEmailField) {
          parentEmailField.style.display = "block";
        }

        var parentDocumentType = document.getElementById(
          "parent_document_type"
        );
        if (parentDocumentType) {
          parentDocumentType.required = true;
        }

        var birthDateParent = document.getElementById("birth_date_parent");
        if (birthDateParent) {
          birthDateParent.required = true;
        }

        var parentIdDocument = document.getElementById("id_document_parent");
        if (parentIdDocument) {
          parentIdDocument.required = true;
        }

        var agentName = document.getElementById("agent_name");
        if (agentName) {
          agentName.required = true;
        }

        var agentLastName = document.getElementById("agent_last_name");
        if (agentLastName) {
          agentLastName.required = true;
        }

        var numberPartner = document.getElementById("number_partner");
        if (numberPartner) {
          numberPartner.required = true;
        }

        var emailPartner = document.getElementById("email_partner");
        if (emailPartner) {
          emailPartner.required = true;
        }

        var parentGenderField = document.getElementById("gender_parent");
        if (parentGenderField) {
          parentGenderField.required = true;
        }
      }
    });
}

function diff_years(dt2, dt1) {
  let diff = (dt2.getTime() - dt1.getTime()) / 1000;
  diff /= 60 * 60 * 24;
  return Math.abs(Math.floor(diff / 365.25)); // Trunca hacia abajo
  // return Math.abs(Math.ceil(diff / 365.25)); // Trunca hacia arriba
}

function setCookie(name, value) {
  var date = new Date();
  date.setTime(date.getTime() + 1 * 24 * 60 * 60 * 1000);
  expires = "; expires=" + date.toGMTString();
  document.cookie = name + "=" + value + expires + "; path=/";
}

const segmentButtons = document.querySelectorAll(".segment-button");

segmentButtons.forEach((button) => {
  button.addEventListener("click", (event) => {
    // Remove active class from all buttons
    segmentButtons.forEach((btn) => btn.classList.remove("active"));

    // Add active class to the clicked button
    event.target.classList.add("active");

    // Get the currently selected option
    const selectedOption = event.target.getAttribute("data-option");
    if (selectedOption == "others") {
      const formMe = document.getElementById("form-me");
      formMe.style.display = "none";

      const formOthers = document.getElementById("form-others");
      formOthers.style.display = "block";

      form = document.querySelector(".form-aes-others");
      buttonSave = document.querySelector("#buttonsave_others");
      sameEmailStudent = document.querySelector("#sameemailstudent");
      sameEmailParent = document.querySelector("#sameemailparent");
      existStudentEmail = document.querySelector("#existstudentemail");
      existParentEmail = document.querySelector("#existparentemail");
      existStudentId = document.querySelector("#exisstudentid");
      emailStudentInput = form?.querySelector('input[name="email_student"]');
      emailPartnerInput = form?.querySelector('input[name="email_partner"]');
      idDocument = form?.querySelector('input[name="id_document"]');
      typeDocument = form?.querySelector('select[name="document_type"]');
      dont_allow_adult = document.getElementById("dont_allow_adult");
      dontBeAdult = document.querySelector("#dontBeAdult");
    } else {
      const formOthers = document.getElementById("form-others");
      formOthers.style.display = "none";

      const formMe = document.getElementById("form-me");
      formMe.style.display = "block";

      form = document.querySelector(".form-aes");
      buttonSave = document.querySelector("#buttonsave");
      sameEmailStudent = document.querySelector("#sameemailstudent");
      sameEmailParent = document.querySelector("#sameemailparent");
      existStudentEmail = document.querySelector("#existstudentemail");
      existParentEmail = document.querySelector("#existparentemail");
      existStudentId = document.querySelector("#exisstudentid");
      emailStudentInput = form?.querySelector('input[name="email_student"]');
      emailPartnerInput = form?.querySelector('input[name="email_partner"]');
      idDocument = form?.querySelector('input[name="id_document"]');
      typeDocument = form?.querySelector('select[name="document_type"]');
      dont_allow_adult = document.getElementById("dont_allow_adult");
      dontBeAdult = document.querySelector("#dontBeAdult");
    }
  });
});

customFlatpickr();
function customFlatpickr() {
  let instances = flatpickr(".flatpickr", {
    dateFormat: "m/d/Y",
    disableMobile: "true",
    onChange: function (selectedDates, dateStr, instance) {
      let id = instance.input.id;
      let date = instance.input.value;
      let year_selected = document.getElementById(`instance${id}`).value;
      if (date && date != "") {
        let date_split = date.split("/");
        instance.setDate(`${date_split[0]}/${date_split[1]}/${year_selected}`);
      } else {
        let currentDate = new Date();
        let month = currentDate.getMonth(); // Mes actual (0-11)
        let day = currentDate.getDate(); // Día actual
        let newDate = new Date(year_selected, month, day);
        instance.setDate(newDate);
      }
    },
  });

  setTimeout(() => {
    document.querySelectorAll(".numInputWrapper").forEach(function (element) {
      element.style.display = "none";
    });

    document
      .querySelectorAll(".flatpickr-prev-month")
      .forEach(function (element) {
        element.style.display = "none";
      });

    document
      .querySelectorAll(".flatpickr-next-month")
      .forEach(function (element) {
        element.style.display = "none";
      });

    document.querySelectorAll(".flatpickr-month").forEach(function (element) {
      element.style.height = "40px";
    });

    document
      .querySelectorAll(".flatpickr-monthDropdown-months")
      .forEach(function (element) {
        element.style.setProperty("padding", "0", "important");
      });

    let selector_months = document.querySelectorAll(".flatpickr-current-month");
    instances = Array.isArray(instances) ? instances : [instances];
    instances.forEach((instance, i) => {
      let yearSelect = document.createElement("select");
      yearSelect.classList.add("numInputWrapper");
      yearSelect.style.setProperty("padding", "0", "important");
      yearSelect.style.borderRadius = "0px";
      yearSelect.id = `instance${instance.input.id}`;

      const currentYear = new Date().getFullYear() - 10;
      const startYear = 1900;
      for (let year = currentYear; year >= startYear; year--) {
        const option = document.createElement("option");
        option.value = year;
        option.textContent = year;
        yearSelect.appendChild(option);
      }

      selector_months[i].appendChild(yearSelect);
    });
  }, 1000);
}

// Función para obtener el valor de una cookie por su nombre
function getCookie(name) {
  let cookieArr = document.cookie.split(";");
  for (let i = 0; i < cookieArr.length; i++) {
    let cookiePair = cookieArr[i].split("=");
    // Eliminamos espacios en blanco y verificamos si el nombre de la cookie coincide
    if (name === cookiePair[0].trim()) {
      return decodeURIComponent(cookiePair[1]);
    }
  }
  // Retornamos null si no se encuentra la cookie
  return null;
}

// Función para obtener parámetros de la URL
function getUrlParameter(name) {
  name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
  let regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
  let results = regex.exec(location.search);
  return results === null
    ? ""
    : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function changeFieldsDisabled(value = false) {
  document.querySelector("input[name=birth_date_student]").disabled = value;
  document.querySelector("input[name=name_student]").disabled = value;
  document.querySelector("input[name=middle_name_student]").disabled = value;
  document.querySelector("input[name=lastname_student]").disabled = value;
  document.querySelector("input[name=middle_last_name_student]").disabled =
    value;
  document.querySelector("input[name=number_phone]").disabled = value;
  document.querySelector("input[name=email_student]").disabled = value;
  document.querySelector("select[name=gender]").disabled = value;
  document.querySelector("select[name=etnia]").disabled = value;
  document.querySelector("input[name=birth_date_parent]").disabled = value;
  document.querySelector("select[name=parent_document_type]").disabled = value;
  document.querySelector("input[name=id_document_parent]").disabled = value;
  document.querySelector("input[name=agent_name]").disabled = value;
  document.querySelector("input[name=agent_last_name]").disabled = value;
  document.querySelector("input[name=number_partner]").disabled = value;
  document.querySelector("select[name=gender_parent]").disabled = value;
  document.querySelector("select[name=country]").disabled = value;
  document.querySelector("input[name=city]").disabled = value;
  document.querySelector("input[name=email_partner]").disabled = value;
  document.querySelector("input[name=password]").disabled = value;
  document.querySelector("select[name=grade]").disabled = value;
  document.querySelector("select[name=program]").disabled = value;
  document.querySelector("select[name=institute_id]").disabled = value;
  document.querySelector("input[name=name_institute]").disabled = value;
  document.querySelector("input[name=terms]").disabled = value;
  document.getElementById("buttonsave").disabled = value;
}
