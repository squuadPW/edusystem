document.addEventListener("DOMContentLoaded", function () {
  var openModalBtn = document.getElementById("open-upload-modal");
  var modal = document.getElementById("upload-modal");
  if (openModalBtn && modal) {
    openModalBtn.addEventListener("click", function (e) {
      e.preventDefault();
      modal.style.display = "block";
    });
  }

  var closeBtns = document.querySelectorAll(".modal-close");
  closeBtns.forEach(function (btn) {
    btn.addEventListener("click", function () {
      modal.style.display = "none";
    });
  });

  if (document.getElementById("program-identificator")) {
    let timeout;
    let currentPlansData = window.initialPlansData || [];

    function updatePaymentPlanDetails(selectedPlanId) {
      const detailsElement = document.getElementById(
        "details-payment-plan-element"
      );
      const detailsContainer = document.getElementById("details-payment-plan");

      if (!selectedPlanId) {
        detailsElement.style.display = "none";
        detailsContainer.innerHTML = "";
        return;
      }

      const planWrapper = currentPlansData.find(
        (p) => (p.plan.identificator || p.plan.id) == selectedPlanId
      );

      if (planWrapper) {
        const p = planWrapper.plan;
        const fees = planWrapper.fees || [];
        const quotes = planWrapper.quote_rules || [];
        const currency = p.currency || "$";

        let html = `
          <p><strong>Name:</strong> ${p.name}</p>
          <p><strong>Description:</strong> ${p.description}</p>
          <p><strong>Regular Price:</strong> ${currency}${p.total_price}</p>
        `;

        if (fees.length > 0) {
          html += `<label style="margin-top:10px; display:block;"><b>Fees</b></label>`;
          fees.forEach((fee) => {
            html += `<p style="margin-left:10px;">• ${fee.name}: ${
              fee.currency || currency
            }${fee.price}</p>`;
          });
        }

        if (quotes.length > 0) {
          html += `<label style="margin-top:10px; display:block;"><b>Payment Options</b></label>`;
          quotes.forEach((quote) => {
            // Lógica para que se vea más profesional
            const qty = parseInt(quote.quotas_quantity);
            const label =
              qty === 1 ? "Single installment" : `${qty} Installments`;
            html += `<p style="margin-left:10px;">• ${quote.name}: <strong>${label}</strong></p>`;
          });
        }

        detailsContainer.innerHTML = html;
        detailsElement.style.display = "block";
      }
    }

    function updateSubprograms() {
      const planSelect = document.getElementById("payment-plan-identificator");
      const selectedPlanId = planSelect.value;
      const subprogramElement = document.getElementById("subprogram-element");
      const subprogramSelect = document.getElementById("subprogram-id");
      const selectedSubprogramId = window.selectedSubprogramId || "";

      subprogramSelect.innerHTML = '<option value="">Select an option</option>';
      subprogramElement.style.display = "none";

      updatePaymentPlanDetails(selectedPlanId);

      if (!selectedPlanId) return;

      const newSelectedPlan = currentPlansData.find(
        (p) => (p.plan.identificator || p.plan.id) == selectedPlanId
      );

      let selectedPlan = newSelectedPlan ? newSelectedPlan.plan : null;
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
            option.text = `${subs[key].name}`;
            if (option.value == selectedSubprogramId) option.selected = true;
            subprogramSelect.appendChild(option);
            subCount++;
          }
        }
        if (subCount > 0) subprogramElement.style.display = "block";
      }
    }

    if (document.getElementById("payment-plan-identificator").value) {
      updateSubprograms();
    }

    document
      .getElementById("program-identificator")
      .addEventListener("change", function (e) {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
          document.getElementById("scholarship-element").style.display = "none";
          document.getElementById("subprogram-element").style.display = "none";
          document.getElementById(
            "details-payment-plan-element"
          ).style.display = "none";
          document.getElementById("details-payment-plan").innerHTML = "";

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
                plans.forEach((plak) => {
                  let p = plak.plan ? plak.plan : plak;
                  var option = document.createElement("option");
                  option.value = p.identificator || p.id;
                  option.text = `${p.name || "Plan"}${
                    p.description ? " (" + p.description + ")" : ""
                  } - ${
                    p.total_price
                      ? `${p.currency ? p.currency : "$"}${p.total_price}`
                      : ""
                  }`;
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

    document
      .getElementById("payment-plan-identificator")
      .addEventListener("change", updateSubprograms);
  }
});
