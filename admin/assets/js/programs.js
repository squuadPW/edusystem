document.addEventListener('DOMContentLoaded', function() {

    subprograms = document.getElementById('subprograms');

    let rule_count = parseInt( subprograms.getAttribute('data-subprogram_count') ) ?? 0;

    

    document.getElementById('add-subprograms').addEventListener('click', function() {
        
        // Incrementar el contador para el siguiente subprograma
        rule_count++;

        const subprogram_template = document.getElementById('template-subprogram');

        // Clonar el template
        const new_subprogram = subprogram_template.cloneNode(true);

        // Quitar el atributo 'disabled' y modificar 'name' y 'for'
        new_subprogram.querySelectorAll('input, label').forEach(elem => {

            // Modificar los atributos 'name' y 'for' solo si son inputs o labels
            if (elem.tagName.toLowerCase() === 'input') {
                // Obtener el nombre actual y reemplazar los corchetes
                const currentName = elem.getAttribute('name');
                const newName = currentName.replace(/\[\]/, `[${rule_count}]`);
                elem.setAttribute('name', newName);

                // Quitar el atributo 'disabled' si existe
                elem.removeAttribute('disabled');

            } else if (elem.tagName.toLowerCase() === 'label') {
                // Obtener el 'for' actual y reemplazar los corchetes
                const currentFor = elem.getAttribute('for');
                const newFor = currentFor.replace(/\[\]/, `[${rule_count}]`);
                elem.setAttribute('for', newFor);
            }
        });

        //quita el id del template
        new_subprogram.removeAttribute('id');

       // Actualizar el atributo data-subprogram_count en el contenedor
        subprograms.setAttribute('data-subprogram_count', rule_count);

        // Agregar el nuevo subprograma al contenedor
        subprograms.appendChild(new_subprogram);
    });
});




