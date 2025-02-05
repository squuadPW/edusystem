document.addEventListener("DOMContentLoaded", function () {
  not_institute = document.getElementById("institute_id");
  not_institute_others = document.getElementById("institute_id_others");

  if (document.getElementById("birth_date")) {
    flatpickr(document.getElementById("birth_date"), {
      dateFormat: "m/d/Y",
      maxDate: "today",
      disableMobile: "true",
    });
  }

  if (not_institute) {
    not_institute.addEventListener("change", (e) => {
      if (e.target.value == "other") {
        document.getElementById("name-institute-field").style.display = "block";
        document.getElementById("name_institute").required = true;
        document.getElementById("institute_id").required = false;
        document.getElementById("institute_id_required").textContent = "";
      } else {
        document.getElementById("name-institute-field").style.display = "none";
        document.getElementById("name_institute").required = false;
        document.getElementById("institute_id").required = true;
        document.getElementById("institute_id_required").textContent = "*";
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

      if (!allowedTypes.includes(fileInput.files[0].type)) {
        alert("Only allowed file types: " + allowedExtensions.join(", "));
        fileInput.value = "";
        fileLabels[index].textContent = "Select file";
      } else {
        const fileName = fileInput.files[0].name;
        fileLabels[index].textContent = fileName ? fileName : "Select file";
      }
    });
  });

  const countrySelect = document.getElementById("country-select");
  const instituteSelect = document.getElementById("institute_id");

  if (countrySelect && instituteSelect) {
    countrySelect.addEventListener("change", function () {
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
    });
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
  if (timer) {
    clearTimeout(timer);
  }
  timer = setTimeout(() => {
    if (idDocument.value && typeDocument.value) {
      sendAjax(
        "action=check_scholarship",
        idDocument.value,
        1,
        typeDocument.value,
        scholarship
      );
    }
  }, 1000);
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
  idInput.dispatchEvent(new Event('input'));
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
      date = e.target.value;
      date = date.split("/");

      start = new Date(date[2], date[0] - 1, date[1]);
      today = new Date();
      diff = diff_years(today, start);
      if (diff >= 18) {
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

const idBitrix = new URLSearchParams(window.location.search).get("idbitrix");

if (idBitrix) {
  document.getElementById("loading").style.display = "block";
  const countries = {
    AF: "Afghanistan",
    AL: "Albania",
    DZ: "Algeria",
    AS: "American Samoa",
    AD: "Andorra",
    AO: "Angola",
    AI: "Anguilla",
    AQ: "Antarctica",
    AG: "Antigua and Barbuda",
    AR: "Argentina",
    AM: "Armenia",
    AW: "Aruba",
    AU: "Australia",
    AT: "Austria",
    AZ: "Azerbaijan",
    BS: "Bahamas (the)",
    BH: "Bahrain",
    BD: "Bangladesh",
    BB: "Barbados",
    BY: "Belarus",
    BE: "Belgium",
    BZ: "Belize",
    BJ: "Benin",
    BM: "Bermuda",
    BT: "Bhutan",
    BO: "Bolivia (Plurinational State of)",
    BQ: "Bonaire, Sint Eustatius and Saba",
    BA: "Bosnia and Herzegovina",
    BW: "Botswana",
    BV: "Bouvet Island",
    BR: "Brazil",
    IO: "British Indian Ocean Territory (the)",
    BN: "Brunei Darussalam",
    BG: "Bulgaria",
    BF: "Burkina Faso",
    BI: "Burundi",
    CV: "Cabo Verde",
    KH: "Cambodia",
    CM: "Cameroon",
    CA: "Canada",
    KY: "Cayman Islands (the)",
    CF: "Central African Republic (the)",
    TD: "Chad",
    CL: "Chile",
    CN: "China",
    CX: "Christmas Island",
    CC: "Cocos (Keeling) Islands (the)",
    CO: "Colombia",
    KM: "Comoros (the)",
    CD: "Congo (the Democratic Republic of the)",
    CG: "Congo (the)",
    CK: "Cook Islands (the)",
    CR: "Costa Rica",
    HR: "Croatia",
    CU: "Cuba",
    CW: "Curaçao",
    CY: "Cyprus",
    CZ: "Czechia",
    CI: "Côte d'Ivoire",
    DK: "Denmark",
    DJ: "Djibouti",
    DM: "Dominica",
    DO: "Dominican Republic (the)",
    EC: "Ecuador",
    EG: "Egypt",
    SV: "El Salvador",
    GQ: "Equatorial Guinea",
    ER: "Eritrea",
    EE: "Estonia",
    SZ: "Eswatini",
    ET: "Ethiopia",
    FK: "Falkland Islands (the) [Malvinas]",
    FO: "Faroe Islands (the)",
    FJ: "Fiji",
    FI: "Finland",
    FR: "France",
    GF: "French Guiana",
    PF: "French Polynesia",
    TF: "French Southern Territories (the)",
    GA: "Gabon",
    GM: "Gambia (the)",
    GE: "Georgia",
    DE: "Germany",
    GH: "Ghana",
    GI: "Gibraltar",
    GR: "Greece",
    GL: "Greenland",
    GD: "Grenada",
    GP: "Guadeloupe",
    GU: "Guam",
    GT: "Guatemala",
    GG: "Guernsey",
    GN: "Guinea",
    GW: "Guinea-Bissau",
    GY: "Guyana",
    HT: "Haiti",
    HM: "Heard Island and McDonald Islands",
    VA: "Holy See (the)",
    HN: "Honduras",
    HK: "Hong Kong",
    HU: "Hungary",
    IS: "Iceland",
    IN: "India",
    ID: "Indonesia",
    IR: "Iran (Islamic Republic of)",
    IQ: "Iraq",
    IE: "Ireland",
    IM: "Isle of Man",
    IL: "Israel",
    IT: "Italy",
    JM: "Jamaica",
    JP: "Japan",
    JE: "Jersey",
    JO: "Jordan",
    KZ: "Kazakhstan",
    KE: "Kenya",
    KI: "Kiribati",
    KP: "Korea (the Democratic People's Republic of)",
    KR: "Korea (the Republic of)",
    KW: "Kuwait",
    KG: "Kyrgyzstan",
    LA: "Lao People's Democratic Republic (the)",
    LV: "Latvia",
    LB: "Lebanon",
    LS: "Lesotho",
    LR: "Liberia",
    LY: "Libya",
    LI: "Liechtenstein",
    LT: "Lithuania",
    LU: "Luxembourg",
    MO: "Macao",
    MG: "Madagascar",
    MW: "Malawi",
    MY: "Malaysia",
    MV: "Maldives",
    ML: "Mali",
    MT: "Malta",
    MH: "Marshall Islands (the)",
    MQ: "Martinique",
    MR: "Mauritania",
    MU: "Mauritius",
    YT: "Mayotte",
    MX: "Mexico",
    FM: "Micronesia (Federated States of)",
    MD: "Moldova (the Republic of)",
    MC: "Monaco",
    MN: "Mongolia",
    ME: "Montenegro",
    MS: "Montserrat",
    MA: "Morocco",
    MZ: "Mozambique",
    MM: "Myanmar",
    NA: "Namibia",
    NR: "Nauru",
    NP: "Nepal",
    NL: "Netherlands (the)",
    NC: "New Caledonia",
    NZ: "New Zealand",
    NI: "Nicaragua",
    NE: "Niger (the)",
    NG: "Nigeria",
    NU: "Niue",
    NF: "Norfolk Island",
    MP: "Northern Mariana Islands (the)",
    NO: "Norway",
    OM: "Oman",
    PK: "Pakistan",
    PW: "Palau",
    PS: "Palestine, State of",
    PA: "Panama",
    PG: "Papua New Guinea",
    PY: "Paraguay",
    PE: "Peru",
    PH: "Philippines (the)",
    PN: "Pitcairn",
    PL: "Poland",
    PT: "Portugal",
    PR: "Puerto Rico",
    QA: "Qatar",
    MK: "Republic of North Macedonia",
    RO: "Romania",
    RU: "Russian Federation (the)",
    RW: "Rwanda",
    RE: "Réunion",
    BL: "Saint Barthélemy",
    SH: "Saint Helena, Ascension and Tristan da Cunha",
    KN: "Saint Kitts and Nevis",
    LC: "Saint Lucia",
    MF: "Saint Martin (French part)",
    PM: "Saint Pierre and Miquelon",
    VC: "Saint Vincent and the Grenadines",
    WS: "Samoa",
    SM: "San Marino",
    ST: "Sao Tome and Principe",
    SA: "Saudi Arabia",
    SN: "Senegal",
    RS: "Serbia",
    SC: "Seychelles",
    SL: "Sierra Leone",
    SG: "Singapore",
    SX: "Sint Maarten (Dutch part)",
    SK: "Slovakia",
    SI: "Slovenia",
    SB: "Solomon Islands",
    SO: "Somalia",
    ZA: "South Africa",
    GS: "South Georgia and the South Sandwich Islands",
    SS: "South Sudan",
    ES: "Spain",
    LK: "Sri Lanka",
    SD: "Sudan (the)",
    SR: "Suriname",
    SJ: "Svalbard and Jan Mayen",
    SE: "Sweden",
    CH: "Switzerland",
    SY: "Syrian Arab Republic",
    TW: "Taiwan",
    TJ: "Tajikistan",
    TZ: "Tanzania, United Republic of",
    TH: "Thailand",
    TL: "Timor-Leste",
    TG: "Togo",
    TK: "Tokelau",
    TO: "Tonga",
    TT: "Trinidad and Tobago",
    TN: "Tunisia",
    TR: "Turkey",
    TM: "Turkmenistan",
    TC: "Turks and Caicos Islands (the)",
    TV: "Tuvalu",
    UG: "Uganda",
    UA: "Ukraine",
    AE: "United Arab Emirates (the)",
    GB: "United Kingdom of Great Britain and Northern Ireland (the)",
    UM: "United States Minor Outlying Islands (the)",
    US: "United States of America (the)",
    UY: "Uruguay",
    UZ: "Uzbekistan",
    VU: "Vanuatu",
    VE: "Venezuela (Bolivarian Republic of)",
    VN: "Viet Nam",
    VG: "Virgin Islands (British)",
    VI: "Virgin Islands (U.S.)",
    WF: "Wallis and Futuna",
    EH: "Western Sahara",
    YE: "Yemen",
    ZM: "Zambia",
    ZW: "Zimbabwe",
    AX: "Åland Islands",
  };

  const url = `https://api.luannerkerton.com/api/getLeadFromAes?id_bitrix=${idBitrix}`;
  const data = { id_bitrix: idBitrix };

  fetch(url, {
    method: "GET",
    headers: {
      "Content-Type": "application/json",
    },
  })
    .then((response) => response.json())
    .then((data) => {
      let name = data.name.split(" ");
      let last_name = data.last_name.split(" ");
      let tutor = data.tutor.split(" ");

      var fecha = new Date(data.birthdate);
      fecha.setDate(fecha.getDate() + 1);
      var fechaIso = fecha.toISOString().split("T")[0];
      flatpickr("#birth_date_student", {
        defaultDate: fechaIso,
        altFormat: "m/d/Y",
      });

      // Refresh the intlTelInput library
      document.querySelector('input[name="name_student"]').value = name[0];
      document.querySelector('input[name="middle_name_student"]').value =
        name[1] ?? "";
      document.querySelector('input[name="lastname_student"]').value =
        last_name[0];
      document.querySelector('input[name="middle_last_name_student"]').value =
        last_name[1] ?? "";
      document.querySelector('input[name="agent_name"]').value = tutor[0];
      document.querySelector('input[name="agent_last_name"]').value = tutor[1];
      const input = document.querySelector('input[name="number_phone"]');
      const iti = intlTelInput(input, {
        // options
        autoFormat: true, // disable auto formatting
        separateDialCode: true, // separate dial code from the number
      });

      iti.setNumber(data.contact_phone);
      document.querySelector('input[name="email_student"]').value =
        data.contact_email;
      switch (data.degree) {
        case "9no (antepenúltimo)":
          document.querySelector('select[name="grade"]').value = 1;
          break;
        case "10mo (penúltimo)":
          document.querySelector('select[name="grade"]').value = 2;
          break;
        case "11vo (último)":
          document.querySelector('select[name="grade"]').value = 3;
          break;
        case "Bachiller (Graduado)":
          document.querySelector('select[name="grade"]').value = 4;
          break;
      }
      const countryCode = Object.keys(countries).find(
        (key) => countries[key].toLowerCase() === data.country.toLowerCase()
      );
      document.querySelector('select[name="country"]').value = countryCode;
      document.querySelector('input[name="city"]').value = data.city;

      document.getElementById("loading").style.display = "none";
    })
    .catch((error) => {
      document.getElementById("loading").style.display = "none";
    });
}

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

let select_country_step_two = document.getElementById(
  "country-select-step-two"
);
let select_state_step_two = document.getElementById("state-select-step-two"); // Asegúrate de tener este select en tu HTML

if (select_country_step_two) {
  select_country_step_two.addEventListener("change", function (e) {
    select_state_step_two.disabled = true;
    let action = "action=get_states_country";
    const XHR = new XMLHttpRequest();
    XHR.open("POST", `${ajax_object.ajax_url}?${action}`, true);
    XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    XHR.responseType = "json";
    let params = `${action}&option=${e.target.value}`;
    XHR.send(params);

    XHR.onload = function () {
      if (XHR.status === 200) {
        // Limpiar el select de estados antes de llenarlo
        select_state_step_two.innerHTML = "";

        // Crear una opción por defecto
        let defaultOption = document.createElement("option");
        defaultOption.value = "";
        defaultOption.textContent = "Select a state"; // Cambia el texto según sea necesario
        select_state_step_two.appendChild(defaultOption);

        // Recorrer los estados y crear las opciones
        select_state_step_two.required = false;
        if (XHR.response && XHR.response.states) {
          for (let key in XHR.response.states) {
            if (XHR.response.states.hasOwnProperty(key)) {
              let option = document.createElement("option");
              option.value = key; // Código del estado
              option.textContent = XHR.response.states[key]; // Nombre del estado
              select_state_step_two.appendChild(option);
            }
          }
          select_state_step_two.disabled = false;
          select_state_step_two.required = true;
        }
      }
    };
  });
}

let select_payment_methods = document.querySelectorAll(".card-select-payment");
if (select_payment_methods.length > 0) {
  // Verifica si hay elementos seleccionados
  select_payment_methods.forEach((payment) => {
    payment.addEventListener("click", function (e) {
      // Remover la clase .card-selected-payment de todos los elementos
      select_payment_methods.forEach((p) =>
        p.classList.remove("card-selected-payment")
      );

      // Agregar la clase .card-selected-payment al elemento que fue clickeado
      e.currentTarget.classList.add("card-selected-payment");

      // Obtener el data-id del elemento clickeado
      let paymentId = e.currentTarget.dataset.id;
      console.log(paymentId); // Muestra el data-id en la consola

      // Asignar el valor al input correspondiente
      document.querySelector("input[name=payment_method_selected]").value =
        paymentId;
    });
  });
}

let buttonsave_secondary = document.getElementById("buttonsave_secondary");
if (buttonsave_secondary) {
  // Verifica si hay elementos seleccionados
  buttonsave_secondary.addEventListener("click", function (e) {
    document.getElementById("buttonsave").click();
  });
}

// setTimeout(() => {
//   let payment_methods_checkout = document.querySelector('.wc_payment_methods');

//   // Verificamos que payment_methods_checkout no sea null
//   if (payment_methods_checkout) {
//     // Obtenemos el valor de la cookie payment_method_selected
//     let selectedPaymentMethod = getCookie('payment_method_selected');

//     // Seleccionamos todos los elementos <li> dentro de payment_methods_checkout
//     let payment_methods = payment_methods_checkout.querySelectorAll('li');

//     // Iteramos sobre cada <li>
//     payment_methods.forEach(method => {
//       // Verificamos si el <li> tiene la clase que coincide con el valor de la cookie
//       if (!method.classList.contains(selectedPaymentMethod)) {
//         // Si no tiene la clase, le asignamos display: none
//         method.style.display = 'none';
//       }
//     });
//   }
// }, 3500);

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

function changeFieldsDisabled(value = false) {
  document.querySelector(
    "input[name=birth_date_student]"
  ).disabled = value;
  document.querySelector("input[name=name_student]").disabled = value;
  document.querySelector(
    "input[name=middle_name_student]"
  ).disabled = value;
  document.querySelector(
    "input[name=lastname_student]"
  ).disabled = value;
  document.querySelector(
    "input[name=middle_last_name_student]"
  ).disabled = value;
  document.querySelector("input[name=number_phone]").disabled = value;
  document.querySelector("input[name=email_student]").disabled = value;
  document.querySelector("select[name=gender]").disabled = value;
  document.querySelector("select[name=etnia]").disabled = value;
  document.querySelector(
    "input[name=birth_date_parent]"
  ).disabled = value;
  document.querySelector(
    "select[name=parent_document_type]"
  ).disabled = value;
  document.querySelector(
    "input[name=id_document_parent]"
  ).disabled = value;
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
