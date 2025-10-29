let signaturePadStudent;
let signaturePadParent;
let gradeSelected = null;
let downloading = false;
let first_time = false;

document.addEventListener("DOMContentLoaded", (event) => {
  if (document.getElementById("modal_open")) {
    document.body.classList.add("modal-open");
    setTimeout(() => {
      window.scrollTo(0, 0);
    }, 1000);
  }

  const closeModalEnrollment = document.getElementById(
    "close-modal-enrollment"
  );
  if (closeModalEnrollment) {
    closeModalEnrollment.addEventListener("click", () => {
      const modalContrasena = document.getElementById("modal-contraseña");
      const modalContent = document.getElementById("modal-content");

      if (modalContrasena) {
        modalContrasena.style.display = "none";
      }
      if (modalContent) {
        modalContent.style.display = "none";
      }
      document.body.classList.remove("modal-open");
    });
  }

  function resizeCanvas(canvasId, timmeout) {
    const canvas = document.getElementById(canvasId);
    if (canvas && !downloading) {
      const ratio = Math.max(window.devicePixelRatio || 1, 1);
      let width, height;

      setTimeout(() => {
        let multiply = canvas.parentNode
          ? canvas.parentNode.offsetWidth < 500
            ? 0.6
            : 0.8
          : 0.8;
        width = canvas.parentNode
          ? canvas.parentNode.offsetWidth * multiply
          : window.innerWidth;
        height = 120;

        canvas.width = width * ratio;
        canvas.height = height * ratio;
        canvas.style.width = `${width}px`;
        canvas.style.height = `${height}px`;
        canvas.getContext("2d").scale(ratio, ratio);

        loadSignatures();
      }, timmeout);
    }
  }

  // Función para detectar si es un dispositivo táctil
  function isTouchDevice() {
    return (
      "ontouchstart" in window ||
      navigator.maxTouchPoints > 0 ||
      navigator.msMaxTouchPoints > 0
    );
  }

  // Ejecutar solo en dispositivos NO táctiles
  if (!isTouchDevice()) {
    window.addEventListener("resize", function () {
      resizeCanvas("signature-student", 100);
      resizeCanvas("signature-parent", 100);
    });
  }

  // window.addEventListener("orientationchange", function () {
  //   resizeCanvas("signature-student", 0);
  //   resizeCanvas("signature-parent", 0);
  // });

  resizeCanvas("signature-student", 2500);
  resizeCanvas("signature-parent", 2500);

  // Create the SignaturePad objects after the canvas elements have been resized
  if (document.getElementById("signature-student")) {
    const studentElement = document.getElementById("signature-student");
    const parentElement = document.getElementById("signature-parent");

    if (studentElement) {
      signaturePadStudent = new SignaturePad(studentElement);
    }

    if (parentElement) {
      signaturePadParent = new SignaturePad(parentElement);
    }

    save_signatures = document.getElementById("saveSignatures");
    sign_here_parent = document.getElementById("sign-here-parent");
    sign_here_student = document.getElementById("sign-here-student");
    let show_parent_info = document.querySelector(
      'input[name="show_parent_info"]'
    ).value;

    document.getElementById("clear-student").addEventListener("click", () => {
      signaturePadStudent.clear();
      sign_here_student.style.display = "block";
      document.getElementById("signature-student").style.border =
        "1px solid gray";
      document.getElementById("signature-student").style.backgroundColor =
        "#ffff005c";
      if (!signaturePadStudent.isEmpty() && !signaturePadParent.isEmpty()) {
        save_signatures.innerHTML = `Generate ${returnButtonTitle()}`;
      } else {
        save_signatures.innerHTML = "Save";
      }
    });

    document
      .getElementById("clear-student-signature")
      .addEventListener("click", () => {
        signaturePadStudent.clear();
        sign_here_student.style.display = "block";
        document.getElementById("signature-student").style.border =
          "1px solid gray";
        document.getElementById("signature-student").style.backgroundColor =
          "#ffff005c";
        if (!signaturePadStudent.isEmpty() && !signaturePadParent.isEmpty()) {
          save_signatures.innerHTML = `Generate ${returnButtonTitle()}`;
        } else {
          save_signatures.innerHTML = "Save";
        }

        document.getElementById("clear-student-signature").style.display =
          "none";
        document.getElementById("signature-text-student").style.display =
          "none";
        document.getElementById("signature-pad-student").style.display =
          "block";
        document.getElementById("clear-student").style.display = "block";
        document.getElementById("generate-signature-student").style.display =
          "block";
      });

    let clearParentElement = document.getElementById("clear-parent");
    if (clearParentElement) {
      clearParentElement.addEventListener("click", () => {
        signaturePadParent.clear();
        sign_here_parent.style.display = "block";
        document.getElementById("signature-parent").style.border =
          "1px solid gray";
        document.getElementById("signature-parent").style.backgroundColor =
          "#ffff005c";
        if (!signaturePadStudent.isEmpty() && !signaturePadParent.isEmpty()) {
          save_signatures.innerHTML = `Generate ${returnButtonTitle()}`;
        } else {
          save_signatures.innerHTML = "Save";
        }
      });

      document
        .getElementById("clear-parent-signature")
        .addEventListener("click", () => {
          signaturePadParent.clear();
          sign_here_parent.style.display = "block";
          document.getElementById("signature-parent").style.border =
            "1px solid gray";
          document.getElementById("signature-parent").style.backgroundColor =
            "#ffff005c";
          if (!signaturePadStudent.isEmpty() && !signaturePadParent.isEmpty()) {
            save_signatures.innerHTML = `Generate ${returnButtonTitle()}`;
          } else {
            save_signatures.innerHTML = "Save";
          }

          document.getElementById("clear-parent-signature").style.display =
            "none";
          document.getElementById("signature-text-parent").style.display =
            "none";
          document.getElementById("signature-pad-parent").style.display =
            "block";
          document.getElementById("clear-parent").style.display = "block";
          document.getElementById("generate-signature-parent").style.display =
            "block";
        });
    }

    if (signaturePadParent) {
      signaturePadParent.addEventListener("afterUpdateStroke", () => {
        if (
          !signaturePadStudent.isEmpty() &&
          signaturePadParent &&
          !signaturePadParent.isEmpty()
        ) {
          save_signatures.innerHTML = `Generate ${returnButtonTitle()}`;
        } else {
          save_signatures.innerHTML = "Save";
        }

        if (signaturePadParent && !signaturePadParent.isEmpty()) {
          sign_here_parent.style.display = "none";
          document.getElementById("signature-parent").style.border = "none";
          document.getElementById("signature-parent").style.borderBottom =
            "1px solid gray";
          document.getElementById("signature-parent").style.backgroundColor =
            "#fff";
        } else {
          sign_here_parent.style.display = "block";
          document.getElementById("signature-parent").style.border =
            "1px solid gray";
          document.getElementById("signature-parent").style.backgroundColor =
            "#ffff005c";
        }
      });
    }

    signaturePadStudent.addEventListener("afterUpdateStroke", () => {
      if (
        !signaturePadStudent.isEmpty() &&
        ((show_parent_info == 1 &&
          signaturePadParent &&
          !signaturePadParent.isEmpty()) ||
          show_parent_info == 0)
      ) {
        save_signatures.innerHTML = `Generate ${returnButtonTitle()}`;
      } else {
        save_signatures.innerHTML = "Save";
      }

      if (!signaturePadStudent.isEmpty()) {
        sign_here_student.style.display = "none";
        document.getElementById("signature-student").style.border = "none";
        document.getElementById("signature-student").style.borderBottom =
          "1px solid gray";
        document.getElementById("signature-student").style.backgroundColor =
          "#fff";
      } else {
        sign_here_student.style.display = "block";
        document.getElementById("signature-student").style.border =
          "1px solid gray";
        document.getElementById("signature-student").style.backgroundColor =
          "#ffff005c";
      }
    });

    save_signatures.addEventListener("click", function () {
      auto_signature_student = document.querySelector(
        'input[name="auto_signature_student"]'
      ).value;
      auto_signature_parent = 0;
      if (document.querySelector('input[name="auto_signature_parent"]')) {
        auto_signature_parent = document.querySelector(
          'input[name="auto_signature_parent"]'
        ).value;
      }
      save_signatures.disabled = true;

      let document_id = "ENROLLMENT";
      if (document.querySelector("input[name=document_id]")) {
        document_id = document.querySelector("input[name=document_id]").value;
      }

      if (
        !gradeSelected &&
        document.getElementById("please_select_grade") &&
        document.getElementById("select_grade")
      ) {
        save_signatures.disabled = false;
        document.getElementById("please_select_grade").style.display = "block";
        document.getElementById("select_grade").style.color = "red";
        document
          .getElementById("select_grade")
          .scrollIntoView({ behavior: "smooth" });
        alert(
          "To proceed with your document, please select the last grade you completed"
        );
        return;
      }

      if (show_parent_info == 0) {
        if (auto_signature_student == 1) {
          generateDocEnrollment();
        } else if (signaturePadStudent.isEmpty()) {
          save_signatures.disabled = false;
          alert(
            "To proceed with your document, please sign in the student area or generate the signature automatically"
          );
          return;
        } else {
          generateDocEnrollment();
        }
      } else {
        if (auto_signature_student == 1 && auto_signature_parent == 1) {
          generateDocEnrollment();
          return;
        }

        if (auto_signature_student == 1) {
          if (signaturePadParent.isEmpty()) {
            generateDocEnrollmentSend();
          } else {
            generateDocEnrollment();
          }
          return;
        }

        if (auto_signature_parent == 1) {
          if (signaturePadStudent.isEmpty()) {
            generateDocEnrollmentSend();
          } else {
            generateDocEnrollment();
          }
          return;
        }

        if (!signaturePadParent.isEmpty() && !signaturePadStudent.isEmpty()) {
          generateDocEnrollment();
          return;
        } else if (
          !signaturePadParent.isEmpty() ||
          !signaturePadStudent.isEmpty()
        ) {
          generateDocEnrollmentSend();
        } else {
          save_signatures.disabled = false;
          alert(
            "To continue with your registration, please sign or generate signatures automatically"
          );
          return;
        }
      }
    });
  }

  function generateDocEnrollment() {
    if (document.getElementById("please_select_grade")) {
      document.getElementById("please_select_grade").style.display = "none";
    }
    document.getElementById("clear-student").style.display = "none";
    document.getElementById("generate-signature-student").style.display =
      "none";
    document.getElementById("clear-student-signature").style.display = "none";
    let clearParentElement = document.getElementById("clear-parent");
    if (clearParentElement) {
      clearParentElement.style.display = "none";
      document.getElementById("clear-parent-signature").style.display = "none";
    }
    let generateSignatureParentElement = document.getElementById(
      "generate-signature-parent"
    );
    if (generateSignatureParentElement) {
      generateSignatureParentElement.style.display = "none";
    }

    let document_id = "ENROLLMENT";
    if (document.querySelector("input[name=document_id]")) {
      document_id = document.querySelector("input[name=document_id]").value;
    }

    let document_name = null;
    if (document.querySelector("input[name=document_name]")) {
      document_name = document.querySelector("input[name=document_name]").value;
    }

    let filename = "Student Enrollment Agreement.pdf";
    if (document_name) {
      filename = `${document_name.toLowerCase()}.pdf`;
    } else if (document_id != "ENROLLMENT") {
      filename = "Student Missing Document Agreement.pdf";
    }

    downloading = true;
    var element = document.getElementById("content-pdf");
    var opt = {
      margin: [0.2, 0, 0, 0],
      filename: filename,
      image: { type: "jpeg", quality: 0.98 },
      jsPDF: { unit: "in", format: "a4", orientation: "portrait" },
      html2canvas: { scale: 3 },
      pagebreak: { after: ".page-break-new-section" },
    };

    html2pdf()
      .set(opt)
      .from(element)
      .outputPdf("blob", filename)
      .then((response) => {
        generateDocEnrollmentSend(response);
      });
  }

  function generateDocEnrollmentSend(doc = null) {
    sendSignatures(doc);
  }

  function sendSignatures(doc = null) {
    auto_signature_student = document.querySelector(
      'input[name="auto_signature_student"]'
    ).value;
    auto_signature_parent = 0;
    if (document.querySelector('input[name="auto_signature_parent"]')) {
      auto_signature_parent = document.querySelector(
        'input[name="auto_signature_parent"]'
      ).value;
    }

    let student_user_id = null;
    if (document.querySelector('input[name="student_user_id"]')) {
      student_user_id = document.querySelector(
        'input[name="student_user_id"]'
      ).value;
    }

    let partner_user_id = null;
    if (document.querySelector('input[name="parent_user_id"]')) {
      partner_user_id = document.querySelector(
        'input[name="parent_user_id"]'
      ).value;
    }

    const formData = new FormData();
    formData.append("action", "create_enrollment_document");
    if (signaturePadParent) {
      if (auto_signature_parent == 1) {
        formData.append("signature_parent", JSON.stringify(["automatic"]));
      } else {
        formData.append(
          "signature_parent",
          JSON.stringify(signaturePadParent.toData())
        );
      }
    }
    if (auto_signature_student == 1) {
      formData.append("signature_student", JSON.stringify(["automatic"]));
    } else {
      formData.append(
        "signature_student",
        JSON.stringify(signaturePadStudent.toData())
      );
    }
    if (student_user_id) {
      formData.append("student_user_id", student_user_id);
    }
    if (partner_user_id) {
      formData.append("partner_user_id", partner_user_id);
    }
    if (gradeSelected) {
      formData.append("grade_selected", gradeSelected);
    }

    let document_id = "ENROLLMENT";
    if (document.querySelector("input[name=document_id]")) {
      document_id = document.querySelector("input[name=document_id]").value;
    }
    formData.append("document_id", document_id);

    let document_name = null;
    if (document.querySelector("input[name=document_name]")) {
      document_name = document.querySelector("input[name=document_name]").value;
    }

    let filename = "Student Enrollment Agreement.pdf";
    if (document_name) {
      filename = `${document_name.toLowerCase()}.pdf`;
    } else if (document_id != "ENROLLMENT") {
      filename = "Student Missing Document Agreement.pdf";
    }

    if (doc) {
      formData.append("document", doc, filename);
    }

    const XHR = new XMLHttpRequest();
    XHR.open(
      "POST",
      `${ajax_object.ajax_url}?action=create_enrollment_document`,
      true
    );
    XHR.send(formData); // Remove the Content-type header

    XHR.onload = function () {
      if (XHR.status === 200) {
        // document.getElementById("modal-contraseña").style.display = "none";
        // document.getElementById("modal-content").style.display = "none";
        // document.body.classList.remove("modal-open");

        location.reload();
      }
    };
  }

  function loadSignatures() {
    const XHR = new XMLHttpRequest();
    XHR.open("POST", ajax_object.ajax_url, true);
    XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    XHR.responseType = "text";
    let document_id = "ENROLLMENT";
    if (document.querySelector("input[name=document_id]")) {
      document_id = document.querySelector("input[name=document_id]").value;
    }
    XHR.send(`action=load_signatures_data&document=${document_id}`);
    XHR.onload = function () {
      if (XHR.status === 200) {
        let grade_selected = JSON.parse(XHR.responseText).grade_selected;

        let parent_signature = JSON.parse(XHR.responseText).parent_signature;
        if (parent_signature.length > 0) {
          if (parent_signature[0] == "automatic") {
            document.querySelector(
              'input[name="auto_signature_parent"]'
            ).value = 1;
            document.getElementById("signature-text-parent").style.display =
              "block";
            document.getElementById("signature-pad-parent").style.display =
              "none";
            document.getElementById("clear-parent").style.display = "none";
            document.getElementById("generate-signature-parent").style.display =
              "none";

            sign_here_parent.style.display = "none";
            document.getElementById("signature-parent").style.border = "none";
            document.getElementById("signature-parent").style.borderBottom =
              "1px solid gray";
            document.getElementById("signature-parent").style.backgroundColor =
              "#fff";
          } else {
            signaturePadParent.fromData(parent_signature);
            signaturePadParent.off();
            document.getElementById("clear-parent").style.display = "none";
            document.getElementById("generate-signature-parent").style.display =
              "none";

            sign_here_parent.style.display = "none";
            document.getElementById("signature-parent").style.border = "none";
            document.getElementById("signature-parent").style.borderBottom =
              "1px solid gray";
            document.getElementById("signature-parent").style.backgroundColor =
              "#fff";
          }
        }

        let student_signature = JSON.parse(XHR.responseText).student_signature;
        if (student_signature.length > 0) {
          if (student_signature[0] == "automatic") {
            document.querySelector(
              'input[name="auto_signature_student"]'
            ).value = 1;
            document.getElementById("signature-text-student").style.display =
              "block";
            document.getElementById("signature-pad-student").style.display =
              "none";
            document.getElementById("clear-student").style.display = "none";
            document.getElementById(
              "generate-signature-student"
            ).style.display = "none";

            sign_here_student.style.display = "none";
            document.getElementById("signature-student").style.border = "none";
            document.getElementById("signature-student").style.borderBottom =
              "1px solid gray";
            document.getElementById("signature-student").style.backgroundColor =
              "#fff";
          } else {
            signaturePadStudent.fromData(student_signature);
            signaturePadStudent.off();
            document.getElementById("clear-student").style.display = "none";
            document.getElementById(
              "generate-signature-student"
            ).style.display = "none";

            sign_here_student.style.display = "none";
            document.getElementById("signature-student").style.border = "none";
            document.getElementById("signature-student").style.borderBottom =
              "1px solid gray";
            document.getElementById("signature-student").style.backgroundColor =
              "#fff";
          }
        }

        if (grade_selected) {
          updateGrade(grade_selected);
        }
      }
    };
  }

  function returnButtonTitle() {
    let document_id = "ENROLLMENT";
    let document_name = null;
    if (document.querySelector("input[name=document_id]")) {
      document_id = document.querySelector("input[name=document_id]").value;
    }

    if (document.querySelector("input[name=document_name]")) {
      document_name = document.querySelector("input[name=document_name]").value;
    }

    if (document_name) {
      return document_name.toLowerCase();
    } else if (document_id == "ENROLLMENT") {
      return "enrollment";
    } else {
      return "missing document";
    }
  }
});

function updateGrade(id) {
  const gradeSpans = document.querySelectorAll('[id^="grade"]');
  gradeSpans.forEach((span) => {
    span.textContent = "( )"; // reset all spans to blank space
  });
  const selectedSpan = document.getElementById(id);
  selectedSpan.textContent = "(✓)"; // set the selected span to "✓"
  gradeSelected = id;
  document.getElementById("please_select_grade").style.display = "none";
  document.getElementById("select_grade").style.color = "#000";
}

function autoSignature(hide, show, button_hide, clear_hide = null) {
  document.getElementById(hide).style.display = "none";
  document.getElementById(show).style.display = "block";
  document.getElementById(button_hide).style.display = "none";

  if (button_hide == "generate-signature-student") {
    document.querySelector('input[name="auto_signature_student"]').value = 1;
    document.getElementById("clear-student-signature").style.display = "block";
  } else {
    document.querySelector('input[name="auto_signature_parent"]').value = 1;
    document.getElementById("clear-parent-signature").style.display = "block";
  }

  if (clear_hide) {
    document.getElementById(clear_hide).style.display = "none";
  }
}
