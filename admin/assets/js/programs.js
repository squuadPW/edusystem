document.addEventListener('DOMContentLoaded', function() {

    /**
     * Clona la plantilla de formulario para los subprogramas y habilita los inputs,
     * asignando el número de posición correspondiente en el atributo 'name'.
     * Esta función se activa al hacer clic en el botón para agregar subprogramas.
     * 
     * @return {void} No retorna ningún valor.
     */
    subprograms = document.getElementById('subprograms');
    if ( subprograms ) {

        let subprogram_count = parseInt( subprograms.getAttribute('data-subprogram_count') ?? 0 ) ;
        document.getElementById('add-subprograms').addEventListener('click', function() {
            
            // Incrementar el contador para el siguiente subprograma
            subprogram_count++;

            const subprogram_template = document.getElementById('template-subprogram');

            // Clonar el template
            const new_subprogram = subprogram_template.cloneNode(true);

            // Quitar el atributo 'disabled' y modificar 'name' y 'for'
            new_subprogram.querySelectorAll('input, label').forEach(elem => {

                // Modificar los atributos 'name' y 'for' solo si son inputs o labels
                if (elem.tagName.toLowerCase() === 'input') {
                    // Obtener el nombre actual y reemplazar los corchetes
                    const currentName = elem.getAttribute('name');
                    const newName = currentName.replace(/\[\]/, `[${subprogram_count}]`);
                    elem.setAttribute('name', newName);

                    // Quitar el atributo 'disabled' si existe
                    elem.removeAttribute('disabled');

                } else if (elem.tagName.toLowerCase() === 'label') {
                    // Obtener el 'for' actual y reemplazar los corchetes
                    const currentFor = elem.getAttribute('for');
                    const newFor = currentFor.replace(/\[\]/, `[${subprogram_count}]`);
                    elem.setAttribute('for', newFor);
                }
            });

            //quita el id del template
            new_subprogram.removeAttribute('id');

            // Agregar funcionalidad al botón de eliminar
            const remove_button = new_subprogram.querySelector('.remove-rule-button');
            remove_button.addEventListener('click', function() {
                new_subprogram.remove(); 
            });

           // Actualizar el atributo data-subprogram_count en el contenedor
            subprograms.setAttribute('data-subprogram_count', subprogram_count);

            // Agregar el nuevo subprograma al contenedor
            subprograms.appendChild(new_subprogram);
        });
    }

    /**
     * Clona la plantilla de formulario para las reglas de cuotas y habilita los inputs,
     * asignando el número de posición correspondiente en el atributo 'name'.
     * Esta función se activa al hacer clic en el botón para agregar reglas de cuotas.
     * 
     * @return {void} No retorna ningún valor.
     */
    rules = document.getElementById('rules');
    if ( rules ) {

        let rules_count = parseInt( rules.getAttribute('data-rules_count') ?? 0 );
        document.getElementById('add-rule-button').addEventListener('click', function() {
            
            // Incrementar el contador para el siguiente regla
            rules_count++;

            const rule_template = document.getElementById('template-quota-rule');

            // Clonar el template
            const new_rule = rule_template.cloneNode(true);

            // Quitar el atributo 'disabled' y modificar 'name' y 'for'
            new_rule.querySelectorAll('input, label').forEach( elem => {

                // Modificar los atributos 'name' y 'for' solo si son inputs o labels
                if ( elem.tagName.toLowerCase() === 'input') {

                    // Obtener el nombre actual y reemplazar los corchetes
                    const current_name = elem.getAttribute('name');
                    const new_name = current_name.replace(/\[\]/, `[${rules_count}]`);
                    elem.setAttribute('name', new_name);

                    // Quitar el atributo 'disabled' si existe
                    elem.removeAttribute('disabled');

                } else if (elem.tagName.toLowerCase() === 'label') {
                    // Obtener el 'for' actual y reemplazar los corchetes
                    const current_for = elem.getAttribute('for');
                    const new_for = current_for.replace(/\[\]/, `[${rules_count}]`);
                    elem.setAttribute('for', new_for);
                }
            });

            //quita el id del template
            new_rule.removeAttribute('id');

            // Agregar funcionalidad al botón de eliminar
            const remove_button = new_rule.querySelector('.remove-rule-button');
            remove_button.addEventListener('click', function() {
                new_rule.remove(); 
            });

            // Actualizar el atributo data-rules_count en el contenedor
            rules.setAttribute('data-rules_count', rules_count);

            // Agregar el nueva regla al contenedor
            rules.appendChild(new_rule);
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
    document.querySelectorAll('.modal-close').forEach((close) => {
        close.addEventListener('click',(e) => {

            modal_delete_quota_rule = document.getElementById('modalDeleteQuotaRule');
            if( modal_delete_quota_rule ){
                modal_delete_quota_rule.style.display = "none";
            }

            modal_delete_subprogram = document.getElementById('modalDeleteSubprogram');
            if( modal_delete_subprogram ){
                modal_delete_subprogram.style.display = "none";
            }

            modal_delete_program = document.getElementById('modalDeleteProgram');
            if( modal_delete_program ){
                modal_delete_program.style.display = "none";
            }

        });
    });

});

/**
 * Abre el modal para eliminar una regla de cuota.
 * Esta función se ejecuta al hacer clic en un botón de eliminación y configura
 * el modal con el ID de la regla de cuota que se va a eliminar.
 * 
 * @param {HTMLElement} button Elemento HTML del botón que debe contener
 *                             el atributo 'data-rule_id' con el ID de la regla.
 * 
 * @return {void} No retorna ningún valor.
 */
function modal_delete_quota_rule_js ( button ) {

    let modal_delete_quota_rule = document.getElementById( 'modalDeleteQuotaRule' );
    if( modal_delete_quota_rule ) {
        id = button.getAttribute('data-rule_id');
        modal_delete_quota_rule.querySelector('#delete_quota_rule_input').value = id;
        modal_delete_quota_rule.style.display = "block";
    }
}

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
function modal_delete_subprogram_js ( button ) {

    let modal_delete_subprogram = document.getElementById( 'modalDeleteSubprogram' );
    if( modal_delete_subprogram ) {
        id = button.getAttribute('data-subprogram_id');
        modal_delete_subprogram.querySelector('#delete_subprogram_input').value = id;
        modal_delete_subprogram.style.display = "block";
    }
}

/**
 * Abre el modal para eliminar un programa.
 * Esta función se activa al hacer clic en un botón de eliminación
 * y establece el ID del programa en el campo de entrada del modal.
 * 
 * @param HTMLElement button Botón que activó el modal, que debe contener
 *                           el atributo 'data-program_id' con el ID del programa.
 * 
 * @return void No retorna ningún valor.
 */
function modal_delete_program_js ( button ) {

    let modal_delete_program = document.getElementById( 'modalDeleteProgram' );
    if( modal_delete_program ) {
        id = button.getAttribute('data-program_id');
        modal_delete_program.querySelector('#delete_program_input').value = id;
        modal_delete_program.style.display = "block";
    }
}

/**
* Valida un input según una expresión regular
* @param {HTMLinput} input - El elemento input a validar
* @param {string} regex_pattern - Patrón regex para validación
* @param {boolean} [convert_to_upper=true] - Convertir automáticamente a mayúsculas
* @param {number} [maxLength=0] - Longitud máxima (0 para ilimitado)
*/
function validate_input(input, regex_pattern, convert_to_upper = true, max_length = 0) {

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
    const last_char = input.value.slice(-1);
            
    // Si el último carácter no cumple con el regex, eliminarlo
    if (input.value && !regex.test(last_char)) {
        input.value = input.value.slice(0, -1);
    }
}



