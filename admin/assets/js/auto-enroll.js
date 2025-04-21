document.addEventListener("DOMContentLoaded", () => {
  let show_button = false;
  const handleSelectChange = () => {
    document.getElementById("enroll-button").style.display = 'none';

    const academicPeriodSelect = document.querySelector(
      'select[name="academic_period"]'
    );
    const academicPeriodCutSelect = document.querySelector(
      'select[name="academic_period_cut"]'
    );

    const academicPeriod = academicPeriodSelect?.value || "";
    const academicPeriodCut = academicPeriodCutSelect?.value || "";

    if (!academicPeriod || !academicPeriodCut) return;

    const XHR = new XMLHttpRequest();
    XHR.open(
      "POST",
      `${ajax_object.url}?action=load_auto_enroll_students`,
      true
    );
    XHR.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    XHR.responseType = "json";

    const params = new URLSearchParams({
      action: "load_auto_enroll_students",
      academic_period: academicPeriod,
      academic_period_cut: academicPeriodCut,
    });

    XHR.onload = () => {
      if (XHR.status === 200 && XHR.response && XHR.response) {
        const students = XHR.response;
        if (students.length > 0) {
          show_button = true;
        } else {
          show_button = false;
        }
        renderStudentsList(students);
      }
    };

    XHR.send(params.toString());
  };

  // Función para renderizar la lista de estudiantes
  const renderStudentsList = (students) => {
    const container = document.querySelector(".admin-add-offer");
    if (!container) {
      console.error("Contenedor principal no encontrado");
      return;
    }

    // Limpiar lista existente
    const oldList = container.querySelector("#students-list");
    if (oldList) oldList.remove();

    // Crear elementos
    const listContainer = document.createElement("div");
    listContainer.id = "students-list";
    listContainer.innerHTML = `
          <h3 style="margin:20px 0 10px 0;color:#333;">Students to enroll</h3>
          <ul style="list-style:none;padding:0;border:1px solid #ddd;border-radius:4px;">
            ${students
              .map(
                (student) => `
              <li style="padding:10px;border-bottom:1px solid #ddd;background:#f9f9f9;">
                ${[
                  student.name,
                  student.middle_name,
                  student.last_name,
                  student.middle_last_name,
                  '-',
                  student.next_enrollment,
                ]
                  .filter(Boolean)
                  .join(" ")}
              </li>
            `
              )
              .join("")}
          </ul>
        `;

    // Buscar punto de inserción seguro
    const insertionPoint = container.querySelector("#dashboard-widgets");
    if (insertionPoint) {
      insertionPoint.insertAdjacentElement("afterend", listContainer);
    } else {
      // Fallback: Insertar al final del contenedor
      container.appendChild(listContainer);
    }

    if (show_button) {
      document.getElementById("enroll-button").style.display = 'flex';
    } else {
      document.getElementById("enroll-button").style.display = 'none';
    }
  };

  // Configurar listeners
  const academicPeriodSelect = document.querySelector(
    'select[name="academic_period"]'
  );
  const academicPeriodCutSelect = document.querySelector(
    'select[name="academic_period_cut"]'
  );

  [academicPeriodSelect, academicPeriodCutSelect].forEach((select) => {
    select?.addEventListener("change", handleSelectChange);
  });
});
