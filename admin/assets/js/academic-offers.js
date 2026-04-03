ajax_url = ajax_object.ajax_url;

jQuery(document).ready(function ($) {
    $(".js-example-basic").select2();
});

document.addEventListener("DOMContentLoaded", function () {
    
    const code_period = document.getElementById("code_period");
    if( code_period ) {
        code_period.addEventListener("change", function (event) {

            cut_period = document.getElementById('cut_period');
            cut_period.innerHTML = "";

            let opt_default = document.createElement('option');
            opt_default.value = ''; 
            opt_default.text = cut_period.dataset.text_default; 
            cut_period.add(opt_default);

            period = code_period.value;

            const formData = new FormData();
            formData.append('action', 'get_cut_period');
            formData.append('code_period', period );
            
            fetch( 
                ajax_url, {
                method: 'POST',
                body: formData
            })
            .then( res => res.json() )
            .then( res => {
                
                if( cut_period ){
                    if( res.success ) {
                        
                        res.data.cut.forEach(value => {

                            let opt = document.createElement('option');
                            opt.value = value; 
                            opt.text = value; 
                            cut_period.add(opt);
                        });

                    } 
                }
                    
            })
            .catch( err => {} ); 
        })
    }
});

