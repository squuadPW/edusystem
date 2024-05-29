document.addEventListener('DOMContentLoaded',function(){

    $elements_number_phone = document.querySelectorAll('.number_phone');

    if(document.getElementById('birth_date')){

        flatpickr(document.getElementById('birth_date'), {
            dateFormat: "m/d/Y",
        }); 
    }

    if($elements_number_phone){
        $elements_number_phone.forEach(number_phone => {
            VMasker(number_phone).maskPattern("(999)999-9999");
        });
    }
});