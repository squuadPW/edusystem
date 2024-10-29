document.addEventListener('DOMContentLoaded',function(){

    if(document.getElementById('continue')){
        document.getElementById('continue').addEventListener('click', function(event) {
          document.getElementById('modal-continue').style.display = 'block';
        });
    }

    if(document.getElementById('send-continue')){
      document.getElementById('send-continue').addEventListener('click', function(event) {
        document.getElementById('send-continue').disabled = true;
        document.getElementById('send-continue').innerText = 'Loading...';
        let elective = document.querySelector('select[name="elective"]').value;
        const XHR = new XMLHttpRequest();
        XHR.open("POST", `${ajax_object.ajax_url}?action=student_continue`, true);
        XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        XHR.responseType = "text";
        let params = `action=student_continue`;
        if (elective) {
          params += `&elective=${elective}`;
        }
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

    if(document.getElementById('close-continue')){
      document.getElementById('close-continue').addEventListener('click', function(event) {
        document.getElementById('modal-continue').style.display = 'none';
      });
    }
});