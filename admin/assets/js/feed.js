document.addEventListener("DOMContentLoaded", (event) => {
    let useMaxDate = document.querySelector('input[name=use_max_date]');
    if (useMaxDate) {
        useMaxDate.addEventListener('change', (event) => {
            if (event.target.checked) {
                document.getElementById('use_max_date_input').style.display = 'block';
                document.querySelector('input[name=max_date]').required = true;
            } else {
                document.getElementById('use_max_date_input').style.display = 'none';
                document.querySelector('input[name=max_date]').required = false;
                document.querySelector('input[name=max_date]').value = '';
            }
        });
    }
});