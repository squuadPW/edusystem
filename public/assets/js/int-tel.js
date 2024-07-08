document.addEventListener('DOMContentLoaded',function(){

    number_phone = document.getElementById('number_phone');
    number_partner = document.getElementById('number_partner');
    number_phone_account = document.getElementById('number_phone_account');
    number_billing_phone = document.getElementById('billing_phone');
    number_rector_phone = document.getElementById('rector_phone');

    jQuery(function($){

        if(number_phone){

            let iti_number_phone = window.intlTelInput(number_phone, {
                nationalMode: true,
                initialCountry: "auto",
                separateDialCode: true,
                strictMode: true,
                hiddenInput: "number_phone_hidden",
                geoIpLookup: callback => {
                fetch("https://ipapi.co/json")
                    .then(res => res.json())
                    .then(data => callback(data.country_code))
                    .catch(() => callback("us"));
                },
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"
            });

            function handleChange(){
                document.getElementsByName("number_phone_hidden")[0].value = iti_number_phone.getNumber(); 
            }

            number_phone.addEventListener('change',handleChange);
            number_phone.addEventListener('keyup', handleChange);
        }

        if(number_partner){

            let iti_number_partner = window.intlTelInput(number_partner,{
                nationalMode: true,
                initialCountry: "auto",
                separateDialCode: true,
                strictMode: true,
                hiddenInput: "number_partner_hidden",
                geoIpLookup: callback => {
                fetch("https://ipapi.co/json")
                    .then(res => res.json())
                    .then(data => callback(data.country_code))
                    .catch(() => callback("us"));
                },
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"
            });

            function handleChange(){
                document.getElementsByName("number_partner_hidden")[0].value = iti_number_partner.getNumber(); 
            }

            number_partner.addEventListener('change',handleChange);
            number_partner.addEventListener('keyup', handleChange);
        }

        if(number_phone_account){

            let iti_number_phone_account = window.intlTelInput(number_phone_account,{
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
                document.getElementsByName('number_phone_hidden')[0].value = iti_number_phone_account.getNumber();
            }

            number_phone_account.addEventListener('change', handleChange);
            number_phone_account.addEventListener('keyup', handleChange);
        }

        if(number_billing_phone){

            let iti_number_billing_phone = window.intlTelInput(number_billing_phone, {
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

            number_billing_phone.addEventListener('change',(e) => {
                document.getElementById('billing_phone_hidden').value = iti_number_billing_phone.getNumber();
            });

            number_billing_phone.addEventListener('input',(e) => {
                document.getElementById('billing_phone_hidden').value = iti_number_billing_phone.getNumber();
            });
        }

        if(number_rector_phone){

            let iti_number_rector_phone = window.intlTelInput(number_rector_phone, {
                nationalMode: true,
                initialCountry: "auto",
                separateDialCode: true,
                strictMode: true,
                hiddenInput: "number_rector_phone_hidden",
                geoIpLookup: callback => {
                fetch("https://ipapi.co/json")
                    .then(res => res.json())
                    .then(data => callback(data.country_code))
                    .catch(() => callback("us"));
                },
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"
            });   
        }
    })
});
    