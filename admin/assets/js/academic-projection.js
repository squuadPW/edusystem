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

let download_grades = document.getElementById("download-grades");
let modalGeneratingGrades = document.getElementById("modalGeneratingGrades");

if (download_grades) {
    download_grades.addEventListener("click", async (e) => {
        document.body.classList.add("modal-open");
        modalGeneratingGrades.style.display = 'block';
        download_grades.disabled = true;
        
        const { jsPDF } = window.jspdf;
        const table = document.getElementById("template_certificate");

        // 1. Mostrar tabla temporalmente
        const originalDisplay = table.style.display;
        table.style.display = "block";

        // 2. Captura optimizada
        const canvas = await html2canvas(table, {
            scale: 1.5,
            useCORS: true,
            logging: false,
            backgroundColor: "#FFFFFF"
        });

        // 3. Conversión a JPEG comprimido
        const imgData = canvas.toDataURL("image/jpeg", 0.7);

        // 4. Crear PDF con compresión
        const doc = new jsPDF({
            orientation: "portrait",
            unit: "mm",
            format: "a4",
            compress: true
        });

        // 5. Cálculo de dimensiones y posición
        const pageWidth = 210;
        const margin = 5;
        const maxContentWidth = pageWidth - (2 * margin);

        // Calcular dimensiones manteniendo relación de aspecto
        const imgRatio = canvas.width / canvas.height;
        let imgWidth = maxContentWidth;
        let imgHeight = maxContentWidth / imgRatio;

        // Posición superior izquierda con márgenes
        const xPos = margin;
        const yPos = margin;

        // 6. Añadir imagen en posición superior
        doc.addImage(
            imgData,
            "JPEG",
            xPos,       // Posición horizontal desde izquierda
            yPos,       // Posición vertical desde arriba
            imgWidth,  // Ancho ajustado
            imgHeight   // Alto proporcional
        );

        doc.save("calificaciones.pdf");

        // 7. Restaurar estado original
        table.style.display = originalDisplay;
        modalGeneratingGrades.style.display = 'none';
        download_grades.disabled = false;
        document.body.classList.remove("modal-open");
    });
}