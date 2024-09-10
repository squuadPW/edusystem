let signaturePadStudent;
let signaturePadParent;
let gradeSelected;

function resizeCanvas(canvasId) {
  const canvas = document.getElementById(canvasId);
  if (canvas) {
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    let width, height;

    // Set different canvas sizes based on screen sizes
    if (window.matchMedia("(max-width: 768px)").matches) {
      // mobile
      width = 150;
      height = 100;
    } else {
      // laptop
      width = 300;
      height = 100;
    }

    canvas.width = width * ratio;
    canvas.height = height * ratio;
    canvas.style.width = width + "px";
    canvas.style.height = height + "px";
    canvas.getContext("2d").scale(ratio, ratio);
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
  signaturePadStudent = new SignaturePad(
    document.getElementById("signature-student")
  );
  signaturePadParent = new SignaturePad(
    document.getElementById("signature-parent")
  );
  save_signatures = document.getElementById("saveSignatures");

  document.getElementById("clear-student").addEventListener("click", () => {
    signaturePadStudent.clear();
    if (!signaturePadStudent.isEmpty() && !signaturePadParent.isEmpty()) {
      save_signatures.innerHTML = "Generate enrollment";
    } else {
      save_signatures.innerHTML = "Save";
    }
  });

  document.getElementById("clear-parent").addEventListener("click", () => {
    signaturePadParent.clear();
    if (!signaturePadStudent.isEmpty() && !signaturePadParent.isEmpty()) {
      save_signatures.innerHTML = "Generate enrollment";
    } else {
      save_signatures.innerHTML = "Save";
    }
  });

  signaturePadParent.addEventListener("afterUpdateStroke", () => {
    if (!signaturePadStudent.isEmpty() && !signaturePadParent.isEmpty()) {
      save_signatures.innerHTML = "Generate enrollment";
    } else {
      save_signatures.innerHTML = "Save";
    }
  });

  signaturePadStudent.addEventListener("afterUpdateStroke", () => {
    if (!signaturePadStudent.isEmpty() && !signaturePadParent.isEmpty()) {
      save_signatures.innerHTML = "Generate enrollment";
    } else {
      save_signatures.innerHTML = "Save";
    }
  });

  loadSignatures();
  save_signatures.addEventListener("click", function () {
    save_signatures.disabled = true;
    if (!signaturePadStudent.isEmpty() && !signaturePadParent.isEmpty()) {
      if (!gradeSelected) {
        save_signatures.disabled = false;
        alert(
          "You must select a last degree completed to generate the enrollment"
        );
        return;
      }

      var element = document.getElementById("content-pdf");
      var opt = {
        margin: [0.2, 0, 0, 0],
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
        .outputPdf("blob", "enrollment.pdf")
        .then((response) => {
          document.getElementById("modal-contraseña").style.display = "none";
          sendSignatures(response);
        });
    } else if (
      !signaturePadStudent.isEmpty() ||
      !signaturePadParent.isEmpty()
    ) {
      document.getElementById("modal-contraseña").style.display = "none";
      sendSignatures();
    } else {
      if (!gradeSelected) {
        save_signatures.disabled = false;
        alert(
          "You must select a last degree completed to generate the enrollment"
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
  formData.append(
    "signature_parent",
    JSON.stringify(signaturePadParent.toData())
  );
  formData.append(
    "signature_student",
    JSON.stringify(signaturePadStudent.toData())
  );
  formData.append("student_user_id", student_user_id);
  formData.append("partner_user_id", partner_user_id);
  formData.append("grade_selected", gradeSelected);

  if (doc) {
    formData.append("document", doc, "enrollment.pdf");
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
}


function loadSignatures() {
  const XHR = new XMLHttpRequest();
  XHR.open("POST", ajax_object.ajax_url, true);
  XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  XHR.responseType = "text";
  XHR.send(
    "action=load_signatures_data"
  );
  XHR.onload = function () {
    if (XHR.status === 200) {
      let grade_selected = JSON.parse(XHR.responseText).grade_selected;

      let parent_signature = JSON.parse(XHR.responseText).parent_signature;
      if (parent_signature.length > 0) {
        signaturePadParent.fromData(parent_signature);
        signaturePadParent.off();
        document.getElementById("clear-parent").style.display = 'none';
      }

      let student_signature = JSON.parse(XHR.responseText).student_signature;
      if (student_signature.length > 0) {
        signaturePadStudent.fromData(student_signature);
        signaturePadStudent.off();
        document.getElementById("clear-student").style.display = 'none';
      }

      if (grade_selected) {
        updateGrade(grade_selected);
      }
      
    }
  };
}
