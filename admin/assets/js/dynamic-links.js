document.addEventListener("DOMContentLoaded", function () {
  var openModalBtn = document.getElementById("open-upload-modal");
  var modal = document.getElementById("upload-modal");
  if (openModalBtn && modal) {
    openModalBtn.addEventListener("click", function (e) {
      e.preventDefault();
      modal.style.display = "block";
    });
  }
  // Cerrar modal con los botones de salida
  var closeBtns = document.querySelectorAll(".modal-close");
  closeBtns.forEach(function (btn) {
    btn.addEventListener("click", function () {
      modal.style.display = "none";
    });
  });

  if (document.getElementById("program-identificator")) {
    let timeout;

    // 1. Inicializamos con los datos que PHP ya cargó
    let currentPlansData = window.initialPlansData || [];

    // Función reutilizable para llenar los subprogramas
    function updateSubprograms() {
      const selectedPlanId = document.getElementById(
        "payment-plan-identificator"
      ).value;
      const subprogramElement = document.getElementById("subprogram-element");
      const subprogramSelect = document.getElementById("subprogram-id");

      // USAMOS LA VARIABLE GLOBAL DE WINDOW
      const selectedSubprogramId = window.selectedSubprogramId || "";

      subprogramSelect.innerHTML = '<option value="">Select an option</option>';
      subprogramElement.style.display = "none";

      if (!selectedPlanId) return;

      const selectedPlan = currentPlansData.find(
        (p) => (p.identificator || p.id) == selectedPlanId
      );

      if (selectedPlan && selectedPlan.subprogram) {
        let subCount = 0;
        const subs =
          typeof selectedPlan.subprogram === "string"
            ? JSON.parse(selectedPlan.subprogram)
            : selectedPlan.subprogram;

        for (const key in subs) {
          if (subs[key].is_active == 1) {
            var option = document.createElement("option");
            option.value = key;
            option.text = subs[key].name;

            if (option.value == selectedSubprogramId) {
              option.selected = true;
            }

            subprogramSelect.appendChild(option);
            subCount++;
          }
        }

        if (subCount > 0) {
          subprogramElement.style.display = "block";
        }
      }
    }

    // Ejecutar al cargar la página por si ya hay un plan seleccionado
    if (document.getElementById("payment-plan-identificator").value) {
      updateSubprograms();
    }

    // Evento cuando cambia el programa (AJAX)
    document
      .getElementById("program-identificator")
      .addEventListener("change", function (e) {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
          document.getElementById("scholarship-element").style.display = "none";
          document.getElementById("subprogram-element").style.display = "none";

          var xhr = new XMLHttpRequest();
          xhr.open("POST", wp_ajax.url, true);
          xhr.setRequestHeader(
            "Content-Type",
            "application/x-www-form-urlencoded"
          );
          xhr.onload = function () {
            if (xhr.status === 200) {
              var raw = JSON.parse(xhr.responseText);
              var plans = raw.data?.plans || raw.data || raw;
              currentPlansData = plans;

              var stateSelect = document.getElementById(
                "payment-plan-identificator"
              );
              stateSelect.innerHTML =
                '<option value="">Select an option</option>';

              if (Array.isArray(plans)) {
                plans.forEach((p) => {
                  var option = document.createElement("option");
                  option.value = p.identificator || p.id;
                  option.text =
                    (p.name || "Plan") +
                    (p.description ? " (" + p.description + ")" : "");
                  stateSelect.appendChild(option);
                });
                if (plans.length > 0)
                  document.getElementById("scholarship-element").style.display =
                    "block";
              }
            }
          };
          xhr.send(
            "action=get_payments_plans_by_program&program_id=" + e.target.value
          );
        }, 50);
      });

    // Evento cuando cambia el plan de pago
    document
      .getElementById("payment-plan-identificator")
      .addEventListener("change", updateSubprograms);

  }
});
