document.addEventListener('DOMContentLoaded',function(){

    not_institute = document.getElementById('institute_id');

    if(document.getElementById('birth_date')){

        flatpickr(document.getElementById('birth_date'), {
            dateFormat: "m/d/Y",
            maxDate:"today",
            disableMobile: "true",
        }); 
    }

    if(document.getElementById('birth_date_student')){

        flatpickr(document.getElementById('birth_date_student'), {
            dateFormat: "m/d/Y",
            disableMobile: 'false',
            maxDate:"today"
        }); 

        document.getElementById('birth_date_student').addEventListener('change',(e) => {

            date = e.target.value;
            date = date.split('/');
           
            start = new Date(date[2],(date[0] - 1),date[1]); 
            today = new Date(); 
            diff = diff_years(today,start);
          
            if(diff >= 18){
                document.getElementById('parent_name_field').style.display = "none";
                document.getElementById('parent-lastname-field').style.display = "none";
                document.getElementById('parent-country-field').style.display = "none";
                document.getElementById('parent-email-field').style.display = "none";
                
                document.getElementById('agent_name').required = false;
                document.getElementById('agent_last_name').required = false;
                document.getElementById('number_partner').required = false;
                document.getElementById('email_partner').required = false;
            }else{
                document.getElementById('parent_name_field').style.display = "block";
                document.getElementById('parent-lastname-field').style.display = "block";
                document.getElementById('parent-country-field').style.display = "block";
                document.getElementById('parent-email-field').style.display = "block";

                document.getElementById('agent_name').required = true;
                document.getElementById('agent_last_name').required = true;
                document.getElementById('number_partner').required = true;
                document.getElementById('email_partner').required = true;
            }
        });
    }

    if(not_institute){

        not_institute.addEventListener('change',(e) => {

            if(e.target.value == 'other'){
                document.getElementById('name-institute-field').style.display = "block";
                document.getElementById('name_institute').required = true;
                document.getElementById('institute_id').required = false;
                document.getElementById('institute_id_required').textContent = "";
            }else{
                document.getElementById('name-institute-field').style.display = "none";
                document.getElementById('name_institute').required = false;
                document.getElementById('institute_id').required = true;
                document.getElementById('institute_id_required').textContent = "*";
            }
        });
    }

    if(document.getElementById('billing_first_name')){

        document.getElementById('billing_first_name').addEventListener('input',(e) => {

            if(!getCookie('is_older')){
                return;
            }

            if(!getCookie('name_student')){
                return 
            }

            value = e.target.value;
            setCookie('name_student',value);
        });
    }

    if(document.getElementById('billing_last_name')){

        document.getElementById('billing_last_name').addEventListener('input',(e) => {
            
            if(!getCookie('is_older')){
                return;
            }

            if(!getCookie('last_name_student')){
                return 
            }

            value = e.target.value;
            setCookie('last_name_student',value);
        });
    }

    if(document.getElementById('billing_country')){

        jQuery(function($){
            $(document.body).on('change', 'select[name=billing_country]', function(){

                if(!getCookie('is_older')){
                    return;
                }

                if(!getCookie('billing_country')){
                    return 
                }

                value = $(this).val();
                setCookie('billing_country',value);
            });
        });
    }

    if(document.getElementById('billing_city')){
        
        document.getElementById('billing_city').addEventListener('input',(e) => {

            if(!getCookie('is_older')){
                return;
            }

            if(!getCookie('billing_city')){
                return 
            }

            value = e.target.value;
            setCookie('billing_city',value);
        });
    }

    if(document.getElementById('billing_phone_hidden')){

        document.getElementById('billing_phone').addEventListener('input',(e) => {
        
            if(!getCookie('is_older')){
                return;
            }

            setTimeout(() => {
                value = document.getElementById('billing_phone_hidden').value;
                console.log(value);
                setCookie('phone_student',value);
            },1000)
        });
    }

    if(document.getElementById('billing_email')){

        document.getElementById('billing_email').addEventListener('input',(e) => {
            
            if(!getCookie('is_older')){
                return;
            }

            if(!getCookie('email_student')){
                return 
            }

            value = e.target.value;
            setCookie('email_student',value);

        });
    }

    const fileInputs = document.querySelectorAll('.custom-file-input');
    const fileLabels = document.querySelectorAll('.custom-file-label');
  
    fileInputs.forEach((fileInput, index) => {
      fileInput.addEventListener('change', () => {
        const fileName = fileInput.files[0].name;
        fileLabels[index].textContent = fileName ? fileName : 'Select file';
      });
    });
});

function diff_years(dt2, dt1) {
  let diff = (dt2.getTime() - dt1.getTime()) / 1000;
  diff /= (60 * 60 * 24);
  return Math.abs(Math.round(diff / 365.25));
}

function getCookie(name){
    function escape(s) { return s.replace(/([.*+?\^$(){}|\[\]\/\\])/g, '\\$1'); }
    var match = document.cookie.match(RegExp('(?:^|;\\s*)' + escape(name) + '=([^;]*)'));
    return match ? match[1] : null;
}

function setCookie(name,value){

    var date = new Date();
    date.setTime(date.getTime() + (1 * 24 * 60 * 60 * 1000));
    expires = "; expires=" + date.toGMTString();
    document.cookie = name + "=" + value + expires + "; path=/"; 
}