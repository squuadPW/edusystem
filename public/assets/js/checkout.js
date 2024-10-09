document.addEventListener('DOMContentLoaded',function(){

    if(document.getElementById('createaccount')){

        document.getElementById('createaccount').checked = true;
    }

    place_order = false;
    document.addEventListener('click', function(event) {
        if (event.target.id === 'place_order') {
            place_order = true;
        }
    });

    const checkoutRoute = '/checkout/'; // adjust this to your actual checkout route
    if (window.location.pathname === checkoutRoute) {
      window.addEventListener('beforeunload', function(event) {
        if (!place_order) {
            event.preventDefault();
        }
      });
    }   

    // let input_billing_email = document.getElementById('billing_email_field');
    // if (input_billing_email) {
    //     input_billing_email.style.display = 'none';
    // }

});