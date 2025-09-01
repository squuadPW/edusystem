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

    paid_more = document.querySelector('input[name=paid_more]');

    if(paid_more){

        paid_more.addEventListener('change',(e) => {
            if (e.target.checked) {
                document.getElementById('cuote-credit-select').style.display = "block";
                document.getElementById('amount-credit-input').style.display = "block";
                document.querySelector('input[name=amount_credit]').required = true;
                document.querySelector('select[name=cuote_credit]').required = true;
            } else {
                document.getElementById('cuote-credit-select').style.display = "none";
                document.getElementById('amount-credit-input').style.display = "none";
                document.querySelector('input[name=amount_credit]').required = false;
                document.querySelector('select[name=cuote_credit]').required = false;
            }
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
            document.getElementById('modalStatusPayment').style.display = "none";
            document.getElementById('modalGenerateOrder').style.display = "none";
        });
    });


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
        elem.value = parseFloat( elem.getAttribute('data-origin-price') || 0 );
        elem.classList.remove('hidden');
    });
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
}
