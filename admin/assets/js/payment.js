document.addEventListener('DOMContentLoaded',function(){

    approved_status = document.getElementById('approved_payment');

    if(approved_status){

        approved_status.addEventListener('click',(e) => {

            order_id = approved_status.getAttribute('data-id');
            document.getElementById('notice-payment-completed').style.display = "none";

            const XHR= new XMLHttpRequest();
            XHR.open('POST',update_payment.url,true);
            XHR.setRequestHeader('Content-type','application/x-www-form-urlencoded');
            XHR.responseType ='text';
            XHR.send('action='+ update_payment.action + "&order_id=" + order_id);
            XHR.onload = function(){

                if(this.readyState=='4' && XHR.status === 200) {
            
                    let result = JSON.parse(XHR.responseText);
                    if(result.status == "success"){
                        document.getElementById('notice-payment-completed').style.display = "block";
                        document.getElementById('approved_payment').style.display = "none";
                        
                        setTimeout(() => {
                            document.getElementById('notice-payment-completed').style.display = "none";
                        },2000);
                    }
                }
            }
        });
    }
});