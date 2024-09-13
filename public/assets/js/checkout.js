document.addEventListener('DOMContentLoaded',function(){

    if(document.getElementById('createaccount')){

        document.getElementById('createaccount').checked = true;
    }

    window.addEventListener('beforeunload', function(event) {
        event.preventDefault();
    });

});