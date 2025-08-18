url_ajax = ajax_object.url_ajax;

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

            modal_delete_cut = document.getElementById('modalDeleteCut');
            if( modal_delete_cut ) modal_delete_cut.style.display = "none";

            modal_delete_period = document.getElementById('modalDeletePeriod');
            if( modal_delete_period ) modal_delete_period.style.display = "none";

        });
    });

});


/**
 * Abre el modal para eliminar un subprograma.
 * Esta función se activa al hacer clic en un botón de eliminación
 * y establece el identificador del subprograma en el campo de entrada del modal.
 * 
 * @param HTMLElement button Botón que activó el modal, que debe contener
 *                           el atributo 'data-cut_id' con el ID del corte,
 *                           'data-cut' con el codigo del corte y 
 *                           'data-period_code' con el codigo del periodo. 
 * 
 * @return void No retorna ningún valor.
 */
function modal_delete_cut_js ( button ) {

    let modal_delete_cut = document.getElementById( 'modalDeleteCut' );
    if( modal_delete_cut ) {

        cut_id = button.getAttribute('data-cut_id');
        modal_delete_cut.querySelector('#delete_cut_id_input').value = cut_id;

        cut = button.getAttribute('data-cut');
        modal_delete_cut.querySelector('#delete_cut_input').value = cut;
        
        period_code = button.getAttribute('data-period_code');
        modal_delete_cut.querySelector('#delete_period_code_input').value = period_code;

        modal_delete_cut.style.display = "block";
    }
}

/**
 * Abre el modal para eliminar un subprograma.
 * Esta función se activa al hacer clic en un botón de eliminación
 * y establece el identificador del subprograma en el campo de entrada del modal.
 * 
 * @param HTMLElement button Botón que activó el modal, que debe contener
 *                           el atributo 'data-period_id' con el ID del periodo,
 *                           'data-period_code' con el codigo del periodo.
 * 
 * @return void No retorna ningún valor.
 */
function modal_delete_period_js ( button ) {

    let modal_delete_period = document.getElementById( 'modalDeletePeriod' );
    if( modal_delete_period ) {

        period_id = button.getAttribute('data-period_id');
        modal_delete_period.querySelector('#delete_period_id_input').value = period_id;

        period_code = button.getAttribute('data-period_code');
        modal_delete_period.querySelector('#delete_period_code_input').value = period_code;

        modal_delete_period.style.display = "block";
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
let 
/**
 * Valida si el código del periodo ya existe.
 * @param {HTMLInputElement} input - El elemento input que contiene el código del periodo a validar.
 */
function check_periods_code_exists_js( input ){

    let error_period_code = document.getElementById('error-period-code');
    error_period_code.innerHTML = "";

    period_code = input.value.trim();

    if ( timeout_id ) clearTimeout( timeout_id );

    // Si ya hay un controlador en ejecución, lo abortamos
    if ( controller_validate_code ) controller_validate_code.abort();

    if ( period_code.length >= 3 ) {

        // Creamos un nuevo AbortController
        controller_validate_code = new AbortController();
        const signal = controller_validate_code.signal;

        timeout_id = setTimeout(function() {

            const formData = new FormData();
            formData.append('action', 'check_period_code_exists');
            formData.append('code', period_code );
            
            fetch( 
                url_ajax, {
                method: 'POST',
                body: formData,
                signal
            })
            .then( res => res.json() )
            .then( res => {
   
                error_period_code.innerHTML = "";
                if ( res.success ){

                    if ( res.data.exists ) {
                        error_period_code.innerHTML = res.data.message;
                        error_period_code.style.display = 'block';
                        document.getElementById('save-period').disabled = true;

                    } else {
                        error_period_code.style.display = 'none'
                        document.getElementById('save-period').disabled = false;
                    }
                }
                    
            })
            .catch( err => {} ); 
        
        }, 0);    
    }

}

let controller_validate_cut
/**
 * Valida si el corte ya existe en otros inputs.
 * @param {HTMLInputElement} input - El elemento input que contiene el corte a validar.
 */
function check_cut_exists_js( input ){

    document.getElementById('save-period').disabled = false;

    //apaga los selectores de error
    document.querySelectorAll(' .input-error').forEach(error => 
        error.style.display = 'none'
    );

    let error_cut = input.parentElement.querySelector(' .input-error');

    cut = input.value.trim();

    if ( timeout_id ) clearTimeout( timeout_id );

    // Si ya hay un controlador en ejecución, lo abortamos
    if ( controller_validate_cut ) controller_validate_cut.abort();

    if ( cut.length >= 3 ) {

        // Creamos un nuevo AbortController
        controller_validate_cut = new AbortController();
        const signal = controller_validate_cut.signal;

        timeout_id = setTimeout(function() {

            let in_use_cut = false;

            // busca si hay otro input de valor cut con ese valor
            const cuts = document.getElementById('cuts');
            cuts.querySelectorAll('[name*="][cut]"]').forEach(elem => {

                if( elem != input ){
                    if( elem.value.trim() === cut ) { 
                        in_use_cut = true; 
                        return;
                    }
                }

            });

            // activa o desactiva el botón de guardar
            document.getElementById('save-period').disabled = in_use_cut;

            // muestra o quita el mensaje de error
            error_cut.style.display = in_use_cut ? 'block' : 'none';

        }, 0);
    }

}

