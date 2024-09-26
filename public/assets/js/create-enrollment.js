let signaturePadStudent;
let signaturePadParent;
let gradeSelected;
let downloading = false;

function resizeCanvas(canvasId) {
  const canvas = document.getElementById(canvasId);
  if (canvas && !downloading) {
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    let width, height;

    setTimeout(() => {
      width = canvas.parentNode ? (canvas.parentNode.offsetWidth * 0.70) : window.innerWidth;
      height = 120;
  
      canvas.width = width * ratio;
      canvas.height = height * ratio;
      canvas.style.width = `${width}px`;
      canvas.style.height = `${height}px`;
      canvas.getContext("2d").scale(ratio, ratio);
    }, 1000);
  }
}

window.addEventListener("resize", function () {
  resizeCanvas("signature-student");
  resizeCanvas("signature-parent");
});

window.addEventListener("orientationchange", function () {
  resizeCanvas("signature-student");
  resizeCanvas("signature-parent");
});

resizeCanvas("signature-student");
resizeCanvas("signature-parent");

// Create the SignaturePad objects after the canvas elements have been resized
if (document.getElementById("signature-student")) {
  document.body.classList.add("modal-open");
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
  let show_parent_info = document.querySelector('input[name="show_parent_info"]').value;

  document.getElementById("clear-student").addEventListener("click", () => {
    signaturePadStudent.clear();
    sign_here_student.style.display = "block";
    document.getElementById("signature-student").style.border =
      "1px solid gray";
    if (!signaturePadStudent.isEmpty() && !signaturePadParent.isEmpty()) {
      save_signatures.innerHTML = "Generate enrollment";
    } else {
      save_signatures.innerHTML = "Save";
    }
  });

  const clearParentElement = document.getElementById("clear-parent");
  if (clearParentElement) {
    clearParentElement.addEventListener("click", () => {
      signaturePadParent.clear();
      sign_here_parent.style.display = "block";
      document.getElementById("signature-parent").style.border =
        "1px solid gray";
      if (!signaturePadStudent.isEmpty() && !signaturePadParent.isEmpty()) {
        save_signatures.innerHTML = "Generate enrollment";
      } else {
        save_signatures.innerHTML = "Save";
      }
    });
  }

  if (signaturePadParent) {
    signaturePadParent.addEventListener("afterUpdateStroke", () => {
      if (!signaturePadStudent.isEmpty() && (signaturePadParent && !signaturePadParent.isEmpty())) {
        save_signatures.innerHTML = "Generate enrollment";
      } else {
        save_signatures.innerHTML = "Save";
      }
  
      if ((signaturePadParent && !signaturePadParent.isEmpty())) {
        sign_here_parent.style.display = "none";
        document.getElementById("signature-parent").style.border = "none";
        document.getElementById("signature-parent").style.borderBottom =
          "1px solid gray";
      } else {
        sign_here_parent.style.display = "block";
        document.getElementById("signature-parent").style.border =
          "1px solid gray";
      }
    });
  }

  signaturePadStudent.addEventListener("afterUpdateStroke", () => {
    if (!signaturePadStudent.isEmpty() && ((show_parent_info == 1 && (signaturePadParent && !signaturePadParent.isEmpty())) || show_parent_info == 0)) {
      save_signatures.innerHTML = "Generate enrollment";
    } else {
      save_signatures.innerHTML = "Save";
    }

    if (!signaturePadStudent.isEmpty()) {
      sign_here_student.style.display = "none";
      document.getElementById("signature-student").style.border = "none";
      document.getElementById("signature-student").style.borderBottom =
        "1px solid gray";
    } else {
      sign_here_student.style.display = "block";
      document.getElementById("signature-student").style.border =
        "1px solid gray";
    }
  });

  loadSignatures();
  save_signatures.addEventListener("click", function () {
    save_signatures.disabled = true;
    if (!signaturePadStudent.isEmpty() && ((show_parent_info == 1 && (signaturePadParent && !signaturePadParent.isEmpty())) || show_parent_info == 0)) {
      if (!gradeSelected) {
        save_signatures.disabled = false;
        alert(
          "To proceed with your enrollment, please select the last grade you completed"
        );
        return;
      }
      document.getElementById("please_select_grade").style.display = "none";
      document.getElementById("clear-student").style.display = "none";
      if (clearParentElement) {
        clearParentElement.style.display = "none";
      }
      downloading = true;
      var element = document.getElementById("content-pdf");
      var opt = {
        margin: [0.2, 0, 0, 0],
        filename: "Student Enrollment Agreement.pdf",
        image: { type: "jpeg", quality: 0.98 },
        jsPDF: { unit: "in", format: "a4", orientation: "portrait" },
        html2canvas: { scale: 2 },
        pagebreak: { after: "#part1" },
      };

      // New Promise-based usage:
      html2pdf().set(opt).from(element).save();
      html2pdf()
        .set(opt)
        .from(element)
        .outputPdf("blob", "Student Enrollment Agreement.pdf")
        .then((response) => {
          document.getElementById("modal-contraseña").style.display = "none";
          document.getElementById("modal-content").style.display = "none";
          // When the modal is closed, remove the `modal-open` class from the body element
          document.body.classList.remove("modal-open");
          sendSignatures(response);
        });
    } else if (
      !signaturePadStudent.isEmpty() ||
      ((show_parent_info == 1 && (signaturePadParent && !signaturePadParent.isEmpty())) || show_parent_info == 0)
    ) {
      if (!gradeSelected) {
        save_signatures.disabled = false;
        alert(
          "To proceed with your enrollment, please select the last grade you completed"
        );
        return;
      }

      document.getElementById("modal-contraseña").style.display = "none";
      document.getElementById("modal-content").style.display = "none";
      // When the modal is closed, remove the `modal-open` class from the body element
      document.body.classList.remove("modal-open");
      sendSignatures();
    } else {
      if (!gradeSelected) {
        save_signatures.disabled = false;
        alert(
          "To proceed with your enrollment, please select the last grade you completed"
        );
        return;
      }

      save_signatures.disabled = false;
      alert("You must have at least one signature to save");
      return;
    }
  });
}

function sendSignatures(doc = null) {
  student_user_id = document.querySelector(
    'input[name="student_user_id"]'
  ).value;
  partner_user_id = document.querySelector(
    'input[name="parent_user_id"]'
  ).value;
  const formData = new FormData();
  formData.append("action", "create_enrollment_document");
  if (signaturePadParent) {
    formData.append(
      "signature_parent",
      JSON.stringify(signaturePadParent.toData())
    );
  }
  formData.append(
    "signature_student",
    JSON.stringify(signaturePadStudent.toData())
  );
  formData.append("student_user_id", student_user_id);
  formData.append("partner_user_id", partner_user_id);
  formData.append("grade_selected", gradeSelected);

  if (doc) {
    formData.append("document", doc, "Student Enrollment Agreement.pdf");
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
    }
  };
}

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

function loadSignatures() {
  const XHR = new XMLHttpRequest();
  XHR.open("POST", ajax_object.ajax_url, true);
  XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  XHR.responseType = "text";
  XHR.send("action=load_signatures_data");
  XHR.onload = function () {
    if (XHR.status === 200) {
      let grade_selected = JSON.parse(XHR.responseText).grade_selected;

      let parent_signature = JSON.parse(XHR.responseText).parent_signature;
      if (parent_signature.length > 0) {
        signaturePadParent.fromData(parent_signature);
        signaturePadParent.off();
        document.getElementById("clear-parent").style.display = "none";
      }

      let student_signature = JSON.parse(XHR.responseText).student_signature;
      if (student_signature.length > 0) {
        signaturePadStudent.fromData(student_signature);
        signaturePadStudent.off();
        document.getElementById("clear-student").style.display = "none";
      }

      if (grade_selected) {
        updateGrade(grade_selected);
      }
    }
  };
}
