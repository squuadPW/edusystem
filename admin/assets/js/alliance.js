document.addEventListener('DOMContentLoaded',function(){

    buttons_change_status = document.querySelectorAll('.change-status-alliance');
    buttons_delete_alliance = document.getElementById('button-delete-alliance');
    number_phone = document.getElementById('number_phone');
    fee = document.getElementById('fee');
    toggleButton = document.getElementById('toggle-table');
    tabOrders = document.getElementById('tab-orders');
    tabPayments = document.getElementById('tab-payments');
    cardInvoices = document.getElementById('card-invoices');
    cardTransactions = document.getElementById('card-transactions');

     if (toggleButton) {
        
        toggleButton.addEventListener('click', function () {
            if (tabOrders.style.display === 'none') {
                tabOrders.style.display = 'table';
                cardInvoices.style.display = 'block';
                tabPayments.style.display = 'none';
                cardTransactions.style.display = 'none';
                toggleButton.textContent = 'Show payments'
            } else {
                tabOrders.style.display = 'none';
                cardInvoices.style.display = 'none';
                tabPayments.style.display = 'table';
                cardTransactions.style.display = 'block';
                toggleButton.textContent = 'Show orders';
            }
        });
     }

    if(buttons_change_status){

        buttons_change_status.forEach((button) => {
            
            button.addEventListener('click',(e) => {


                message = button.getAttribute('data-message');
                title = button.getAttribute('data-title');
                status_id = button.getAttribute('data-id');
                alliance_id = button.getAttribute('data-alliance');

                document.getElementById('title-modal-status-alliance').textContent = title;
                document.getElementById('message-modal-status-alliance').textContent = message;
                document.getElementById('status_alliance_id').value = alliance_id;
                document.getElementById('status_id').value = status_id;
                document.getElementById('modalStatusAlliance').style.display = "block";
            });
        });
    }

    if(buttons_delete_alliance){

        buttons_delete_alliance.addEventListener('click',(e) => {

            alliance_id = buttons_delete_alliance.getAttribute('data-id');
            document.getElementById('delete_alliance_id').value = alliance_id;
            document.getElementById('modalDeleteAlliance').style.display = "block";
        });
    }   

    document.querySelectorAll('.modal-close').forEach((close) => {
        close.addEventListener('click',(e) => {
            document.getElementById('modalStatusAlliance').style.display = "none";
            document.getElementById('modalDeleteAlliance').style.display = "none";
        });
    });

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

    if(fee){
        VMasker(fee).maskMoney({
            precision: 0,
            separator: ',',
            delimiter: '.',
            unit: '',
            suffixUnit: '%',
            zeroCents: true
          });

    }

    if(document.getElementById('typeFilter')){

        document.getElementById('typeFilter').addEventListener('change',(e) => {

            if(e.target.value == 'custom'){

                document.getElementById('inputStartDate').style.display = "inline-block";
                document.getElementById('inputStartDate').required = true;
                document.getElementById('inputStartDate').focus();
            }else{
                document.getElementById('inputStartDate').style.display = "none";
                document.getElementById('inputStartDate').required = false;
            }
        
        }); 
    }

    if(document.getElementById('update_data')){
        function loadData() {
            
            filter = document.getElementById('typeFilter').value;
            custom = document.getElementById('inputStartDate').value;
            alliance_id = document.getElementById('alliance_id').value;

            let htmlLoading = "";

            htmlLoading += "<tr>";
            htmlLoading += "<td class='column-primary id column-id' colspan='5' style='text-align:center;float:none;'><span class='spinner is-active' style='float:none;'></span></td>";
            htmlLoading += "</tr>";
                
            if (document.getElementById('table-institutes-payment')) {
                document.getElementById('table-institutes-payment').innerHTML = htmlLoading;
            }

            if (document.getElementById('table-payment-alliance')) {
                document.getElementById('table-payment-alliance').innerHTML = htmlLoading;
            }

            htmlLoading = "";
            htmlLoading += "<tr>";
            htmlLoading += "<td class='column-primary id column-id' colspan='4' style='text-align:center;float:none;'><span class='spinner is-active' style='float:none;'></span></td>";
            htmlLoading += "</tr>";
    
            if (document.getElementById('table-institutes-payment-payments')) {
                document.getElementById('table-institutes-payment-payments').innerHTML = htmlLoading;
                document.getElementById('length-transactions').innerHTML = '';
                document.getElementById('total-transactions').innerHTML = '';  
            }

            const XHR= new XMLHttpRequest();
            XHR.open('POST',list_fee_alliance.url,true);
            XHR.setRequestHeader('Content-type','application/x-www-form-urlencoded');
            XHR.responseType ='text';
            XHR.send('action='+ list_fee_alliance.action + '&filter=' + filter + '&custom=' + custom + "&alliance_id=" + alliance_id);
            XHR.onload = function(){

                if(this.readyState=='4' && XHR.status === 200) {

                    let result = JSON.parse(XHR.responseText);

                    if(result.status == 'success'){

                        if (document.getElementById('table-payment-alliance')) {
                            document.getElementById('table-payment-alliance').innerHTML = result.html;
                        }
                        
                        if (document.getElementById('fee-total-alliance')) {
                            document.getElementById('fee-total-alliance').innerHTML = result.data.total;    
                        }

                        if (document.getElementById('fee-total-balance')) {
                            document.getElementById('fee-total-balance').innerHTML = result.current_invoice.total;
                        }
    
                        if (document.getElementById('table-institutes-payment')) {
                            document.getElementById('table-institutes-payment').innerHTML = result.html;
                            if (document.getElementById('length-invoices')) {
                                document.getElementById('length-invoices').innerHTML = result.data.orders.length;
                                document.getElementById('total-invoices').innerHTML = result.data.total;
                            }
                        }

                        if (document.getElementById('table-institutes-payment-payments')) {
                            document.getElementById('table-institutes-payment-payments').innerHTML = result.html_transactions;
                            document.getElementById('length-transactions').innerHTML = result.transactions.orders.length;
                            document.getElementById('total-transactions').innerHTML = result.transactions.total;  
                            document.getElementById('fee-total-paid').innerHTML = result.transactions.total_paid;
                            document.getElementById('fee-pending-payment').innerHTML = result.transactions.total_pending;
                        }
                        
                        if (document.getElementById('table-institutes-payment')) {
                            document.getElementById('table-institutes-payment').innerHTML = result.html;
                        }
                    }
                }
            }   
        }

        loadData();
        document.getElementById('update_data').addEventListener('click',() => {
            loadData();
        });
    }
});