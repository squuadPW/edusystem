document.addEventListener('DOMContentLoaded',function(){

    if(document.getElementById('update_data')){

        document.getElementById('update_data').addEventListener('click',() => {

            filter = document.getElementById('typeFilter').value;
            custom = document.getElementById('inputStartDate').value;

            let htmlLoading = "";

            htmlLoading += "<tr>";
            htmlLoading += "<td class='column-primary id column-id' colspan='5' style='text-align:center;float:none;'><span class='spinner is-active' style='float:none;'></span></td>";
            htmlLoading += "</tr>";
                
            document.getElementById('table-institutes-payment').innerHTML = htmlLoading;

            const XHR= new XMLHttpRequest();
            XHR.open('POST',list_orders_sales.url,true);
            XHR.setRequestHeader('Content-type','application/x-www-form-urlencoded');
            XHR.responseType ='text';
            XHR.send('action='+ list_orders_sales.action + '&filter=' + filter + '&custom=' + custom);
            XHR.onload = function(){

                if(this.readyState=='4' && XHR.status === 200) {

                    let result = JSON.parse(XHR.responseText);

                    if (result.status == 'success') {
                        document.getElementById('table-institutes-payment').innerHTML = result.html;
                        document.getElementById('fee-alliance').innerHTML = result.data.alliance_fee;
                        document.getElementById('fee-institution').innerHTML = result.data.institute_fee;
                        document.getElementById('total').innerHTML = result.data.net_total;
                      
                        // Eliminar todos los elementos con id payment-options
                        var paymentOptions = document.querySelectorAll('#payment-options');
                        for (var i = 0; i < paymentOptions.length; i++) {
                          paymentOptions[i].remove();
                        }
                      
                        // Crear nuevos elementos dentro de card-totals-sales
                        var cardTotalsSales = document.getElementById('card-totals-sales');
                        Object.entries(result.data.payment_methods).forEach(element => {
                          var newElement = document.createElement('div');
                          newElement.className = 'card-report-sales';
                          newElement.id = 'payment-options';
                          newElement.innerHTML = `
                            <div>${element[0]}</div>
                            <div style="margin-top: 10px"><strong id="${element[0]}">${element[1]}</strong></div>
                          `;
                          cardTotalsSales.appendChild(newElement);
                        });
                    }
                }
            }   
        });
    }
});