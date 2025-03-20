function academic_period_changed(key) {
  let current_period = document.querySelector(
    `input[name="current_period"]`
  ).value;
  let current_cut = document.querySelector(`input[name="current_cut"]`).value;
  let selected_period = document.querySelector(
    `select[name="academic_period[${key}]"]`
  ).value;
  let selected_cut = document.querySelector(
    `select[name="academic_period_cut[${key}]"]`
  ).value;
  document.querySelector(`input[name="completed[${key}]"]`).checked = true;

  if (current_period == selected_period && current_cut == selected_cut) {
    document.querySelector(`input[name="this_cut[${key}]"]`).value = 1;
    document.querySelector(
      `input[name="calification[${key}]"]`
    ).required = false;
    document.getElementById(`row[${key}]`).classList.add("current-period");
  } else {
    document.querySelector(`input[name="this_cut[${key}]"]`).value = 0;
    document.querySelector(
      `input[name="calification[${key}]"]`
    ).required = true;
    document.getElementById(`row[${key}]`).classList.remove("current-period");
  }
}

let preview_grades = document.getElementById("preview-grades");
if (preview_grades) {
    preview_grades.addEventListener("click", async (e) => {
      document.getElementById('modal-grades').style.display = 'block';
      document.body.classList.add("modal-open");
      setTimeout(() => {
        window.scrollTo(0, 0);
      }, 100);
    });
}

let close_modal_grades = document.getElementById("close-modal-grades");
if (close_modal_grades) {
    close_modal_grades.addEventListener("click", async (e) => {
      document.getElementById('modal-grades').style.display = 'none';
      document.body.classList.remove("modal-open");
      setTimeout(() => {
        window.scrollTo(0, 0);
      }, 100);
    });
}


let download_grades = document.getElementById("download-grades");
if (download_grades) {
    download_grades.addEventListener("click", async (e) => {
      download_grades.disabled = true;
      var element = document.getElementById("content-pdf");
      var opt = {
        margin: [0.2, 0, 0, 0],
        filename: 'califications.pdf',
        image: { type: "jpeg", quality: 0.98 },
        jsPDF: { unit: "in", format: "a4", orientation: "portrait" },
        html2canvas: { scale: 3 }
      };
  
      html2pdf().set(opt).from(element).save();
      download_grades.disabled = false;
    });
}