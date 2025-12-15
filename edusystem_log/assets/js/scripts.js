ajax_url = log_data.ajax_url;
translations = log_data.translations;

document.addEventListener('DOMContentLoaded', () => {
    const date_range = document.getElementById('date-range');
    if( date_range )  edusystem_date_range_js( date_range );
});

function edusystem_date_range_js( input ){
    const fp =  flatpickr(input, {
        mode: "range",
        dateFormat: "m-d-Y",
        onChange: function( selectedDates, dateStr, instance ) {

            if (selectedDates.length === 2 && selectedDates[0] instanceof Date && 
                selectedDates[1] instanceof Date) {

                const startDate = instance.formatDate(selectedDates[0], "m-d-Y");
                const endDate = instance.formatDate(selectedDates[1], "m-d-Y");

                if( startDate !== '' && endDate !== '' ){
                    edusystem_filters_transactions( 'date','custom', startDate, endDate );
                }
            } else if (selectedDates.length === 0) {
                instance.open();
            }
        }
    });

    return fp;
}

function edusystem_date_filter_transactions(value){

    if( value === 'custom' ){

        const date_range = document.getElementById('date-range');
        const selectElement = document.getElementById('select-date');
        const selectRect = selectElement.getBoundingClientRect();

        const fp = edusystem_date_range_js(date_range);

        fp.open();

        const calendarElement = document.querySelector('.flatpickr-calendar.open');
        x = 0;
        y = 0;
        if(window.innerWidth > 700){

            x = selectRect.left + (selectRect.width - calendarElement.offsetWidth);
            y = selectRect.top ;

        } else {
            x = 10;
            y = 650 ;
        }

        calendarElement.style.position = 'absolute';
        calendarElement.style.top = `${y}px`;
        calendarElement.style.left = `${x}px`;

        // coloca el select a la posision anterior por si no selecciona ninguna fecha
        const urlParams = new URLSearchParams(window.location.search);
        const getValue = urlParams.get('date');
        let filter = 'today'
        if (getValue !== null) {
            filter = getValue
        }

        selectElement.value = filter;
        

    } else if( value !== '' ) {
        document.getElementById('date-range').style.display = 'none'
        edusystem_filters_transactions('date',value);
    }
    
}

function edusystem_filters_transactions( param, value, startdate ='', enddate = ''){

    let url = window.location.href;
    const urlParts = url.split('?');
    const baseUrl = urlParts[0];
    const queryParams = urlParts[1] || '';

    const params = new URLSearchParams(queryParams);

    if( startdate !== '' && enddate !== '' ){
        params.set('startDate', startdate);
        params.set('endDate', enddate);
    } else if( !params.has('startDate') || !params.has('endDate') ) {
        params.delete("startDate");
        params.delete("endDate");
    }

    params.set(param, value);
    newUrl = `${baseUrl}?${params.toString()}`;
    window.location.href = newUrl;
}

jQuery(document).ready(function($){
    // Validar que exista el input con id user_id
    if (jQuery('#user_id').length) {
        jQuery('#user_id').select2({
            placeholder: translations.select_user,
            allowClear: true,
            ajax: {
                url: ajaxurl, // endpoint de WP admin AJAX
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        action: 'edusystem_search_users',
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            let fullName = (item.first_name ? item.first_name : '') + ' ' + (item.last_name ? item.last_name : '');
                            fullName = fullName.trim() || item.user_email;
                            return {
                                id: item.ID,
                                text: fullName + ' (' + item.user_email + ')'
                            };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });
    }

    jQuery('#start_date, #end_date').on('change input', edusystem_toggle_required);

});

function edusystem_toggle_required() {
    const startVal = jQuery('#start_date').val();
    const endVal   = jQuery('#end_date').val();

    if (startVal && !endVal) {
        jQuery('#end_date').attr('required', true);
    } else {
        jQuery('#end_date').removeAttr('required');
    }

    if (endVal && !startVal) {
        jQuery('#start_date').attr('required', true);
    } else {
        jQuery('#start_date').removeAttr('required');
    }
}


