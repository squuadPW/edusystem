document.addEventListener('DOMContentLoaded',function(){

    if(document.getElementById('unsubscribe')){
        document.getElementById('unsubscribe').addEventListener('click', function(event) {
            const confirmMessage = `You are about to initiate the unenrollment process from your courses. Please be aware that if you proceed, all your assigned courses in Moodle will be permanently removed, your access to the student area will be revoked immediately, and you will be moved to the next academic cut-off. Are you sure you want to proceed with this action? Please confirm your decision.`;
            if (confirm(confirmMessage)) {
                const XHR = new XMLHttpRequest();
                XHR.open("POST", `${ajax_object.ajax_url}?action=student_unsubscribe`, true);
                XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                XHR.responseType = "text";
                let params = `action=student_unsubscribe`;
                XHR.send(params);
                XHR.onload = function () {
                  if (XHR.status === 200) {
                    location.reload();
                  }
                };
            }

        });
    }

});