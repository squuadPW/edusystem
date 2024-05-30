document.addEventListener('DOMContentLoaded',function(){

    approved_status = document.getElementById('approved_payment');

    if(approved_status){

        approved_status.addEventListener('click',(e) => {

            order_id = approved_status.getAttribute('data-id');
            let title = approved_status.getAttribute('data-title');
            let message = approved_status.getAttribute('data-message');
            
            document.getElementById('title-modal-status-payment').textContent = title;
            document.getElementById('message-modal-status-payment').textContent = message;
            document.getElementById('order_id').value = order_id;
            document.getElementById('modalStatusPayment').style.display = "block";
        });
    }

    document.querySelectorAll('.modal-close').forEach((close) => {
        close.addEventListener('click',(e) => {
            document.getElementById('modalStatusPayment').style.display = "none";
        });
    });
});