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
              // let response = JSON.parse(XHR.response);
              // window.location.href = response.data.redirect;
              document.querySelector("input[name=birth_date_student]").value = getCookie("birth_date");
              document.querySelector("select[name=document_type]").value = getCookie("document_type");
              document.querySelector("input[name=id_document]").value = getCookie("id_document");
              document.querySelector("input[name=name_student]").value = getCookie("name_student");
              document.querySelector("input[name=middle_name_student]").value = getCookie("middle_name_student");
              document.querySelector("input[name=lastname_student]").value = getCookie("last_name_student");
              document.querySelector("input[name=middle_last_name_student]").value = getCookie("middle_last_name_student");
              document.querySelector("input[name=number_phone]").value = getCookie("phone_student");
              document.querySelector("input[name=email_student]").value = getCookie("email_student");
              document.querySelector("select[name=gender]").value = getCookie("gender");
              document.querySelector("select[name=country]").value = getCookie("billing_country");
              // document.querySelector("select[name=country]").dispatchEvent(new Event("change"));

              document.querySelector("input[name=city]").value = getCookie("billing_city");
              document.querySelector("select[name=etnia]").value = getCookie("ethnicity");
              document.querySelector("input[name=birth_date_parent]").value = getCookie("birth_date_parent");
              document.querySelector("select[name=parent_document_type]").value = getCookie("parent_document_type");
              document.querySelector("input[name=id_document_parent]").value = getCookie("id_document_parent");
              document.querySelector("input[name=agent_name]").value = getCookie("agent_name");
              document.querySelector("input[name=agent_last_name]").value = getCookie("agent_last_name");
              document.querySelector("input[name=number_partner]").value = getCookie("number_partner");
              document.querySelector("select[name=gender_parent]").value = getCookie("gender_parent");
              document.querySelector("input[name=email_partner]").value = getCookie("email_partner");
              document.querySelector("input[name=password]").value = getCookie("password");
              // document.querySelector("select[name=program]").value = getCookie("program_id_number");
              // document.querySelector("select[name=program]").dispatchEvent(new Event("change"));

              // document.querySelector("select[name=institute_id]").value = getCookie("institute_id");
              // document.querySelector("select[name=institute_id]").dispatchEvent(new Event("change"));

              // document.querySelector("select[name=grade]").value = getCookie("initial_grade");
              // document.querySelector("select[name=grade]").dispatchEvent(new Event("change"));

              document.getElementById('modal-continue-checkout').style.display = 'none';
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

function getCookie(name) {
  let cookieArr = document.cookie.split(";");
  for (let i = 0; i < cookieArr.length; i++) {
    let cookiePair = cookieArr[i].split("=");
    // Eliminamos espacios en blanco y verificamos si el nombre de la cookie coincide
    if (name === cookiePair[0].trim()) {
      return decodeURIComponent(cookiePair[1]);
    }
  }
  // Retornamos null si no se encuentra la cookie
  return null;
}
