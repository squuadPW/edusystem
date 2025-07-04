document.addEventListener( 'DOMContentLoaded', ()=>{

    options_quotas = document.querySelectorAll('.options-quotas .option-quota');
    options_quotas.forEach( option_quota => {

        option_quota.addEventListener('click', function() {
            rule_id = option_quota.getAttribute("data-id");
            rule = document.getElementById(`data-rule-${rule_id}`)?.value;
            if( rule ) {
                rule_data = JSON.parse( rule );
                payment_table( rule_data ); 
                update_price_product_cart_quota_rule_js ( 551,rule_id );
            }
        });
    });

});

function payment_table( rule_data ) {

    table_payment = document.getElementById('table-payment');
    table_payment.innerHTML = "";
    const text_total = table_payment.getAttribute("data-text_total");
    const headers = JSON.parse( table_payment.getAttribute("data-text_table_headers") ?? '{}');

    // Crear tabla
    const table = document.createElement('table');
    table.className = 'payment-parts-table mt-5';
            
        // Crear fila de encabezado
        const header_row = document.createElement('tr');
            // Crear encabezados
            headers.forEach(header_text => {
                const th = document.createElement('th');
                th.className = 'payment-parts-table-header';
                th.textContent = header_text;
                header_row.appendChild(th);
            });
        table.appendChild(header_row);
        
        // fecha para formato
        const opcions_date = { year: 'numeric', month: 'long', day: 'numeric' };

        // Crear filas de datos
        total = 0;
        for (let i = 0; i < rule_data.quotas_quantity; i++) {

            type_frequency = rule_data.type_frequency;
            frequency_value = parseInt(rule_data.frequency_value);
            quote_price = parseFloat(rule_data.quote_price);
            initial_price = parseFloat( rule_data.initial_price )

            const row = document.createElement('tr');

                // Crear celdas
                const payment_cell = document.createElement('td');
                payment_cell.className = 'payment-parts-table-data';
                payment_cell.textContent = (i + 1).toString();
                row.appendChild(payment_cell);


                // Calcular la fecha del próximo pago
                const date = new Date();
                if ( i > 0 ) {

                    frequency = i * frequency_value;
                    date.setFullYear(date.getFullYear() + ( type_frequency == 'year' ? frequency : 0));
                    date.setMonth(date.getMonth() + ( type_frequency == 'month' ? frequency : 0));
                    date.setDate(date.getDate() + ( type_frequency == 'day' ? frequency : 0));

                }  else {
                    if( initial_price > 0 ) quote_price = initial_price;
                }
                const lang = document.documentElement.lang;

                const date_cell = document.createElement('td');
                date_cell.className = 'payment-parts-table-data';
                date_cell.textContent =  new Intl.DateTimeFormat( lang ?? 'en-US', opcions_date).format(date) + (i === 0 ? ' (Current)' : '');
                row.appendChild(date_cell);

                const amount_cell = document.createElement('td');
                amount_cell.className = 'payment-parts-table-data';
                amount_cell.textContent = `$${parseInt(quote_price).toFixed(2)}`;
                row.appendChild(amount_cell);

            // Añadir fila a la tabla
            table.appendChild(row);

            total += quote_price;
        }

        // Fila de total
        const total_row = document.createElement('tr');
            const total_header = document.createElement('th');
            total_header.className = 'payment-parts-table-header text-end';
            total_header.colSpan = 3;
            total_header.textContent = text_total;
            total_row.appendChild(total_header);
        table.appendChild(total_row);
            
        const total_payment_row = document.createElement('tr');
        total_payment_row.className = 'payment-parts-table-row';
            const total_payment_cell = document.createElement('td');
            total_payment_cell.className = 'payment-parts-table-data text-end';
            total_payment_cell.colSpan = 3;
            total_payment_cell.textContent = `$${(total).toFixed(2)}`;
            total_payment_row.appendChild(total_payment_cell);
        table.appendChild(total_payment_row);

    // Insertar tabla en el contenedor
    table_payment.appendChild(table);
} 

function update_price_product_cart_quota_rule_js ( product_id, rule_id ) {

    const formData = new FormData();
    formData.append('action', 'update_price_product_cart_quota_rule');
    formData.append('product_id', product_id);
    formData.append('rule_id', rule_id);

    fetch(ajax_object.ajax_url, {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.log('Error:', data.data);
        }

        refreshCart();
    })
    .catch(error => {
        console.log('Error en la petición:', error);
    });
}

function refreshCart() {
    // Método 1: Disparar evento nativo de WooCommerce
    jQuery(document.body).trigger('wc_fragment_refresh updated_wc_div');
    
    // Método 2: Actualizar fragmentos del carrito (alternativa)
    jQuery.get(wc_cart_fragments_params.wc_ajax_url.toString()
        .replace('%%endpoint%%', 'get_refreshed_fragments'), function(data) {
        if (data && data.fragments) {
            jQuery.each(data.fragments, function(key, value) {
                jQuery(key).replaceWith(value);
            });
        }
    });
    
    // Método 3: Forzar recarga de los totales (opcional)
    jQuery('body').trigger('update_checkout');
}


(function($) {
    $(document).ready(function() {

        // Select the radio inputs
        var selectedValue = 'Complete';
        var $radioInputs = $('input[type="radio"][name="option"]');

        // reloadTable();
        reloadButton();

        $(document).on('updated_checkout', function() {
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


        $('input[name="fee"]').on('change', function() {
        // Get the cart update URL
        var updateCartUrl = ajax_object.ajax_url + '?action=fee_update';

        // Send an AJAX request to update the cart
        $.ajax({
            type: 'POST',
            url: updateCartUrl,
            data: {
            'action': 'fee_update',
            'option': $(this).is(':checked')
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

        // Add the applyScholarship function
        function applyScholarship() {
        // Apply the scholarship discount
            $.ajax({
                type: 'POST',
                url: ajax_object.ajax_url + '?action=apply_scholarship',
                data: {
                'action': 'apply_scholarship'
                },
                success: function(response) {
                // Update the cart price
                $('#cart_totals').html(response);
                $(document.body).trigger('update_checkout');
                    //reloadTable();
                    reloadButton();
                }
            });
        }

        // Antigua funcion que traia la tabla
        function reloadTable() {
            // Apply the scholarship discount
            $.ajax({
                type: 'POST',
                url: ajax_object.ajax_url + '?action=reload_payment_table',
                data: {
                    'action': 'reload_payment_table',
                    'option': selectedValue
                },
                success: function(response) {
                    // Update the cart price
                    $('#table-payment').html(response);
                }
            });
        }

        function reloadButton() {
            console.log('hola');
            // Apply the scholarship discount
            $.ajax({
                type: 'GET',
                url: ajax_object.ajax_url + '?action=reload_button_schoolship',
                success: function(response) {
                    // Update the cart price
                    $('#button-schoolship').html(response);
                    // Add an event listener to the button scholaships
                    $('#apply-scholarship-btn').on('click', function() {
                        applyScholarship();
                    });
                }
            });
        }

    });
})(jQuery);





