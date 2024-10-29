document.addEventListener('DOMContentLoaded',function(){

    if(document.getElementById('unsubscribe')){
        document.getElementById('unsubscribe').addEventListener('click', function(event) {
          document.getElementById('modal-unsubscribe').style.display = 'block';
        });
    }

    if(document.getElementById('send-unsubscribe')){
      document.getElementById('send-unsubscribe').addEventListener('click', function(event) {
        let reason = document.querySelector('textarea[name="reason"]').value;
        if (!reason) {
          alert('To continue you must write the reason why you are unsubscribing.');
          return;
        }
        document.getElementById('send-unsubscribe').disabled = true;
        document.getElementById('send-unsubscribe').innerText = 'Loading...';
        const XHR = new XMLHttpRequest();
        XHR.open("POST", `${ajax_object.ajax_url}?action=student_unsubscribe`, true);
        XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        XHR.responseType = "text";
        let params = `action=student_unsubscribe`;
        if (reason) {
          params += `&reason=${reason}`;
        }
        XHR.send(params);
        XHR.onload = function () {
          if (XHR.status === 200) {
            location.reload();
          } else {
            document.getElementById('send-unsubscribe').disabled = false;
            document.getElementById('send-unsubscribe').innerText = 'Unsubscribe';
          }
        };
      });
    }

    if(document.getElementById('close-unsubscribe')){
      document.getElementById('close-unsubscribe').addEventListener('click', function(event) {
        document.getElementById('modal-unsubscribe').style.display = 'none';
      });
    }
});