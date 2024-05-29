document.addEventListener('DOMContentLoaded',function(){

    buttons_change_status = document.querySelectorAll('.change-status-institute');

    if(buttons_change_status){

        buttons_change_status.forEach((button) => {
            
            button.addEventListener('click',(e) => {

                let status = button.getAttribute('data-id');
                let title = button.getAttribute('data-title');
                let message = button.getAttribute('data-message');
                let institute = document.getElementById('institute_id').value;
                document.getElementById('message-modal-status-institute').textContent = message;
                document.getElementById('title-modal-status-institute').textContent = title;
                document.getElementById('change_status_institute_id').value = institute;
                document.getElementById('change_status_id').value = status;
                document.getElementById('modalStatusInstitute').style.display = "block";
            });
        });
    }

    document.querySelectorAll('.modal-close').forEach((close) => {
        close.addEventListener('click',(e) => {
            document.getElementById('modalStatusInstitute').style.display = "none";
        });
    });
});