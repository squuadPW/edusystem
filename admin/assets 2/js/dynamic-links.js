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

    function getSelectedValues(selectEl) {
      if (!selectEl) return [];
      return Array.from(selectEl.selectedOptions || []).map(
        (option) => option.value,
      );
    }

    function updatePaymentPlanDetails(selectedPlanIds) {
      const detailsElement = document.getElementById(
        "details-payment-plan-element",
      );
      const detailsContainer = document.getElementById("details-payment-plan");

      if (!selectedPlanIds || selectedPlanIds.length === 0) {
        detailsElement.style.display = "none";
        detailsContainer.innerHTML = "";
        return;
      }
      let html = "";

      selectedPlanIds.forEach((selectedPlanId) => {
        const planWrapper = currentPlansData.find(
          (p) => (p.plan.identificator || p.plan.id) == selectedPlanId,
        );

        if (!planWrapper) {
          return;
        }

        const p = planWrapper.plan;
        const fees = planWrapper.fees || [];
        const quotes = planWrapper.quote_rules || [];
        const currency = p.currency || "$";

        html += `
            <div style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px;">
                <p><strong>Name:</strong> ${p.name}</p>
                <p><strong>Description:</strong> ${p.description}</p>
                <p><strong>Regular Price:</strong> ${currency}${p.total_price}</p>
            </div>
        `;

        if (fees.length > 0) {
          html += `<label><b>Fees</b></label>`;
          fees.forEach((fee) => {
            html += `<p style="margin-left:10px; font-size: 0.9em;">• ${fee.name}: ${fee.currency || currency}${fee.price}</p>`;
          });
        }

        if (quotes.length > 0) {
          html += `<label style="margin-top:10px; display:block;"><b>Payment Options</b></label>`;
          quotes.forEach((quote) => {
            const qty = parseInt(quote.quotas_quantity);
            const freqVal = parseInt(quote.frequency_value);

            // Mapeo de Starts
            const startsMap = {
              registration: "By registering",
              academic_period: "At the beginning of the academic period",
            };
            const startsText =
              startsMap[quote.start_charging] || quote.start_charging;

            const frequencyText =
              freqVal === 0 || qty === 1
                ? "One-time payment"
                : `Every ${freqVal} ${quote.type_frequency}${freqVal > 1 ? "s" : ""}`;

            const installmentLabel =
              qty === 1 ? "Single installment" : `${qty} Installments`;
            const amountLabel = qty === 1 ? "Payment:" : "Installment:";

            const getSaleValue = (sale, regular) =>
              sale === "" || sale === null || sale === undefined
                ? regular
                : sale;

            const initialVal = getSaleValue(
              quote.initial_payment_sale,
              quote.initial_payment,
            );
            const quoteVal = getSaleValue(
              quote.quote_price_sale,
              quote.quote_price,
            );
            const finalVal = getSaleValue(
              quote.final_payment_sale,
              quote.final_payment,
            );

            const hasInitialDiscount =
              quote.initial_payment_sale !== "" &&
              quote.initial_payment_sale !== null &&
              initialVal != quote.initial_payment;
            const hasQuoteDiscount =
              quote.quote_price_sale !== "" &&
              quote.quote_price_sale !== null &&
              quoteVal != quote.quote_price;
            const hasFinalDiscount =
              quote.final_payment_sale !== "" &&
              quote.final_payment_sale !== null &&
              finalVal != quote.final_payment;

            html += `
                    <div style="background: #f9f9f9; border: 1px solid #e5e5e5; border-radius: 4px; padding: 10px; margin: 10px 0;">
                        <div style="margin-bottom: 5px;"><strong>${quote.name}</strong> (${installmentLabel})</div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 8px; font-size: 11px; line-height: 1.2;">
                            <div><strong>Frequency:</strong> ${frequencyText}</div>
                            <div><strong>Starts:</strong> ${startsText}</div>
                            <div>
                                <strong>Initial:</strong> ${currency}${initialVal}
                                ${hasInitialDiscount ? `<span style="text-decoration:line-through; color: #999;">${currency}${quote.initial_payment}</span>` : ""}
                            </div>
                            <div>
                                <strong>${amountLabel}</strong> ${currency}${quoteVal}
                                ${hasQuoteDiscount ? `<span style="text-decoration:line-through; color: #999;">${currency}${quote.quote_price}</span>` : ""}
                            </div>
                            <div>
                                <strong>Final:</strong> ${currency}${finalVal}
                                ${hasFinalDiscount ? `<span style="text-decoration:line-through; color: #999;">${currency}${quote.final_payment}</span>` : ""}
                            </div>
                        </div>
                    </div>`;
          });
        }
      });

      detailsContainer.innerHTML = html;
      detailsElement.style.display = "block";
    }

    function updateSubprograms() {
      const planSelect = document.getElementById("payment-plan-identificator");
      const selectedPlanIds = getSelectedValues(planSelect);
      const subprogramElement = document.getElementById("subprogram-element");
      const subprogramSelect = document.getElementById("subprogram-id");
      const selectedSubprogramId = window.selectedSubprogramId || "";

      subprogramSelect.innerHTML = '<option value="">Select an option</option>';
      subprogramElement.style.display = "none";

      updatePaymentPlanDetails(selectedPlanIds);

      if (!selectedPlanIds || selectedPlanIds.length !== 1) {
        return;
      }

      const newSelectedPlan = currentPlansData.find(
        (p) => (p.plan.identificator || p.plan.id) == selectedPlanIds[0],
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

    if (getSelectedValues(document.getElementById("payment-plan-identificator")).length) {
      updateSubprograms();
    }

    document
      .getElementById("program-identificator")
      .addEventListener("change", function (e) {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
          const selectedProgramIds = getSelectedValues(e.target);
          document.getElementById("scholarship-element").style.display = "none";
          document.getElementById("subprogram-element").style.display = "none";
          document.getElementById(
            "details-payment-plan-element",
          ).style.display = "none";
          document.getElementById("details-payment-plan").innerHTML = "";

          if (!selectedProgramIds.length) {
            const stateSelect = document.getElementById(
              "payment-plan-identificator",
            );
            stateSelect.innerHTML = "";
            return;
          }

          var xhr = new XMLHttpRequest();
          xhr.open("POST", wp_ajax.url, true);
          xhr.setRequestHeader(
            "Content-Type",
            "application/x-www-form-urlencoded",
          );
          xhr.onload = function () {
            if (xhr.status === 200) {
              var raw = JSON.parse(xhr.responseText);
              var groups = raw.data?.groups || raw.data || [];
              var flatPlans = raw.data?.plans || [];

              if (Array.isArray(groups) && groups.length > 0) {
                currentPlansData = groups.flatMap((group) => group.plans || []);
              } else {
                currentPlansData = flatPlans.length ? flatPlans : [];
              }

              var stateSelect = document.getElementById(
                "payment-plan-identificator",
              );
              stateSelect.innerHTML = "";

              if (Array.isArray(groups) && groups.length > 0) {
                groups.forEach((group) => {
                  const label = group.program
                    ? `${group.program.name || "Program"}${
                        group.program.description
                          ? " (" + group.program.description + ")"
                          : ""
                      }`
                    : "Program";
                  const optgroup = document.createElement("optgroup");
                  optgroup.label = label;

                  (group.plans || []).forEach((plak) => {
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
                    optgroup.appendChild(option);
                  });

                  if (optgroup.children.length > 0) {
                    stateSelect.appendChild(optgroup);
                  }
                });
              } else if (Array.isArray(flatPlans)) {
                flatPlans.forEach((plak) => {
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
              }

              if (stateSelect.options.length > 0) {
                document.getElementById("scholarship-element").style.display =
                  "block";
              }
            }
          };
          xhr.send(
            "action=get_payments_plans_by_program&program_id=" +
              encodeURIComponent(selectedProgramIds.join(",")),
          );
        }, 50);
      });

    document
      .getElementById("payment-plan-identificator")
      .addEventListener("change", updateSubprograms);
  }
});
