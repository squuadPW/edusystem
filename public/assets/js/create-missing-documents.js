const url = new URL(window.location.href);
const missingParam = url.searchParams.get("missing");
document.body.classList.remove("modal-open");

if (missingParam) {

  const confirmCreateDocument = confirm(
    `It seems that you are missing some documents, would you like to sign the missing documents agreement?`
  );

  if (confirmCreateDocument) {
    document.getElementById("modal-content").style.display = "block";
    let signaturePadStudent;
    let user_student_id;
    loadStudentMissingDocuments(JSON.parse(missingParam));

    function resizeCanvas(canvasId) {
      const canvas = document.getElementById(canvasId);
      if (canvas) {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        let width, height;

        // Set different canvas sizes based on screen sizes
        if (window.matchMedia("(max-width: 768px)").matches) {
          // mobile
          width = 250;
          height = 100;
        } else {
          // laptop
          width = 324;
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
      resizeCanvas("signature-student-missing-documents");
    });

    window.addEventListener("orientationchange", function () {
      resizeCanvas("signature-student-missing-documents");
    });

    resizeCanvas("signature-student-missing-documents");

    // Create the SignaturePad objects after the canvas elements have been resized
    if (document.getElementById("signature-student-missing-documents")) {
      console.log('missing')
      document.body.classList.add("modal-open");
      signaturePadStudent = new SignaturePad(
        document.getElementById("signature-student-missing-documents")
      );
      save_signatures = document.getElementById("saveSignatures");

      document.getElementById("clear-student").addEventListener("click", () => {
        signaturePadStudent.clear();
        if (!signaturePadStudent.isEmpty()) {
          save_signatures.disabled = false;
        } else {
          save_signatures.disabled = true;
        }
      });

      signaturePadStudent.addEventListener("afterUpdateStroke", () => {
        if (!signaturePadStudent.isEmpty()) {
          save_signatures.disabled = false;
        } else {
          save_signatures.disabled = true;
        }
      });

      save_signatures.addEventListener("click", function () {
        save_signatures.disabled = true;
        if (!signaturePadStudent.isEmpty()) {
          var element = document.getElementById("content-pdf");
          var opt = {
            margin: [0.2, 0, 0, 0],
            filename: `Student Missing Documents Agreement.pdf`,
            image: { type: "jpeg", quality: 0.98 },
            jsPDF: { unit: "in", format: "letter", orientation: "portrait" },
            html2canvas: { scale: 2 }
          };

          // New Promise-based usage:
          html2pdf().set(opt).from(element).save();
          html2pdf()
            .set(opt)
            .from(element)
            .outputPdf("blob", "Student Missing Documents Agreement.pdf")
            .then((response) => {
              document.getElementById("modal-contraseña").style.display =
                "none";
              document.getElementById("modal-content").style.display = "none";
              // When the modal is closed, remove the `modal-open` class from the body element
              document.body.classList.remove("modal-open");
              sendSignatures(response);
            });
        }
      });
    }

    function sendSignatures(doc = null) {
      const formData = new FormData();
      formData.append("action", "create_enrollment_document");
      formData.append(
        "signature_student",
        JSON.stringify(signaturePadStudent.toData())
      );
      formData.append("id_student_to_use", user_student_id);
      formData.append("document_id", 'MISSING DOCUMENTS');

      if (doc) {
        formData.append("document", doc, "Student Missing Documents Agreement.pdf");
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

    function loadStudentMissingDocuments(student_id) {
      user_student_id = student_id[0];
      const formData = new FormData();
      formData.append("action", "get_student_missing_documents");
      formData.append("student_id", student_id[0]);
      const XHR = new XMLHttpRequest();
      XHR.open(
        "POST",
        `${ajax_object.ajax_url}?action=get_student_missing_documents`,
        true
      );
      XHR.send(formData); // Remove the Content-type header

      XHR.onload = function () {
        if (XHR.status === 200) {
          var result = JSON.parse(XHR.responseText);
          const listItems = result.data.documents;
          const student = result.data.student;

          // Select the <ul> element with the id "pending-documents"
          const pendingDocuments = document.querySelector('#pending-documents');
          const name = document.getElementById('name');
          const id_document = document.getElementById('id_document');

          name.innerHTML = `${student.name} ${student.middle_name} ${student.last_name} ${student.middle_last_name}`;
          id_document.innerHTML = `${student.id_document}`;
          // Loop through the <li> elements using a `forEach` loop
          listItems.forEach((listItem, index) => {
            // Create a new <li> element
            const newListItem = document.createElement('li');

            // Set the text content of the new <li> element to the text content of the current <li> element
            newListItem.textContent = `${index + 1}. ${listItem.document_id}`;

            // Append the new <li> element to the <ul> element
            pendingDocuments.appendChild(newListItem);
          });
        }
      };
    }
  } else {
    //
  }
}
