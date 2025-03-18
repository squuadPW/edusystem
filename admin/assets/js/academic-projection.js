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
    
        // 1. Mostrar temporalmente la tabla
        const originalDisplay = table.style.display;
        table.style.display = "block";
    
        // 2. Capturar como imagen
        const canvas = await html2canvas(table, { scale: 3 });
        const imgData = canvas.toDataURL("image/png", 1.0);
    
        // 3. Crear PDF en A4 portrait
        const doc = new jsPDF({
            orientation: "portrait",  // Cambiado a vertical
            unit: "mm",
            format: "a4"
        });
    
        // 4. Configuración de márgenes y dimensiones para portrait
        const pageWidth = 210;   // Ancho A4 portrait (mm)
        const pageHeight = 297;  // Alto A4 portrait (mm)
        const margin = 10;       // Márgenes de 10mm
    
        // Convertir píxeles a mm (96dpi)
        let imgWidth = canvas.width * 0.264583;
        let imgHeight = canvas.height * 0.264583;
    
        // Área disponible para el contenido
        const maxWidth = pageWidth - (2 * margin);
        const maxHeight = pageHeight - (2 * margin);
    
        // Ajustar al ancho máximo manteniendo relación de aspecto
        if (imgWidth > maxWidth) {
            const ratio = maxWidth / imgWidth;
            imgWidth = maxWidth;
            imgHeight *= ratio;
        }
    
        // Ajustar altura si aún excede el máximo
        if (imgHeight > maxHeight) {
            const ratio = maxHeight / imgHeight;
            imgHeight = maxHeight;
            imgWidth *= ratio;
        }
    
        // Posición en la parte superior
        const xPos = margin; // Margen izquierdo
        const yPos = margin; // Margen superior
    
        // 5. Añadir imagen al PDF
        doc.addImage(imgData, "PNG", xPos, yPos, imgWidth, imgHeight);
        doc.save("tabla.pdf");
    
        // 6. Restaurar visibilidad original
        table.style.display = originalDisplay;
        modalGeneratingGrades.style.display = 'none';
        download_grades.disabled = false;
        document.body.classList.remove("modal-open");
        location.reload();
    });
}
