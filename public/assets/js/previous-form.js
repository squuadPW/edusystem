document.addEventListener('DOMContentLoaded',function(){

    if(document.getElementById('continue-checkout')){
      document.getElementById('continue-checkout').addEventListener('click', function(event) {
        document.getElementById('continue-checkout').disabled = true;
        document.getElementById('continue-checkout').innerText = 'Loading...';
        const XHR = new XMLHttpRequest();
        XHR.open("POST", `${ajax_object.ajax_url}?action=use_previous_form_aes`, true);
        XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        XHR.responseType = "text";
        let params = `action=use_previous_form_aes`;
        params += `&use=${1}`;
        XHR.send(params);
        XHR.onload = function () {
          if (XHR.status === 200) {
            if (XHR.response) {
              // Redirigir si se proporciona una URL
              let response = JSON.parse(XHR.response);
              window.location.href = response.data.redirect;
            }
          } else {
            document.getElementById('send-continue').disabled = false;
            document.getElementById('send-continue').innerText = 'continue';
          }
        };
      });
    }
  
    if(document.getElementById('quit-checkout')){
      document.getElementById('quit-checkout').addEventListener('click', function(event) {
        document.getElementById('quit-checkout').disabled = true;
        document.getElementById('quit-checkout').innerText = 'Loading...';
        const XHR = new XMLHttpRequest();
        XHR.open("POST", `${ajax_object.ajax_url}?action=use_previous_form_aes`, true);
        XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        XHR.responseType = "text";
        let params = `action=use_previous_form_aes`;
        params += `&use=${0}`;
        XHR.send(params);
        XHR.onload = function () {
          if (XHR.status === 200) {
            location.reload();
          } else {
            document.getElementById('send-continue').disabled = false;
            document.getElementById('send-continue').innerText = 'continue';
          }
        };
      });
    }

});