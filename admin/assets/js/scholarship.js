document.addEventListener("DOMContentLoaded", function () {
  approved_status = document.getElementById("approved_scholarship");

  if (approved_status) {
    approved_status.addEventListener("click", (e) => {
      scholarship_id = approved_status.getAttribute("data-id");
      let title = approved_status.getAttribute("data-title");
      let message = approved_status.getAttribute("data-message");

      document.getElementById("title-modal-status-scholarship").textContent =
        title;
      document.getElementById("message-modal-status-scholarship").textContent =
        message;
      document.getElementById("scholarship_id").value = scholarship_id;
      document.getElementById("modalStatusScholarship").style.display = "block";
    });
  }

  pre_scholarship = document.getElementById("pre-scholarship");
  if (pre_scholarship) {
    pre_scholarship.addEventListener("click", (e) => {
      document.getElementById("pre-scholarship-modal").style.display = "block";
    });

    var modalClosePreScholarship = document.querySelectorAll(
      "#pre-scholarship-exit-icon, #pre-scholarship-exit-button"
    );
    if (modalClosePreScholarship) {
      modalClosePreScholarship.forEach(function (element) {
        element.addEventListener("click", function () {
          document.getElementById("pre-scholarship-form").reset();
          document.getElementById("pre-scholarship-modal").style.display =
            "none";
        });
      });
    }
  }

  assign_scholarship = document.getElementById("assign-scholarship");
  if (assign_scholarship) {
    assign_scholarship.addEventListener("click", (e) => {
      document.getElementById("assign-scholarship-modal").style.display =
        "block";
    });

    var modalClosePreScholarship = document.querySelectorAll(
      "#assign-scholarship-exit-icon, #assign-scholarship-exit-button"
    );
    if (modalClosePreScholarship) {
      modalClosePreScholarship.forEach(function (element) {
        element.addEventListener("click", function () {
          document.getElementById("assign-scholarship-form").reset();
          document.getElementById("assign-scholarship-modal").style.display =
            "none";
        });
      });
    }
  }

  document.querySelectorAll(".modal-close").forEach((close) => {
    close.addEventListener("click", (e) => {
      document.getElementById("modalStatusScholarship").style.display = "none";
    });
  });
});

jQuery(document).ready(function ($) {
  // Agregar estilos CSS
  $("<style>")
    .text(
      `
            .select2-result {
                padding: 8px 12px;
                border-bottom: 1px solid #eee;
            }
            .select2-result-main {
                font-weight: 600;
                color: #333;
                margin-bottom: 4px;
            }
            .select2-result-description {
                font-size: 0.9em;
                color: #666;
            }
            .select2-container--default .select2-results__option--highlighted[aria-selected] {
                background-color: #f8f9fa;
                color: #333;
            }
            .select2-container--default .select2-results__option[aria-selected=true] {
                background-color: #e9ecef;
            }
        `
    )
    .appendTo("head");

  $(".js-example-basic")
    .select2({
      ajax: {
        url: `${scholarship.url}`, // URL del servicio
        dataType: "json", // Tipo de datos esperado
        type: "POST",
        delay: 250, // Retraso en ms después de teclear
        data: function (params) {
          // Parámetros de búsqueda
          return {
            q: params.term, // Término de búsqueda
            action: scholarship.action, // Término de búsqueda
            return_id: true
          };
        },
        processResults: function (data, params) {
          params.page = params.page || 1;

          return {
            results: data.items, // Resultados formateados
          };
        },
        cache: true,
      },
      minimumInputLength: 2, // Mínimo de caracteres para buscar
      placeholder: "Buscar...", // Texto placeholder
      templateResult: formatResult, // Función para formatear resultados
    })
    .on("change", function (e) {
      // Obtener el valor seleccionado
      const selectedValue = $(this).val();
      console.log(selectedValue)
      document.getElementById("student_id").value = selectedValue;

      // Aquí puedes hacer lo que necesites con el valor seleccionado
      // Por ejemplo, hacer una llamada AJAX o actualizar otros elementos
    });

  // Función para formatear la presentación de resultados
  function formatResult(repo) {
    if (repo.loading) return repo.text;

    const $container = $('<div class="select2-result">');
    const $main = $('<div class="select2-result-main">').text(repo.text);
    const $description = $('<div class="select2-result-description">').text(
      repo.description || ""
    );

    $container.append($main);
    if (repo.description) {
      $container.append($description);
    }

    return $container;
  }
});
