document.addEventListener('DOMContentLoaded', ()=>{

    let generate_quote = document.getElementById("generate-quote");
    if (generate_quote) {
        generate_quote.addEventListener("click", (e) => {
            generate_quote.style.width = '140px';
            document.getElementById("generate-quote").disabled = true;
            document.getElementById("generate-quote").innerText = "Loading...";

            let payment_id = generate_quote.getAttribute("data-payment-id");

            const XHR = new XMLHttpRequest();
            XHR.open(
                "POST",
                `${ajax_object.ajax_url}?action=generate_quote_public`,
                true
            );
            XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            XHR.responseType = "json";

            let params = `action=generate_quote_public`;
            // if (student_id) {
            //   params += `&student_id=${student_id}`;
            // }
            // if (amount) {
            //   params += `&amount=${amount}`;
            // }
            if (payment_id) {
                params += `&payment_id=${payment_id}`;
            }

            XHR.send(params);
            XHR.onload = function () {
                if (XHR.status === 200) {
                    let data = XHR.response.data;
                    window.location.href = data.url;
                } else {
                    document.getElementById("generate-quote").disabled = false;
                    document.getElementById("generate-quote").innerText = "Pay";
                    generate_quote.style.width = '70px';
                }
            };
        });
    }
});
