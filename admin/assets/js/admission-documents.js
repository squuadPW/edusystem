document.addEventListener('DOMContentLoaded', ()=>{
    
    // Declaracion y evento del select scope
    jQuery('#select_scope').select2({
        minimumResultsForSearch: 0, // fuerza que aparezca el buscador siempre
        width: '100%',
        templateResult: function(option) {

            // Si tiene clase en el <option>, la copiamos
            const $span = jQuery('<span></span>').text(option.text);

            if (option.element && option.element.className) {
                $span.addClass(option.element.className);
            }

            return $span;
        }

    }).on('change', function (e) {

        const selectedList = document.getElementById('selected_list');
        const select_scope = this;
        
        selectedList.innerHTML = '';

        [...select_scope.selectedOptions].forEach(option => {
            const item = document.createElement('div');
            item.className = 'selected-item';

            // Contenedor nombre + checkbox
            const left = document.createElement('div');
            left.className = 'item-left';

            const name = document.createElement('span');
            name.className = 'item-name';
            name.textContent = option.text;
            left.appendChild(name);

            const data_type = option.getAttribute('data-type');
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = `academic_scope[${data_type}][${option.value}][name]`;
            hidden.value = option.value; 
            left.appendChild(hidden);

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.name = `academic_scope[${data_type}][${option.value}][required]`;
            checkbox.value = true;

            // Marcar automáticamente si el <option> tiene data-required="true"
            if (option.getAttribute('data-required') === 'true') checkbox.checked = true;
            left.appendChild(checkbox);

            // Botón eliminar flotante
            const removeBtn = document.createElement('button');
            removeBtn.className = 'remove-btn';
            removeBtn.textContent = '✖';
            removeBtn.onclick = () => {
                option.selected = false;
                item.remove();
                
                // Refresca select2 para que desaparezca visualmente
                jQuery('#select_scope').trigger('change');
            };

            item.appendChild(left);
            item.appendChild(removeBtn);

            selectedList.appendChild(item);
        });
    });

    //Actualiza el select scope
    jQuery('#select_scope').trigger('change');

    // Eventos para cerar el modal 'modalDeleteDocument'
    const closeButtons = document.querySelectorAll('#modalDeleteDocument .modal-close');
    closeButtons.forEach(btn => {
        btn.addEventListener('click', close_delete_modal);
    });

});

/*
* Función para abrir el modal y setear datos dinámicos
*/
function open_delete_modal( document_id , document_name) {
    const modal = document.getElementById('modalDeleteDocument');
    const document_name_container = document.getElementById('modal_document_name');
    const document_id_nput = document.getElementById('modal_document_id');

    // Asignar valores dinámicos
    if( document_id_nput ) document_id_nput.value = document_id;
    if( document_name_container ) document_name_container.textContent = document_name;

    // Mostrar modal
    if( modal ) modal.style.display = 'block';
}

/*
* Función para cerrar el modal y reiniciar los datos
*/
function close_delete_modal() {
    const modal = document.getElementById('modalDeleteDocument');
    const document_name_container = document.getElementById('modal_document_name');
    const document_id_nput = document.getElementById('modal_document_id');

    // Asignar valores dinámicos
    if( document_id_nput ) document_id_nput.value = '';
    if( document_name_container ) document_name_container.textContent = '';

    // Mostrar modal
    modal.style.display = 'none';
}

  