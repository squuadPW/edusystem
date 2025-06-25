document.addEventListener('DOMContentLoaded', function() {

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

           // Actualizar el atributo data-subprogram_count en el contenedor
            subprograms.setAttribute('data-subprogram_count', subprogram_count);

            // Agregar el nuevo subprograma al contenedor
            subprograms.appendChild(new_subprogram);
        });
    }


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

            // Actualizar el atributo data-subprogram_count en el contenedor
            rules.setAttribute('data-subprogram_count', rules_count);

            // Agregar el nueva regla al contenedor
            rules.appendChild(new_rule);
        });
    }

    
});




