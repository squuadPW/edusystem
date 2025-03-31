document.addEventListener('DOMContentLoaded', function() {
  // Seleccionar todos los botones con data-visual
  const buttons = document.querySelectorAll('.form-group button[data-visual]');
  
  buttons.forEach(button => {
      button.addEventListener('click', function() {
          // Obtener el contenido de data-visual
          const content = this.dataset.visual;
          
          // Verificar si el editor visual está activo
          if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
              // Insertar contenido en el editor visual
              tinymce.get('content').execCommand('mceInsertContent', false, content);
          } else {
              // Insertar en el textarea (modo texto)
              const textarea = document.getElementById('message');
              const startPos = textarea.selectionStart;
              const endPos = textarea.selectionEnd;
              
              // Insertar el contenido en la posición del cursor
              textarea.value = textarea.value.substring(0, startPos) 
                              + content 
                              + textarea.value.substring(endPos);
              
              // Actualizar la posición del cursor
              textarea.selectionStart = textarea.selectionEnd = startPos + content.length;
              
              // Disparar evento para actualizar el editor
              textarea.dispatchEvent(new Event('input'));
          }
      });
  });
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
            formGroup.style.display = "block";

            document.querySelector('input[name="type"]').value = '1';

            document.querySelector('select[name="academic_period"]').required = true;
            document.querySelector('select[name="academic_period_cut_filter"]').required = true;
            document.querySelector('select[name="academic_period_cut"]').required = true;
            document.querySelector('input[name="email_student"]').required = false;
        } else {
            const formOthers = document.getElementById("by_group");
            formOthers.style.display = "none";

            const formMe = document.getElementById("by_email");
            formMe.style.display = "block";

            document.querySelector('input[name="type"]').value = '2';

            document.querySelector('select[name="academic_period"]').required = false;
            document.querySelector('select[name="academic_period_cut_filter"]').required = false;
            document.querySelector('select[name="academic_period_cut"]').required = false;
            document.querySelector('input[name="email_student"]').required = true;
        }
    });
});


var modalCloseElementSummaryEmail = document.querySelectorAll('#summary-email-exit-icon, #summary-email-exit-button');
if (modalCloseElementSummaryEmail) {
  modalCloseElementSummaryEmail.forEach(function(element) {
    element.addEventListener('click', function() {
      document.getElementById('list-students-email').innerHTML = '';
      document.getElementById('summary-email-modal').style.display = 'none';
    });
  });
}

var summaryEmailButton = document.getElementById('summary-email');
if (summaryEmailButton) {
    summaryEmailButton.addEventListener('click', function() {
        document.getElementById('summary-email-modal').style.display = 'block';
        document.getElementById('list-students-email').innerHTML = '';
        var studentsSend = document.getElementById('list-students-email');
        var li = document.createElement('li');
        li.innerText = `Loading...`;
        studentsSend.appendChild(li);
        var total_send = document.getElementById('total-send');
        total_send.innerText = 0;

        let academic_period = document.querySelector('select[name="academic_period"]').value;
        let cut = document.querySelector('select[name="academic_period_cut"]').value;
        let filter = document.querySelector('select[name="academic_period_cut_filter"]').value;
        let email_student = document.querySelector('input[name="email_student"]').value;
        let type = document.querySelector('input[name="type"]').value;

        const XHR = new XMLHttpRequest();
        XHR.open("POST", summary_email.url, true);
        XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        XHR.responseType = "json";
        XHR.send(`action=${summary_email.action}&academic_period=${academic_period}&cut=${cut}&filter=${filter}&email_student=${email_student}&type=${type}`);
        XHR.onload = function () {
          if (this.readyState == "4" && XHR.status === 200) {
            document.getElementById('list-students-email').innerHTML = '';
            let students = XHR.response.students;
            students.forEach(student => {
              var li = document.createElement('li');
              li.innerText = `${student.last_name} ${student.middle_last_name} ${student.name} ${student.middle_name}`;
              studentsSend.appendChild(li);
            });

            if (students.length == 0) {
              var li = document.createElement('li');
              li.innerText = `No students found.`;
              studentsSend.appendChild(li);
            }

            var total_send = document.getElementById('total-send');
            total_send.innerText = students.length;
          }
        };
    });
}

var summaryEmailButtonConfirm = document.getElementById('summary-email-button');
if (summaryEmailButtonConfirm) {
    summaryEmailButtonConfirm.addEventListener('click', function() {
      document.getElementById('list-students-email').innerHTML = '';
      document.getElementById('summary-email-modal').style.display = 'none';
      document.getElementById('summary-email-send').click();
    });
}

