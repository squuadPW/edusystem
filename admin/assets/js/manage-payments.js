jQuery(document).ready(function ($) {
    // Agregar estilos CSS
    $('<style>')
        .text(`
            .select2-result {
                padding: 8px 12px;
                border-bottom: 1px solid #eee;
            }
            .select2-result-main {
                font-weight: 600;
                color: #333;
                margin-bottom: 4px;
            }
            .select2-result-description {
                font-size: 0.9em;
                color: #666;
            }
            .select2-container--default .select2-results__option--highlighted[aria-selected] {
                background-color: #f8f9fa;
                color: #333;
            }
            .select2-container--default .select2-results__option[aria-selected=true] {
                background-color: #e9ecef;
            }
        `)
        .appendTo('head');

    $('.js-example-basic').select2({
        ajax: {
            url: `${manage_payments.url}`,       // URL del servicio
            dataType: 'json',         // Tipo de datos esperado
            type: 'POST',
            delay: 250,               // Retraso en ms después de teclear
            data: function(params) {   // Parámetros de búsqueda
                return {
                    q: params.term,    // Término de búsqueda
                    action: manage_payments.action,    // Término de búsqueda
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                
                return {
                    results: data.items,      // Resultados formateados
                };
            },
            cache: true
        },
        minimumInputLength: 2,        // Mínimo de caracteres para buscar
        placeholder: 'Buscar...',     // Texto placeholder
        templateResult: formatResult // Función para formatear resultados
    }).on('change', function(e) {
        // Obtener el valor seleccionado
        const selectedValue = $(this).val();
        document.getElementById('id_document').value = selectedValue;
        
        // Aquí puedes hacer lo que necesites con el valor seleccionado
        // Por ejemplo, hacer una llamada AJAX o actualizar otros elementos
    });

    // Función para formatear la presentación de resultados
    function formatResult(repo) {
        if (repo.loading) return repo.text;

        const $container = $('<div class="select2-result">');
        const $main = $('<div class="select2-result-main">').text(repo.text);
        const $description = $('<div class="select2-result-description">').text(repo.description || '');
        
        $container.append($main);
        if (repo.description) {
            $container.append($description);
        }

        return $container;
    }
});