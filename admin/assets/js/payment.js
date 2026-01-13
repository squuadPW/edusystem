url_ajax = vars.url_ajax;

document.addEventListener('DOMContentLoaded',function(){

    approved_status = document.getElementById('approved_payment');
    if(approved_status){

        approved_status.addEventListener('click',(e) => {

            order_id = approved_status.getAttribute('data-id');
            let title = approved_status.getAttribute('data-title');
            let message = approved_status.getAttribute('data-message');
            
            document.getElementById('title-modal-status-payment').textContent = title;
            document.getElementById('text-modal-status-payment').textContent = title.toLowerCase();
            document.getElementById('message-modal-status-payment').textContent = message;
            document.getElementById('order_id').value = order_id;
            document.getElementById('status_id').value = 'completed';
            document.getElementById('modalStatusPayment').style.display = "block";
        });
    }

    decline_status = document.getElementById('decline_payment');
    if(decline_status){

        decline_status.addEventListener('click',(e) => {

            order_id = decline_status.getAttribute('data-id');
            let title = decline_status.getAttribute('data-title');
            let message = decline_status.getAttribute('data-message');
            
            document.getElementById('title-modal-status-payment').textContent = title;
            document.getElementById('text-modal-status-payment').textContent = title.toLowerCase();
            document.getElementById('message-modal-status-payment').textContent = message;
            document.getElementById('order_id').value = order_id;
            document.getElementById('status_id').value = 'canceled';
            document.getElementById('modalStatusPayment').style.display = "block";
        });
    }

    payment_selected = document.querySelector('select[name=payment_selected]');
    if(payment_selected){

        payment_selected.addEventListener('change',(e) => {
            if (e.target.value == 'other_payment') {
                document.getElementById('other-payments').style.display = "block";
            } else {
                document.getElementById('other-payments').style.display = "none";
            }
        });
    }
    
    generate_order_split = document.getElementById('generate_order_split');
    if(generate_order_split){
        generate_order_split.addEventListener('click',(e) => {
            let order_id = generate_order_split.getAttribute('data-id');
            let title = generate_order_split.getAttribute('data-title');
            let message = generate_order_split.getAttribute('data-message');
            let total = generate_order_split.getAttribute('data-total');

            document.getElementById('title-modal-generate-order-split').textContent = title;
            document.getElementById('message-modal-generate-order-split').textContent = message;
            document.getElementById('amount-order').value = total;
            document.getElementById('order_id_old').value = order_id;
            document.getElementById('modalGenerateOrder').style.display = "block";
        });
    }

    document.querySelectorAll('.modal-close').forEach((close) => {
        close.addEventListener('click',(e) => {

            modal_status_payment = document.getElementById('modalStatusPayment');
            if( modal_status_payment ) modal_status_payment.style.display = "none";

            modal_generate_order = document.getElementById('modalGenerateOrder');
            if( modal_generate_order ) modal_generate_order.style.display = "none";

            modal_edit_item_split_payment = document.getElementById('modalEditItemSplitPayment');
            if( modal_edit_item_split_payment ) modal_edit_item_split_payment.style.display = "none";

            modal_status_payment_pending = document.getElementById('modalStatusPaymentToPending')
            if( modal_status_payment_pending ){

                select_status_payment = document.getElementById('select_status_payment');
                if ( select_status_payment ) {

                    select_status_payment.value = 'completed';
                    select_status_payment.dispatchEvent(new Event('change'));
                }

                modal_status_payment_pending.style.display = "none";
            }
        });
    });

    cuote_credit = document.getElementById('cuote-credit');
    new_coute_date = document.getElementById('new_coute_date');
    if( cuote_credit && new_coute_date ) {
        cuote_credit.addEventListener('change', function() {
            if (this.value === 'new_cuote') {
                new_coute_date.classList.remove('hidden');
            } else {
                new_coute_date.classList.add('hidden');
            }
        });
    }

});

function active_edit_price_item () {

    container_acction = document.querySelector('#table-products-payment .actions');
    if( container_acction ) {
        container_acction.classList.remove('hidden');
    }

    document.querySelectorAll('.item-product-payment .total-price').forEach((elem) => {
        elem.classList.add('hidden');
    });

    document.querySelectorAll('.item-product-payment .inputs-price').forEach((elem) => {

        // pone por defecto el precio original
        input = elem.querySelector('input');
        if( input ) input.value = parseFloat( input.getAttribute('data-origin-price') ?? 0 );

        elem.classList.remove('hidden');
    });

    // activa la seccion de botones
    seccion_button = document.getElementById('button-acction-payment');
    if( seccion_button ) {
        seccion_button.classList.add('hidden');
    }
}

function desactive_edit_price_item () {

    container_acction = document.querySelector('#table-products-payment .actions');
    if( container_acction ) {
        container_acction.classList.add('hidden');
    }

    document.querySelectorAll('.item-product-payment .total-price').forEach((elem) => {
        elem.classList.remove('hidden');
    });

    document.querySelectorAll('.item-product-payment .inputs-price').forEach((elem) => {
        elem.classList.add('hidden');
    });

    // desactiva la seccion de botones
    seccion_button = document.getElementById('button-acction-payment');
    if( seccion_button ) {
        seccion_button.classList.remove('hidden');
    }
}

// modal para verificar si el monto ingresado excede el monto pendiente si es de split payment
document.addEventListener('DOMContentLoaded', function() {

    recalculate_button = document.getElementById('recalculate_button');
    if( recalculate_button ) {
        recalculate_button.addEventListener('click', function(event) {
            
            // Selecciona todos los inputs que tengan el atributo data-fee-split-payment
            split_payment = document.querySelector('input[data-fee-split-payment]');
            if( split_payment ) {
                event.preventDefault();
                
                pending = document.getElementById('input_amount_pending')?.value ?? 0;
                currency = document.getElementById('input_amount_pending')?.getAttribute('data-currency') ?? '';

                pending = parseFloat(pending);
                total_entered = parseFloat(split_payment.value);
                origin_price = parseFloat(split_payment.getAttribute('data-origin-price') ?? 0);

                pending_entered = total_entered - origin_price;

                if( pending_entered > pending ) {
                    
                    excess_amount = pending_entered - pending;

                    modal = document.getElementById('modalEditItemSplitPayment');
                    if( modal ) {

                        input_excess_amount = document.getElementById('input_excess_amount');
                        if( input_excess_amount ) input_excess_amount.value = excess_amount;

                        text_excess_amount = document.getElementById('excess_amount');
                        if( text_excess_amount ) text_excess_amount.textContent = new Intl.NumberFormat('en-US', { style: 'currency', currency: currency }).format(excess_amount);

                        amount_entered = document.getElementById('amount_entered');
                        if( amount_entered ) amount_entered.textContent = new Intl.NumberFormat('en-US', { style: 'currency', currency: currency }).format(total_entered);

                        modal.style.display = "block";
                    }

                } else {
                    // Si no excede, envía el formulario
                    event.target.closest('form').submit();
                }
            }

        });
    }

    modal_edit_item_split = document.getElementById('modalEditItemSplitPaymentYes');
    if ( modal_edit_item_split ) {
        modal_edit_item_split.addEventListener('click', function() {

            // Selecciona todos los inputs que tengan el atributo data-fee-split-payment
            split_payment = document.querySelector('input[data-fee-split-payment]');
            if( split_payment ){
                
                pending = document.getElementById('input_amount_pending')?.value ?? 0;
                pending = parseFloat(pending);
                
                origin_price = parseFloat(split_payment.getAttribute('data-origin-price') ?? 0);
                
                excess_amount = origin_price + pending;

                split_payment.value = excess_amount;

                // Busca el formulario padre del botón y lo envía
                document.getElementById('recalculate_button').closest('form').submit();
            }
        });
    }

    // abre el modal de pagos completados a pendientes
    select_status_payment = document.getElementById('select_status_payment');
    if ( select_status_payment ) {
        select_status_payment.addEventListener('change', function() {

            const selected_value = this.value;  // Obtiene el valor seleccionado del select
            
            if ( selected_value != 'pending' ) return;

            modal = document.getElementById('modalStatusPaymentToPending');
            if( modal ){

                payment_id = select_status_payment.dataset.payment_id;

                payment_input = modal.querySelector('#payment_id');
                if ( payment_input ) payment_input.value = payment_id;
                
                description_area = modal.querySelector('#description'); 
                if ( description_area ) description_area.value = '';

                modal.style.display = 'block';
            }
        });
    }

    // en caso de que accepten poner como pendiente el pago
    payment_to_pending_from = document.querySelector('#payment_to_pending_from');
    if( payment_to_pending_from ) {

        payment_to_pending_from.addEventListener('submit', (event) => {

            event.preventDefault();

            button_submit = document.getElementById('payment_to_pending_submit');
            if( button_submit )  button_submit.disabled = true;
            
            payment_id = payment_to_pending_from.querySelector('#payment_id').value; 
            description = payment_to_pending_from.querySelector('#description').value; 

            const formData = new FormData(); 
            formData.append("action", "update_payment_to_pending"); 
            formData.append("payment_id", payment_id);
            formData.append("description", description);

            fetch(url_ajax, {
                method: "POST",
                body: formData,
            }).then( (res) => res.json() )
            .then((res) => {

                //recarga la pagina 
                location.reload();
            
            })
            .catch((err) => {});
        });

    }

});

