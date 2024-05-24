document.addEventListener('DOMContentLoaded',function(){

    if(document.getElementById('birth_date')){

        flatpickr(document.getElementById('birth_date'), {
            dateFormat: "m/d/Y",
        }); 
    }

    if(document.getElementById('number_phone')){
        VMasker(document.getElementById('number_phone')).maskPattern("(999)999-9999");
    }
    if(document.getElementById('number_partner')){
        VMasker(document.getElementById('number_partner')).maskPattern("(999)999-9999");
    }
});