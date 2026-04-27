document.addEventListener('DOMContentLoaded',function(){

    delete_department = document.getElementById('delete_department');

    if(delete_department){

        delete_department.addEventListener('click',(e) => {

            id = delete_department.getAttribute('data-id');
            document.getElementById('delete_department_id').value = id;
            document.getElementById('modalDeleteDepartment').style.display = "block";
        });
    }

    document.querySelectorAll('.modal-close').forEach((close) => {
        close.addEventListener('click',(e) => {
            document.getElementById('modalDeleteDepartment').style.display = "none";
        });
    });

    capability_items = document.querySelectorAll('.capability-item');
    if(capability_items) {
        capability_items.forEach((item) => {
            
            if( item.querySelector('ul') ) {

                item.querySelector('input[type="checkbox"]').addEventListener('change',(e) => {
                    let checked = e.target.checked ?? false;
                    item.querySelectorAll('ul input[type="checkbox"]').forEach((subitem) => {

                        if ( subitem.hasAttribute('capability-disabled') ) {
                            subitem.checked = false;
                        } else {
                            subitem.checked = checked;
                        }

                        // Disparar evento change para que se ejecuten otros listeners
                        subitem.dispatchEvent(new Event('change', { bubbles: true }));
                    });
                });
            }

            // Manejar checkboxes que activan otros (con data-activates)
            const checkbox = item.querySelector('input[type="checkbox"]');
            if ( checkbox && checkbox.hasAttribute('data-activates') ) {
                checkbox.addEventListener('change', (e) => {
                    const activates_list = checkbox.getAttribute('data-activates');
                    if (activates_list) {

                        const ids = activates_list.split(',');
                        ids.forEach(id => {
                            const target_checkbox = document.getElementById(id.trim());
                            if (target_checkbox) {

                                // Solo marcar si no tiene capability-disabled
                                if ( checkbox.checked && !target_checkbox.hasAttribute('capability-disabled')) {

                                    target_checkbox.checked = true;

                                    // Disparar evento change para que se ejecuten otros listeners
                                    target_checkbox.dispatchEvent(new Event('change', { bubbles: true }));
                                }
                            }
                        });

                    }
                });
            }

        })
    }

});


