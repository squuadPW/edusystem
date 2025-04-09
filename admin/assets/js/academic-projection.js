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
          margin: [10, 0, 20, 0],
          filename: 'califications.pdf',
          image: { type: "jpeg", quality: 0.98 },
          jsPDF: { unit: "mm", format: "a4", orientation: "portrait" },
          html2canvas: { scale: 3 }
        };

        // Generar el PDF
        const pdf = await html2pdf().set(opt).from(element).toPdf().get('pdf');

        // Capturar el contenido del footer
        const footerElement = document.getElementById("colophon");
        footerElement.style.display = "block"; // Asegúrate de que el footer sea visible
        const canvas = await html2canvas(footerElement, { scale: 2 });
        const imgData = canvas.toDataURL("image/png");
        footerElement.style.display = "none"; // Ocultar el footer nuevamente

        // Agregar el footer manualmente
        const pageCount = pdf.internal.getNumberOfPages();
        for (let i = 1; i <= pageCount; i++) {
            pdf.setPage(i);
            const imgWidth = pdf.internal.pageSize.width; // Ancho de la imagen igual al ancho de la página
            const imgHeight = (canvas.height * imgWidth) / canvas.width; // Mantener la proporción
            const x = 0; // Posición X para que ocupe todo el ancho
            const y = pdf.internal.pageSize.height - imgHeight; // Posición Y para que esté en la parte inferior

            // Agregar la imagen del footer al PDF
            pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);
        }

        // Guardar el PDF
        pdf.save('califications.pdf'); // No se usa .then() aquí
        download_grades.disabled = false; // Habilitar el botón nuevamente
    });
}