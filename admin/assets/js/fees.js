document.addEventListener('DOMContentLoaded', () => {
    
    jQuery('#programas_select').select2({
        placeholder: "Select a program",
        width: '100%',
        templateResult: function(option) {
            return ( jQuery('#programas_select').val().includes(option.id) ) ? null : option.text;
        }
    });
    
});

function modal_delete_fee_js( button ){

    let modal_delete_fee = document.getElementById( 'modalDeleteAdmissionFee' );
    if( modal_delete_fee ) {
        id = button.getAttribute('data-fee_id');
        modal_delete_fee.querySelector('#delete_fee_input').value = id;
        modal_delete_fee.style.display = "block";
    }
}

