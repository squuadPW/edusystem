document.addEventListener('DOMContentLoaded',function(){

    approved_status = document.getElementById('approved_scholarship');

    if(approved_status){

        approved_status.addEventListener('click',(e) => {

            scholarship_id = approved_status.getAttribute('data-id');
            let title = approved_status.getAttribute('data-title');
            let message = approved_status.getAttribute('data-message');
            
            document.getElementById('title-modal-status-scholarship').textContent = title;
            document.getElementById('message-modal-status-scholarship').textContent = message;
            document.getElementById('scholarship_id').value = scholarship_id;
            document.getElementById('modalStatusScholarship').style.display = "block";
        });
    }

    pre_scholarship = document.getElementById('pre-scholarship');
    if(pre_scholarship){
        pre_scholarship.addEventListener('click',(e) => {
            document.getElementById('pre-scholarship-modal').style.display = "block";
        });
        
        var modalClosePreScholarship = document.querySelectorAll('#pre-scholarship-exit-icon, #pre-scholarship-exit-button');
        if (modalClosePreScholarship) {
            modalClosePreScholarship.forEach(function(element) {
            element.addEventListener('click', function() {
                document.getElementById('pre-scholarship-form').reset();
                document.getElementById('pre-scholarship-modal').style.display = 'none';
            });
            });
        }

    }

    document.querySelectorAll('.modal-close').forEach((close) => {
        close.addEventListener('click',(e) => {
            document.getElementById('modalStatusScholarship').style.display = "none";
        });
    });
});