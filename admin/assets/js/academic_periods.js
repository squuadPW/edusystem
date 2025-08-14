document.addEventListener('DOMContentLoaded', function() {

    /**
     * Clona la plantilla de formulario para los cortes y habilita los inputs,
     * asignando el número de posición correspondiente en el atributo 'name'.
     * Esta función se activa al hacer clic en el botón para agregar subprogramas.
     * 
     * @return {void} No retorna ningún valor.
     */
    cuts = document.getElementById('cuts');
    if ( cuts ) {

        let cuts_count = parseInt( cuts.getAttribute('data-cuts_count') ?? 0 ) ;
        document.getElementById('add-cuts').addEventListener('click', function() {
            
            // Incrementar el contador para el siguiente corte
            cuts_count++;

            const cut_template = document.getElementById('template-cut');
            const new_cut = cut_template.cloneNode(true);// Clonar el template

            // Quitar el atributo 'disabled' y modificar 'name' y 'for'
            new_cut.querySelectorAll('input, label').forEach(elem => {

                // Modificar los atributos 'name' y 'for' solo si son inputs o labels
                if (elem.tagName.toLowerCase() === 'input') {
                    // Obtener el nombre actual y reemplazar los corchetes
                    const currentName = elem.getAttribute('name');
                    if( currentName ) {
                        const newName = currentName.replace(/\[\]/, `[${cuts_count}]`);
                        elem.setAttribute('name', newName);
                    }

                    // Quitar el atributo 'disabled' si existe
                    elem.removeAttribute('disabled');

                } else if (elem.tagName.toLowerCase() === 'label') {
                    // Obtener el 'for' actual y reemplazar los corchetes
                    const currentFor = elem.getAttribute('for');
                    if( currentFor ){
                        const newFor = currentFor.replace(/\[\]/, `[${cuts_count}]`);
                        elem.setAttribute('for', newFor);
                    }
                    
                }
            });

            //quita el id del template
            new_cut.removeAttribute('id');

            // Agregar funcionalidad al botón de eliminar
            const remove_button = new_cut.querySelector('.remove-rule-button');
            remove_button.addEventListener('click', function() {
                new_cut.remove(); 
            });

           // Actualizar el atributo data-cuts_count en el contenedor
            cuts.setAttribute('data-cuts_count', cuts_count);

            // Agregar el nuevo corte al contenedor
            cuts.appendChild(new_cut);
        });
    }

    /**
     * Agrega funcionalidad a los botones de cierre de los modales.
     * Esta función busca todos los elementos con la clase 'modal-close'
     * y les asigna un evento de clic que oculta los modales correspondientes
     * al ser activados.
     * 
     * @return {void} No retorna ningún valor.
     */
    document.querySelectorAll('.modal-close').forEach( (close) => {
        close.addEventListener('click',(e) => {

            modal_delete_program = document.getElementById('modalDeleteCut');
            if( modal_delete_program ) modal_delete_program.style.display = "none";

        });
    });

});


/**
 * Abre el modal para eliminar un subprograma.
 * Esta función se activa al hacer clic en un botón de eliminación
 * y establece el identificador del subprograma en el campo de entrada del modal.
 * 
 * @param HTMLElement button Botón que activó el modal, que debe contener
 *                           el atributo 'data-subprogram_id' con el ID del subprograma.
 * 
 * @return void No retorna ningún valor.
 */
function modal_delete_cut_js ( button ) {

    let modal_delete_cut = document.getElementById( 'modalDeleteCut' );
    if( modal_delete_cut ) {
        id = button.getAttribute('data-cut_id');
        modal_delete_cut.querySelector('#delete_cut_input').value = id;
        modal_delete_cut.style.display = "block";
    }
}

/**
* Valida un input según una expresión regular
* @param {HTMLinput} input - El elemento input a validar
* @param {string} regex_pattern - Patrón regex para validación
* @param {boolean} [convert_to_upper=true] - Convertir automáticamente a mayúsculas
* @param {number} [maxLength=0] - Longitud máxima (0 para ilimitado)
*/
function validate_input(input, regex_pattern, convert_to_upper = false, max_length = 0) {

    // Limitar longitud si max_length > 0
    if (max_length > 0 && input.value.length > max_length) {
        input.value = input.value.substring(0, max_length);
        return;
    }
    
    // Convertir a mayúsculas si está habilitado
    if (convert_to_upper) {
        input.value = input.value.toUpperCase();
    }
    
    const regex = new RegExp(regex_pattern);
    
    // Validar toda la cadena de entrada
    if ( input.value && !regex.test(input.value) ) {
        // Si no cumple con el regex, eliminar el último carácter
        input.value = input.value.slice(0, -1);
    }
}

let timeout_id = null;
let controller_validate_identificator
function check_periods_identificator_exists_js( input ){

    return

    let error_identificator = document.getElementById('error-identificator');
    error_identificator.innerHTML = "";

    identificator = input.value.trim();

    if ( timeout_id ) clearTimeout( timeout_id );

    // Si ya hay un controlador en ejecución, lo abortamos
    if ( controller_validate_identificator ) controller_validate_identificator.abort();

    if ( identificator.length >= 3 ) {

        // Creamos un nuevo AbortController
        controller_validate_identificator = new AbortController();
        const signal = controller_validate_identificator.signal;

        timeout_id = setTimeout(function() {

            const formData = new FormData();
            formData.append('action', 'check_program_identificator_exists');
            formData.append('identificator', identificator );

            fetch( 
                url_ajax, {
                method: 'POST',
                body: formData,
                signal
            })
            .then( res => res.json() )
            .then( res => {
   
                error_identificator.innerHTML = "";
                if ( res.success ){
                    if ( res.data.exists ) {
                        error_identificator.innerHTML = res.data.message;
                        error_identificator.style.display = 'block'
                        // document.getElementById('send-application').disabled = true;

                    } else if ( res.message === "" ) {
                        error_identificator.style.display = 'none'
                        // document.getElementById('send-application').disabled = false;
                    }
                }
                    
            })
            .catch( err => {} ); 
        
        }, 0);    
    }

}

