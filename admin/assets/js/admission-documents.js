document.addEventListener('DOMContentLoaded', ()=>{
    
    jQuery('#select_scope').select2({
        placeholder: "Select a program",
        width: '100%',
        templateResult: function(option) {

            // Si tiene clase en el <option>, la copiamos
            const $span = jQuery('<span></span>')
                .text(option.text);

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

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.name = 'scope_required[]';
            checkbox.value = option.value;

            // Marcar automáticamente si el <option> tiene data-required="true"
            if (option.getAttribute('data-required') === 'true') checkbox.checked = true;

            left.appendChild(name);
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

    jQuery('#select_scope').trigger('change');

});