document.addEventListener('DOMContentLoaded',function(){

    approved_status = document.getElementById('approved_request');

    if(approved_status){

        approved_status.addEventListener('click',(e) => {

            order_id = approved_status.getAttribute('data-id');
            let title = approved_status.getAttribute('data-title');
            let message = approved_status.getAttribute('data-message');
            
            document.getElementById('title-modal-status-request').textContent = title;
            document.getElementById('text-modal-status-request').textContent = title.toLowerCase();
            document.getElementById('message-modal-status-request').textContent = message;
            document.getElementById('status_id').value = 3;
            document.getElementById('modalStatusRequest').style.display = "block";
        });
    }

    decline_status = document.getElementById('decline_request');

    if(decline_status){

        decline_status.addEventListener('click',(e) => {

            order_id = decline_status.getAttribute('data-id');
            let title = decline_status.getAttribute('data-title');
            let message = decline_status.getAttribute('data-message');
            
            document.getElementById('title-modal-status-request').textContent = title;
            document.getElementById('text-modal-status-request').textContent = title.toLowerCase();
            document.getElementById('message-modal-status-request').textContent = message;
            document.getElementById('status_id').value = 2;
            document.getElementById('modalStatusRequest').style.display = "block";
        });
    }

    document.querySelectorAll('.modal-close').forEach((close) => {
        close.addEventListener('click',(e) => {
            document.getElementById('modalStatusRequest').style.display = "none";
        });
    });
});