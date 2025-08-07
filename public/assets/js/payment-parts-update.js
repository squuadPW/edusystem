document.addEventListener("DOMContentLoaded", () => {
  options_quotas = document.querySelectorAll(".options-quotas .option-quota");

  /**
   * Maneja la interacción del usuario con las opciones de cuotas en la interfaz.
   *
   * Este bloque de código itera sobre cada opción de cuota y agrega un evento de clic
   * que permite al usuario seleccionar una opción de cuota específica. Al hacer clic en
   * una opción, se obtienen los datos de la regla correspondiente y se genera la tabla
   * de pagos utilizando la función `payment_table`. Además, se actualiza el precio del
   * producto en el carrito de WooCommerce según la regla seleccionada, llamando a la
   * función `update_price_product_cart_quota_rule_js`.
   *
   * @param {NodeList} options_quotas - Lista de opciones de cuotas en la interfaz.
   *
   * @return {void} No retorna ningún valor.
   */
  options_quotas.forEach((option_quota) => {
    option_quota.addEventListener("click", function () {
      // Eliminar la clase 'checked' de todas las opciones
      options_quotas.forEach((opt) => {
        opt.querySelector(".option-rule").removeAttribute("checked");
      });

      // Añadir la clase 'checked' a la opción seleccionada
      option_quota.querySelector(".option-rule").setAttribute("checked", true);

      rule_id = option_quota.getAttribute("data-id");
      rule = document.getElementById(`data-rule-${rule_id}`)?.value;
      if (rule) {
        rule_data = JSON.parse(rule);
        payment_table(rule_data);

        table_payment = document.getElementById("table-payment");
        product_id = table_payment.getAttribute("data-product_id");
        update_price_product_cart_quota_rule_js(product_id, rule_id);
      }
    });
  });

  // Marcar automáticamente la primera opción de cuotas
  if (options_quotas.length > 0) {
    options_quotas[0].click(); // Simula un clic en la primera opción
  }
});

/**
 * Crea y muestra una tabla de pagos basada en los datos de una regla específica.
 * La tabla incluye información sobre las cuotas, fechas de pago y montos.
 *
 * @param {Object} rule_data - Objeto que contiene los datos de la regla.
 * @param {number} rule_data.id - Identificador único de la regla.
 * @param {number} rule_data.quotas_quantity - Cantidad de cuotas a mostrar.
 * @param {string} rule_data.type_frequency - Tipo de frecuencia de los pagos (día, mes, año).
 * @param {number} rule_data.frequency_value - Valor de la frecuencia de los pagos.
 * @param {number} rule_data.quote_price - Precio de cada cuota.
 * @param {number} rule_data.initial_payment - Precio inicial de la regla.
 * @param {number} rule_data.final_payment - Precio final de la regla.
 *
 * @return {void} No retorna ningún valor.
 *
 * La función genera una tabla de pagos que muestra las cuotas de una regla específica,
 * calculando las fechas y montos de cada cuota, y utilizando el precio inicial como el primer pago si está disponible.
 */
function payment_table(rule_data) {
    
    table_payment = document.getElementById("table-payment");
    table_payment.innerHTML = "";
    
    const text_total = table_payment.getAttribute("data-text_total");
    const headers = JSON.parse(
        table_payment.getAttribute("data-text_table_headers") ?? "{}"
    );

    // Crear tabla
    const table = document.createElement("table");
    table.setAttribute("data-rule_id", rule_data.id);
    table.className = "payment-parts-table mt-5";

    // Crear fila de encabezado
    const header_row = document.createElement("tr");
    // Crear encabezados
    headers.forEach((header_text) => {
        const th = document.createElement("th");
        th.className = "payment-parts-table-header";
        th.textContent = header_text;
        header_row.appendChild(th);
    });
    table.appendChild(header_row);

    // fecha para formato
    const opcions_date = { year: "numeric", month: "long", day: "numeric" };
    const discount_value = parseFloat(
        document.getElementById("discount_value").value ?? 0
    );

    quotas_quantity = rule_data.quotas_quantity;
    initial_payment = parseFloat(rule_data.initial_payment);
    final_payment = parseFloat(rule_data.final_payment);

    if ( initial_payment > 0 ) quotas_quantity++;
    if ( final_payment > 0 ) quotas_quantity++;

    if (discount_value > 0) {
        initial_payment = initial_payment - ( (initial_payment * discount_value) / 100 );
        final_payment = final_payment - ( (final_payment * discount_value) / 100 );
    }

    // Crear filas de datos
    total = 0;
    for (let i = 0; i < quotas_quantity; i++) {
        type_frequency = rule_data.type_frequency;
        frequency_value = parseInt(rule_data.frequency_value);
        quote_price = parseFloat(rule_data.quote_price);

        if (discount_value > 0) quote_price = quote_price - ( quote_price * discount_value) / 100;

        const row = document.createElement("tr");

        // Crear celdas
        const payment_cell = document.createElement("td");
        payment_cell.className = "payment-parts-table-data";
        payment_cell.textContent = (i + 1).toString();
        row.appendChild(payment_cell);

        // Calcular la fecha del próximo pago
        const date = new Date();
        if (i > 0) {

            if ( i+1 == quotas_quantity && final_payment > 0 ) quote_price = final_payment;

                frequency = i * frequency_value;
                date.setFullYear(
                    date.getFullYear() + (type_frequency == "year" ? frequency : 0)
                );
                date.setMonth(
                    date.getMonth() + (type_frequency == "month" ? frequency : 0)
                );
                date.setDate(date.getDate() + (type_frequency == "day" ? frequency : 0));
        } else {
            if (initial_payment > 0) quote_price = initial_payment;
        }

        // acomoda el precio para que solo tome 2 decimales
        quote_price = parseFloat( parseFloat(quote_price).toFixed(2) );

        const lang = document.documentElement.lang;

        const date_cell = document.createElement("td");
        date_cell.className = "payment-parts-table-data";
        date_cell.textContent =
        new Intl.DateTimeFormat(lang ?? "en-US", opcions_date).format(date) + (i === 0 ? " (Current)" : "");
        row.appendChild(date_cell);

        const amount_cell = document.createElement("td");
        amount_cell.className = "payment-parts-table-data";

        const usdFormatter = new Intl.NumberFormat("en-US", {
            style: "currency",
            currency: "USD",
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });

        amount_cell.textContent = usdFormatter.format( quote_price );
        row.appendChild(amount_cell);

        // Añadir fila a la tabla
        table.appendChild(row);
        
        total += quote_price;
    }

    // Fila de total
    const total_row = document.createElement("tr");
    const total_header = document.createElement("th");
    total_header.className = "payment-parts-table-header text-end";
    total_header.colSpan = 3;
    total_header.textContent = text_total;
    total_row.appendChild(total_header);
    table.appendChild(total_row);

    const total_payment_row = document.createElement("tr");
    total_payment_row.className = "payment-parts-table-row";

    const total_payment_cell = document.createElement("td");
    total_payment_cell.className = "payment-parts-table-data text-end";
    total_payment_cell.colSpan = 3;

    // Reutiliza el formateador de moneda USD
    const usdFormatter = new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    // Asegúrate de que 'total' sea un número antes de formatearlo
    total_payment_cell.textContent = usdFormatter.format(parseFloat(total));

    total_payment_row.appendChild(total_payment_cell);
    table.appendChild(total_payment_row);

    // Insertar tabla en el contenedor
    table_payment.appendChild(table);
}

/**
 * Actualiza el precio de un producto en el carrito de WooCommerce
 * de acuerdo con las reglas de cotización actuales.
 *
 * @param {number} product_id - Identificador del producto cuyo precio se actualizará.
 * @param {number} rule_id - Identificador de la regla de cotización que se aplicará.
 *
 * @return {void} No retorna ningún valor.
 *
 * La función envía una solicitud AJAX para actualizar el precio del producto
 * en el carrito y, si la actualización es exitosa, refresca el carrito
 * para reflejar el nuevo precio.
 */
function update_price_product_cart_quota_rule_js(product_id, rule_id) {
  const formData = new FormData();
  formData.append("action", "update_price_product_cart_quota_rule");
  formData.append("product_id", product_id);
  formData.append("rule_id", rule_id);

  fetch(ajax_object.ajax_url, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      refresh_wc_cart();
    })
    .catch((error) => {});
}

/**
 * Recarga el carrito de WooCommerce actualizando los fragmentos y los totales.
 *
 * @return {void} No retorna ningún valor.
 *
 * La función dispara un evento para refrescar el carrito, realiza una solicitud AJAX
 * para obtener los fragmentos actualizados y reemplaza los elementos del DOM correspondientes.
 * También puede forzar la recarga de los totales en la página de pago.
 */
function refresh_wc_cart() {
  // Disparar evento nativo de WooCommerce
  jQuery(document.body).trigger("wc_fragment_refresh updated_wc_div");

  // Actualizar fragmentos del carrito
  jQuery.get(
    wc_cart_fragments_params.wc_ajax_url
      .toString()
      .replace("%%endpoint%%", "get_refreshed_fragments"),
    function (data) {
      if (data && data.fragments) {
        jQuery.each(data.fragments, function (key, value) {
          jQuery(key).replaceWith(value);
        });
      }
    }
  );

  // Forzar recarga de los totales (opcional)
  jQuery("body").trigger("update_checkout");
}

(function ($) {
  $(document).ready(function () {
    // Select the radio inputs
    var selectedValue = "Complete";
    var $radioInputs = $('input[type="radio"][name="option"]');

    // reloadTable();
    reloadButton();

    $(document).on("updated_checkout", function () {
      //   reloadTable();
      reloadButton();
    });

    /* 
        // Add an event listener to the radio inputs
        $radioInputs.on('change', function() {
            // Get the selected radio input value
            selectedValue = $(this).val();

            switch (selectedValue) {
                case 'Complete':
                var complete = document.getElementById('Complete');
                if (complete) {
                    complete.style.display = 'table';
                }
                var annual = document.getElementById('Annual');
                if (annual) {
                    annual.style.display = 'none';
                }
                var semmiannual = document.getElementById('Semiannual');
                if (semmiannual) {
                    semmiannual.style.display = 'none';
                }
                break;
                case 'Annual':
                var annual = document.getElementById('Annual');
                if (annual) {
                    annual.style.display = 'table';
                }
                var complete = document.getElementById('Complete');
                if (complete) {
                    complete.style.display = 'none';
                }
                var semmiannual = document.getElementById('Semiannual');
                if (semmiannual) {
                    semmiannual.style.display = 'none';
                }
                break;
                case 'Semiannual':
                var semmiannual = document.getElementById('Semiannual');
                if (semmiannual) {
                    semmiannual.style.display = 'table';
                }
                var complete = document.getElementById('Complete');
                if (complete) {
                    complete.style.display = 'none';
                }
                var annual = document.getElementById('Annual');
                if (annual) {
                    annual.style.display = 'none';
                }
                break;
            }

            // Get the cart update URL
            var updateCartUrl = ajax_object.ajax_url + '?action=woocommerce_update_cart';

            // Send an AJAX request to update the cart
            $.ajax({
                type: 'POST',
                url: updateCartUrl,
                data: {
                    'action': 'woocommerce_update_cart',
                    'option': selectedValue
                },
                success: function(response) {
                    // Update the cart price
                    $('#cart_totals').html(response);
                    $(document.body).trigger('update_checkout');
                    setTimeout(() => {
                        //reloadTable();
                    }, 250);
                }
            });
        });
        */

    $('input[name="fee"]').on("change", function () {
      // Get the cart update URL
      var updateCartUrl = ajax_object.ajax_url + "?action=fee_update";

      // Send an AJAX request to update the cart
      $.ajax({
        type: "POST",
        url: updateCartUrl,
        data: {
          action: "fee_update",
          option: $(this).is(":checked"),
        },
        success: function (response) {
          // Update the cart price
          $("#cart_totals").html(response);
          $(document.body).trigger("update_checkout");
          setTimeout(() => {
            //reloadTable();
          }, 250);
        },
      });
    });

    // Add the applyScholarship function
    function applyScholarship() {
      table_payment = document.getElementById("table-payment");
      product_id = table_payment.getAttribute("data-product_id");

      // Obtener el valor del atributo 'data-producto_id' de '#table-payment'
      // var product_id = $('#table-payment').data('producto_id');

      // Apply the scholarship discount
      $.ajax({
        type: "POST",
        url: ajax_object.ajax_url + "?action=apply_scholarship",
        data: {
          action: "apply_scholarship",
          product_id: product_id,
        },
        success: function (response) {
          // Update the cart price
          $("#cart_totals").html(response);
          $(document.body).trigger("update_checkout");

          //reloadTable();
          reloadButton();

          discount_value = response.data.discount_value;
          if (discount_value) {
            $("#discount_value").val(discount_value);

            const element = $("#table-payment [data-rule_id]").first();
            if (element.length) {
              const rule_id = element.data("rule_id");
              $(`#option-rule-${rule_id}`).trigger("click");
            }
          }
        },
      });
    }

    // Antigua funcion que traia la tabla
    function reloadTable() {
      // Apply the scholarship discount
      $.ajax({
        type: "POST",
        url: ajax_object.ajax_url + "?action=reload_payment_table",
        data: {
          action: "reload_payment_table",
          option: selectedValue,
        },
        success: function (response) {
          // Update the cart price
          $("#table-payment").html(response);
        },
      });
    }

    function reloadButton() {
      // Apply the scholarship discount
      $.ajax({
        type: "GET",
        url: ajax_object.ajax_url + "?action=reload_button_schoolship",
        success: function (response) {
          // Update the cart price
          $("#button-schoolship").html(response);
          // Add an event listener to the button scholaships
          $("#apply-scholarship-btn").on("click", function () {
            applyScholarship();
          });
        },
      });
    }
  });
})(jQuery);
