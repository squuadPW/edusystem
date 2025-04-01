document.addEventListener('DOMContentLoaded',function(){

    buttons_change_status = document.querySelectorAll('.change-status-institute');
    button_delete_institute = document.getElementById('button-delete-institute');
    button_delete_institute_alliance = document.getElementById('button-delete-institute-alliance');
    number_rector_phone = document.getElementById('rector_phone');
    number_contact_phone = document.getElementById('contact_phone');
    number_phone = document.getElementById('phone');
    fee = document.getElementById('fee');
    input_date = document.getElementById('input-date');
    filter_date = document.getElementById('filter-date');
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

                let status = button.getAttribute('data-id');
                let title = button.getAttribute('data-title');
                let message = button.getAttribute('data-message');
                let institute = document.getElementById('institute_id').value;
                document.getElementById('message-modal-status-institute').textContent = message;
                document.getElementById('title-modal-status-institute').textContent = title;
                document.getElementById('change_status_institute_id').value = institute;
                document.getElementById('change_status_id').value = status;
                document.getElementById('modalStatusInstitute').style.display = "block";
            });
        });
    }

    if(button_delete_institute){
        button_delete_institute.addEventListener('click',(e) => {
            id = e.target.getAttribute('data-id');
            document.getElementById('delete_institute_id').value = id;
            document.getElementById('modalDeleteInstitute').style.display = "block";
        });
    }

    document.querySelectorAll('.modal-close').forEach((close) => {
        close.addEventListener('click',(e) => {
            if(document.getElementById('modalStatusInstitute')){
                document.getElementById('modalStatusInstitute').style.display = "none";
            }

            if(document.getElementById('modalDeleteInstitute')){
                document.getElementById('modalDeleteInstitute').style.display = "none";   
            }

            if(document.getElementById('modalDeleteInstituteAlliance')){
                document.getElementById('modalDeleteInstituteAlliance').style.display = "none";  
            }
        });
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

    if(button_delete_institute_alliance){

        button_delete_institute_alliance.addEventListener('click',(e) => {
            id = e.target.getAttribute('data-id');
            document.getElementById('delete_institute_id').value = id;
            document.getElementById('modalDeleteInstituteAlliance').style.display = "block";
        });
    }

    jQuery(function($){

        if(number_rector_phone){

            let iti_number_phone_rector = window.intlTelInput(number_rector_phone, {
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
                document.getElementById("rector_phone_hidden").value = iti_number_phone_rector.getNumber(); 
            }

            number_rector_phone.addEventListener('change',handleChange);
            number_rector_phone.addEventListener('keyup', handleChange);
        }

        if(number_contact_phone){

            let iti_number_phone_contact = window.intlTelInput(number_contact_phone, {
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
                document.getElementById("contact_phone_hidden").value = iti_number_phone_contact.getNumber(); 
            }

            number_contact_phone.addEventListener('change',handleChange);
            number_contact_phone.addEventListener('keyup', handleChange);
        }

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
    function loadData() {
        filter = document.getElementById('typeFilter').value;
        custom = document.getElementById('inputStartDate').value;
        institute_id = document.getElementById('institute_id').value;

        let htmlLoading = "";

        htmlLoading += "<tr>";
        htmlLoading += "<td class='column-primary id column-id' colspan='6' style='text-align:center;float:none;'><span class='spinner is-active' style='float:none;'></span></td>";
        htmlLoading += "</tr>";
            
        if (document.getElementById('table-institutes-payment')) {
            document.getElementById('table-institutes-payment').innerHTML = htmlLoading;
            if (document.getElementById('length-invoices')) {
                document.getElementById('length-invoices').innerHTML = '';
                document.getElementById('total-invoices').innerHTML = '';   
            }
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
        XHR.open('POST',list_fee_institute.url,true);
        XHR.setRequestHeader('Content-type','application/x-www-form-urlencoded');
        XHR.responseType ='text';
        XHR.send('action=invoices_institute' + '&filter=' + filter + '&custom=' + custom + "&institute_id=" + institute_id);
        XHR.onload = function(){

            if(this.readyState=='4' && XHR.status === 200) {

                let result = JSON.parse(XHR.responseText);

                if(result.status == 'success'){
                    if (document.getElementById('fee-total')) {
                        document.getElementById('fee-total').innerHTML = result.data.total;
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
                }
            }
        }   
    }

    if(document.getElementById('update_data')){
        loadData();
        document.getElementById('update_data').addEventListener('click',() => {
            loadData();
        });
    }

    if (document.getElementById('country-selector')) {
        let timeout;
        document.getElementById('country-selector').addEventListener('change', function(e) {
            clearTimeout(timeout);
            timeout = setTimeout(() => {

                document.getElementById('state-td').style.display = 'none';

                var xhr = new XMLHttpRequest();
                xhr.open('POST',list_fee_institute.url,true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                if (xhr.status === 200) {
                    var states = JSON.parse(xhr.responseText);
                    var stateSelect = document.getElementById('state-selector');
                    stateSelect.innerHTML = '';
                    for (var stateCode in states) {
                    var option = document.createElement('option');
                    option.value = stateCode;
                    option.text = states[stateCode];
                    stateSelect.appendChild(option);
                    }
                    if (states) {
                        document.getElementById('state-td').style.display = 'block';
                    }
                }
                };
                xhr.send('action=get_states_by_country&country_code=' + e.target.value);

            }, 800); // Cambia 2000 a 1000 para 1 segundo si lo prefieres
        });
    }
});