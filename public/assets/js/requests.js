document.addEventListener('DOMContentLoaded', ()=>{

    /**
     * Maneja la apertura del modal de solicitud de documentos
     * 
     * Este script asigna un event listener al botón de "Nueva Solicitud" que,
     * al hacer click, muestra el modal correspondiente cambiando su estilo display.
     *
     * @listens click#add_new_request - Evento que desencadena la acción
     */
    let add_new_request = document.getElementById("add_new_request");
    if (add_new_request) {
        add_new_request.addEventListener("click", function () {
            document.getElementById("modal-request").style.display = "block";
        });
    }

    /**
     * Maneja el cierre del modal de solicitud de documentos
     * 
     * Este script asigna un event listener a todos los elementos con la clase "modal-close". 
     * Al hacer click en cualquiera de estos elementos, se oculta el modal de solicitud de documentos
     * y se reinician los valores de los campos del formulario dentro del modal. Esto asegura que
     * cada vez que se abra el modal, comience con un estado limpio y sin datos previos.
     *
     * @listens click.modal-close - Evento que desencadena la acción
     */
    let modal_close = document.querySelectorAll(".modal-close");
    if (modal_close) {
        modal_close.forEach((close) => {
            close.addEventListener("click", function () {
                document.getElementById("modal-request").style.display = "none";
                document.getElementById("modal-detail-request").style.display = "none";

                // resetea los valores
                var select_document = document.getElementById('type-document');
                if(select_document) {
                    select_document.selectedIndex = 0;
                    select_document.dispatchEvent(new Event('change'));
                }

                var reason = document.getElementById('reason');
                if(reason) reason.value = ""

                var student_id = document.getElementById('student-id');
                if(student_id) student_id.selectedIndex = 0;

                var product_id = document.getElementById('product-id');
                if(product_id) product_id.value = "";

            });
        });
    }

    /**
     * Este evento se ejecuta cuando se selecciona un tipo de documento a requerir.
     * Actualiza el precio y el producto a montar en función de la opción seleccionada.
     * 
     * Al cambiar la selección, se obtienen los atributos 'data-product_id' y 'data-price'
     * de la opción seleccionada. Luego, se actualizan los elementos del DOM que muestran
     * el precio del documento y el total. También se activa o desactiva el botón de envío
     * según si hay un producto válido seleccionado.
     *
     * @listens change#type-document - Evento que desencadena la acción
     */
    let type_document = document.getElementById('type-document');
    if( type_document ){

        type_document.addEventListener('change', (e)=>{

            const option = type_document.options[type_document.selectedIndex];
            
            const product_id = option.getAttribute('data-product_id') ?? 0;
            const price_html = option.getAttribute('data-price') ?? '';

            // modifica el precio
            let price_document = document.getElementById('price-document');
            if( price_document ) price_document.innerHTML = price_html;

            // modifica el total
            let total_document = document.getElementById('total-document');
            if( total_document ) total_document.innerHTML = price_html;

            // activa o desactiva el boton de submit
            let send_request = document.getElementById('send-request');
            if( send_request ) send_request.disabled = ( product_id == 0 );

            // modifica el total
            let product_id_input = document.getElementById('product-id');
            if( product_id_input ) product_id_input.value = product_id ?? '';

        });
    }

    /**
     * Este evento se ejecuta al enviar el formulario de solicitud de documento.
     * Previene el comportamiento por defecto del formulario y envía los datos
     * a través de una solicitud AJAX para agregar el producto al carrito.
     * Si la solicitud es exitosa, redirige al usuario a la página de checkout.
     *
     * @listens submit#form-send-request - Evento que desencadena la acción
     */
    let form_send_request = document.getElementById('form-send-request');
    if( form_send_request ) {
        form_send_request.addEventListener('submit', (e)=> {

            e.preventDefault();

            let button_submit = document.getElementById('send-request');
            if ( button_submit ) button_submit.disabled = true;
    
            const formData = new FormData(form_send_request);
            formData.append('action', 'save_document_request_metadata_cart_item');
            
            fetch(ajax_object.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                if(data.success) {
                    // Redireccionar al checkout
                    window.location.href = data.data.url; 
                } else {
                    window.location.href = window.location.href;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error al procesar la solicitud');
            });

        });
    }
});



let send_request = document.getElementById("send-request");
if ( send_request && false ) {
    send_request.addEventListener("click", function () {

        let type_id = document.querySelector('select[name="type_id"]').value;
        let reason = document.querySelector('textarea[name="reason"]').value;

        if (!type_id || !reason) {
            alert('The request type and reason fields are required.');
            return;
        }

        document.getElementById("send-request").disabled = true;
        document.getElementById("send-request").innerText = "Loading...";

        let student_id = null;
        let partner_id = null;
        let by = "Parent";

        if (document.querySelector('select[name="student_id"]')) {
            student_id = document.querySelector('select[name="student_id"]').value;
        } else {
            by = "Student";
            student_id = document.querySelector('input[name="student_id"]').value;
            partner_id = document.querySelector('input[name="partner_id"]').value;
        }

        const XHR = new XMLHttpRequest();
        XHR.open("POST", `${ajax_object.ajax_url}?action=send_request`, true);
        XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        XHR.responseType = "text";
        let params = `action=send_request`;
        if (type_id) {
            params += `&type_id=${type_id}`;
        }
        if (reason) {
            params += `&reason=${reason}`;
        }
        if (student_id) {
            params += `&student_id=${student_id}`;
        }
        if (partner_id) {
            params += `&partner_id=${partner_id}`;
        }
        if (by) {
            params += `&by=${by}`;
        }

        XHR.send(params);
        XHR.onload = function () {
        if (XHR.status === 200) {
            location.reload();
        } else {
            document.getElementById("send-request").disabled = false;
            document.getElementById("send-request").innerText = "Save";
        }
        };
    });
}

function view_details_request(type, description, response, status, created_at) {

    document.getElementById("request_type").innerText = type == "" ? "N/A" : type;
    document.getElementById("created_at").innerText =
        created_at == "" ? "N/A" : created_at;

    document.getElementById("request_description").innerText =
        description == "" ? "N/A" : description;

    document.getElementById("request_response").innerText =
        response == "" ? "N/A" : response;

    document.getElementById("request_status").innerText =
        status == "" ? "N/A" : status;

    document.getElementById("modal-detail-request").style.display = "block";
}
