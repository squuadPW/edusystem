function academic_period_changed(key) {
    let current_period = document.querySelector(`input[name="current_period"]`).value;
    let current_cut = document.querySelector(`input[name="current_cut"]`).value;
    let selected_period = document.querySelector(`select[name="academic_period[${key}]"]`).value;
    let selected_cut = document.querySelector(`select[name="academic_period_cut[${key}]"]`).value;

    if (current_period == selected_period && current_cut == selected_cut) {
        document.querySelector(`input[name="this_cut[${key}]"]`).value = 1;
        document.querySelector(`input[name="calification[${key}]"]`).required = true;
        document.getElementById(`row[${key}]`).classList.add('current-period');
    } else {
        document.querySelector(`input[name="this_cut[${key}]"]`).value = 0;
        document.querySelector(`input[name="calification[${key}]"]`).required = false;
        document.getElementById(`row[${key}]`).classList.remove('current-period');
    }
    console.log(document.querySelector(`input[name="this_cut[${key}]"]`).value)
}