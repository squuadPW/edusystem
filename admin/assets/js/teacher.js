document.addEventListener('DOMContentLoaded',function(){

    number_phone = document.getElementById('phone');

    jQuery(function($){
        if(number_phone){

            let iti_number_phone = window.intlTelInput(number_phone, {
                nationalMode: true,
                initialCountry: "auto",
                separateDialCode: true,
                strictMode: true,
                geoIpLookup: callback => {
                fetch("https://ipapi.co/json")
                    .then(res => res.json())
                    .then(data => callback(data.country_code))
                    .catch(() => callback("us"));
                },
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"
            });

            function handleChange(){
                document.getElementById("phone_hidden").value = iti_number_phone.getNumber(); 
            }

            number_phone.addEventListener('change',handleChange);
            number_phone.addEventListener('keyup', handleChange);
        }
    });
});

modal = document.getElementById('decline-modal');
function declineDocument(doc) {
    modal.style.display = 'block';
    document.querySelector('input[name=teacher_id_decline]').value = doc.teacher_id;
    document.querySelector('input[name=document_id_decline]').value = doc.id;
}

var modalCloseElements = document.querySelectorAll('#decline-exit-icon, #decline-exit-button');
if (modalCloseElements) {
  modalCloseElements.forEach(function(element) {
    element.addEventListener('click', function() {
      document.getElementById('decline-modal').style.display = 'none';
      const textarea = document.querySelector('textarea[name="decline-description"]');
      textarea.value = '';
    });
  });
}
