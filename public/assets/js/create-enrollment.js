let signaturePadStudent;
let signaturePadParent;

function resizeCanvas(canvasId) {
  const canvas = document.getElementById(canvasId);
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

save_signatures.addEventListener("click", function () {
  if (!signaturePadStudent.isEmpty() && !signaturePadParent.isEmpty()) {
    var element = document.getElementById("content-pdf");
    var opt = {
      margin: 0,
      image: { type: "jpeg", quality: 0.98 },
      jsPDF: { unit: "in", format: "a4", orientation: "portrait" },
      pagebreak: { after: ["#student-information", "#tuition-payment"] },
    };

    // New Promise-based usage:
    html2pdf().set(opt).from(element).save();
    html2pdf().set(opt).from(element).outputPdf('blob', 'enrollment.pdf').then((response => {
      sendSignatures(response);
    }))
  } else {
    sendSignatures();
  }
});

function sendSignatures(doc = null) {
  console.log(doc)
    student_user_id = document.querySelector('input[name="student_user_id"]').value;
    partner_user_id = document.querySelector('input[name="parent_user_id"]').value;
    const formData = new FormData();
    formData.append("action", "create_enrollment_document");
    formData.append("signature_parent", signaturePadParent.toData());
    formData.append("signature_student", signaturePadStudent.toData());
    formData.append("student_user_id", student_user_id);
    formData.append("partner_user_id", partner_user_id);

    if (doc) {
      formData.append("document", doc, "enrollment.pdf");
    }

    const XHR = new XMLHttpRequest();
    XHR.open("POST", `${ajax_object.ajax_url}?action=create_enrollment_document`, true);
    XHR.send(formData); // Remove the Content-type header

    XHR.onload = function () {
        if (XHR.status === 200) {
            // Handle successful response
        }
    };
  }
