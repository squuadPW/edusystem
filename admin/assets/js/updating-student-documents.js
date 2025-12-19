ajax_url = var_php.ajax_url;
translation = var_php.translation;

document.addEventListener('DOMContentLoaded', function () {

    // Función para cargar cortes según período
    function loadCuts(period_select_id, cut_select_id) {

        const period_select = document.getElementById(period_select_id);
        const cut_select = document.getElementById(cut_select_id);

        function fetchCuts(period) {
            const formData = new FormData();
            formData.append('action', 'get_cuts_by_period');
            formData.append('period', period);

            fetch(ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {

                cut_select.innerHTML = '';

                const default_option = document.createElement('option');
                default_option.value = '';
                default_option.textContent = translation.select_cut;
                cut_select.appendChild(default_option);

                const selected_value = cut_select.getAttribute('data-selected'); 

                data.forEach(cut => {
                    const option = document.createElement('option');
                    option.value = cut.id;
                    option.textContent = cut.cut;
                    if (selected_value && selected_value == cut.id) option.selected = true; 
                    cut_select.appendChild(option);
                });
            });
        }

        // Al cambiar período
        period_select.addEventListener('change', function () {
            if (this.value !== '') {
                fetchCuts(this.value);
            } else {
                cut_select.innerHTML = '';
            }
        });

        // Si ya hay un período seleccionado en GET, cargar cortes al inicio
        if ( period_select.value !== '' ) fetchCuts(period_select.value);
    }

    loadCuts('from-period', 'from-period-cut');
    loadCuts('to-period', 'to-period-cut');

});



