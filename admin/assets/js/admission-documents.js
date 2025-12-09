document.addEventListener('DOMContentLoaded', ()=>{

    
    const selectedList = document.getElementById('selected_list');
    jQuery('#select_scope').select2({
        placeholder: "Select a program",
        width: '100%',
        templateResult: function(option) {
            return ( jQuery('#select_scope').val().includes(option.id) ) ? null : option.text;
        }
    }).on('change', function (e) {

        selectedList.innerHTML = '';

        [...selectScope.selectedOptions].forEach(option => {
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
            checkbox.name = `required_${option.value}`;
            checkbox.title = "Marcar si es requerido";

            left.appendChild(name);
            left.appendChild(checkbox);

            // Botón eliminar flotante
            const removeBtn = document.createElement('button');
            removeBtn.className = 'remove-btn';
            removeBtn.textContent = '✖';
            removeBtn.onclick = () => {
                option.selected = false;
                item.remove();
            };

            item.appendChild(left);
            item.appendChild(removeBtn);

            selectedList.appendChild(item);
        });
    });
});