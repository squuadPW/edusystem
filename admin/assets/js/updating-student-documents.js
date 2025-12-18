ajax_url = var_php.ajax_url;
translation = var_php.translation;

document.addEventListener('DOMContentLoaded', function () {

    // Función para cargar cortes según período
    function loadCuts(periodSelectId, cutSelectId) {

        const periodSelect = document.getElementById(periodSelectId);
        const cutSelect = document.getElementById(cutSelectId);

        periodSelect.addEventListener('change', function () {
            const period = this.value;

            if (period !== '') {
                // Crear objeto FormData
                const formData = new FormData();
                formData.append('action', 'get_cuts_by_period');
                formData.append('period', period);

                fetch(ajaxurl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la petición: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    // Limpiar opciones previas
                    cutSelect.innerHTML = '';
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = translation.select_cut;
                    cutSelect.appendChild(defaultOption);

                    // Agregar nuevas opciones
                    data.forEach(cut => {
                        const option = document.createElement('option');
                        option.value = cut.code;
                        option.textContent = cut.name;
                        cutSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error en fetch:', error);
                });
            } else {
                // Si no hay período seleccionado, limpiar cortes
                cutSelect.innerHTML = '';
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = translation.select_cut;
                cutSelect.appendChild(defaultOption);
            }
        });
    }

    // Inicializar para ambos selects
    loadCuts('from-period', 'from-period-cut');
    loadCuts('to-period', 'to-period-cut');

});



