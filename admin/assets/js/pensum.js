jQuery(document).ready(function ($) {
    // Inicializar select2
    $(".js-example-basic").select2();

    // Cargar valores guardados en el campo oculto subjects[]
    let hiddenSubjects = document.querySelectorAll('input[name="subjects[]"]');
    let subjects = Array.from(hiddenSubjects).map(input => input.value);

    // Función para eliminar un subject del arreglo y de la tabla
    function removeSubject(subjectId) {
        subjects = subjects.filter(id => id !== subjectId);
        document.querySelectorAll('input[name="subjects[]"]').forEach(input => {
            if (input.value === subjectId) {
                input.remove(); // Eliminar el input oculto correspondiente
            }
        });
    }

    // Manejar clic en el botón de trash
    document.querySelectorAll('.wp-list-table tbody tr').forEach(tr => {
        tr.querySelector('button').addEventListener('click', function (e) {
            e.preventDefault();
            let subjectId = tr.dataset.subjectId;
            removeSubject(subjectId); // Eliminar del arreglo y de los inputs ocultos
            tr.remove(); // Eliminar la fila de la tabla
        });
    });

    // Agregar nuevos subjects
    let add_subject_pensum = document.getElementById('add-subject-pensum');
    if (add_subject_pensum) {
        add_subject_pensum.addEventListener('click', function () {
            let subjectSelect = document.querySelector('select[name=subject_id]');
            let selectedOption = subjectSelect.options[subjectSelect.selectedIndex];
            let subject_id = selectedOption.value;

            // Validar que se haya seleccionado una materia
            if (!subject_id) {
                alert('Por favor, selecciona una materia.');
                return;
            }

            // Verificar si la materia ya fue agregada
            if (subjects.includes(subject_id)) {
                alert('This subject has already been added.');
                return;
            }

            // Obtener name, code y type desde los atributos personalizados
            let name = selectedOption.getAttribute('data-name');
            let code = selectedOption.getAttribute('data-code');
            let type = selectedOption.getAttribute('data-type');

            // Crear la nueva fila
            let tr = document.createElement('tr');
            tr.dataset.subjectId = subject_id;

            // Columnas de la tabla
            tr.innerHTML = `
                <th colspan="4">${name}</th>
                <th colspan="4">${code}</th>
                <th colspan="2">${type}</th>
                <th colspan="2" style="text-align: end">
                    <button class="button button-danger"><span class="dashicons dashicons-trash"></span></button>
                </th>
            `;

            // Crear un input hidden para el subject_id
            let hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'subjects[]';
            hiddenInput.value = subject_id;
            tr.appendChild(hiddenInput);

            // Manejar eliminación de la fila
            tr.querySelector('button').addEventListener('click', function (e) {
                e.preventDefault();
                removeSubject(subject_id); // Eliminar del arreglo y de los inputs ocultos
                tr.remove(); // Eliminar la fila de la tabla
            });

            // Agregar fila a la tabla
            document.querySelector('.wp-list-table tbody').appendChild(tr);

            // Agregar el subject_id al arreglo
            subjects.push(subject_id);

            // Limpiar el select2
            $(subjectSelect).val(null).trigger('change'); // Esto limpia el select2
        });
    }

    // Inicializar SortableJS
    const sortableList = document.getElementById('sortable-list');
    if (Sortable) {
        Sortable.create(sortableList, {
            animation: 150, // Duración de la animación en milisegundos
            ghostClass: 'sortable-ghost', // Clase CSS para el elemento fantasma
            chosenClass: 'sortable-chosen', // Clase CSS para el elemento seleccionado
            dragClass: 'sortable-drag', // Clase CSS para el elemento arrastrado
            onEnd: function (evt) {
                // Obtener el nuevo orden de los elementos en el DOM
                let newOrder = Array.from(sortableList.querySelectorAll('tr')).map(tr => tr.dataset.subjectId);
    
                // Actualizar el arreglo subjects[] con el nuevo orden
                subjects = newOrder;
    
                // Actualizar los inputs ocultos en el mismo orden
                sortableList.querySelectorAll('input[name="subjects[]"]').forEach((input, index) => {
                    input.value = subjects[index];
                });
    
                console.log('Nuevo orden:', subjects); // Para depuración
            },
        });
    }
});