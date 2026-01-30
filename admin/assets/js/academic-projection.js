function academic_period_changed(key) {

    let current_period = document.querySelector(`input[name="current_period"]`).value;
    let current_cut = document.querySelector(`input[name="current_cut"]`).value;
    let selected_period = document.querySelector(`select[name="academic_period[${key}]"]`).value;
    let selected_cut = document.querySelector(`select[name="academic_period_cut[${key}]"]`).value;
    document.querySelector(`input[name="completed[${key}]"]`).checked = true;

    if (current_period == selected_period && current_cut == selected_cut) {
        document.querySelector(`input[name="this_cut[${key}]"]`).value = 1;
        document.querySelector(`input[name="calification[${key}]"]`).required = false;
        document.getElementById(`row[${key}]`).classList.add("current-period");
    } else {
        document.querySelector(`input[name="this_cut[${key}]"]`).value = 0;
        document.querySelector(`input[name="calification[${key}]"]`).required = true;
        document.getElementById(`row[${key}]`).classList.remove("current-period");
    }
}

let preview_grades = document.getElementById("preview-grades");
if (preview_grades) {
    preview_grades.addEventListener("click", async (e) => {
        document.getElementById("modal-grades").style.display = "block";
        document.body.classList.add("modal-open");
        setTimeout(() => {
            window.scrollTo(0, 0);
        }, 100);
    });
}

let close_modal_grades = document.getElementById("close-modal-grades");
if (close_modal_grades) {
    close_modal_grades.addEventListener("click", async (e) => {
        document.getElementById("modal-grades").style.display = "none";
        document.body.classList.remove("modal-open");
        setTimeout(() => {
            window.scrollTo(0, 0);
        }, 100);
    });
}

let download_grades = document.getElementById("download-grades");
if (download_grades) {
    download_grades.addEventListener("click", async (e) => {
        download_grades.disabled = true;
        var element = document.getElementById("content-pdf");
        var opt = {
            margin: [10, 0, 10, 0],
            filename: "califications.pdf",
            image: { type: "jpeg", quality: 0.98 },
            jsPDF: { unit: "mm", format: "a4", orientation: "portrait" },
            html2canvas: { scale: 3 },
        };

        html2pdf().set(opt).from(element).save();
        download_grades.disabled = false;
    });
}

let openModalDownloadMoodleNotes = document.getElementById("openModalDownloadMoodleNotes");
if (openModalDownloadMoodleNotes) {
    openModalDownloadMoodleNotes.addEventListener("click", async (e) => {
        document.getElementById("modalDownloadMoodleNotes").style.display = "block";
        document.body.classList.add("modal-open");
        setTimeout(() => {
            window.scrollTo(0, 0);
        }, 100);
    });
}

document.querySelectorAll(".modal-close").forEach((close) => {
    close.addEventListener("click", (e) => {
        document.getElementById("modalDownloadMoodleNotes").style.display = "none";
        document.body.classList.remove("modal-open");
        setTimeout(() => {
            window.scrollTo(0, 0);
        }, 100);
    });
});

let close_offer = document.getElementById("close-offer");
if (close_offer) {
    close_offer.addEventListener("click", async (e) => {
        close_offer.disabled = true;
        const urlParams = new URLSearchParams(window.location.search);
        const academic_period = urlParams.get("academic_period"); 
        const academic_period_cut = urlParams.get("academic_period_cut"); 
        const subject_id = urlParams.get("subject_id"); 
        const section = urlParams.get("section"); 
        const close_offfer = document.getElementById("close-offer").value;

        const XHR = new XMLHttpRequest();
        XHR.open("POST", projection_data.url, true);
        XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        XHR.responseType = "text";
        XHR.send(
            "action=" +
            projection_data.action +
            "&academic_period=" + 
            academic_period +
            "&academic_period_cut=" + 
            academic_period_cut +
            "&subject_id=" + 
            subject_id +
            "&section=" + 
            section +
            "&close_offer=" + close_offfer
        );
        XHR.onload = function () {
            if (this.readyState == "4" && XHR.status === 200) {
                // alert("The offer has been successfully closed.");
                location.reload();
            } else {
                close_offer.disabled = false;
                alert("An error occurred while closing the offer. Please try again.");
            }
        };
    });
}

document.addEventListener("DOMContentLoaded", function () {

    academic_projection_form = document.getElementById('academic_projection_form');
    if (academic_projection_form) {

        academic_projection_form.addEventListener('submit', (e) => {
            e.preventDefault();

            restricted_input = document.getElementById('restricted_subjects');
            const restricted_subjects = ( restricted_input ) ? JSON.parse( restricted_input.value ) : [];
            
            let subjects_conflict = [];

            const selected_checkboxes = document.querySelectorAll('input[type="checkbox"][data-subject_id]:checked');
            selected_checkboxes.forEach( checkbox => {

                const subject_id = parseInt(checkbox.getAttribute('data-subject_id'));

                const conflict = restricted_subjects.find(s => parseInt(s.id) == subject_id);

                if (conflict) subjects_conflict.push(`${conflict.name} - ${conflict.code_subject}`);
                
            });

            if ( subjects_conflict.length == 0 ){
                
                academic_projection_form.submit();
            } else {
                modal_open_force_enrollment_subjects( subjects_conflict );
            }
            
        });
    }

    /**
     * Agrega funcionalidad a los botones de cierre de los modales.
     * Esta función busca todos los elementos con la clase 'modal-close'
     * y les asigna un evento de clic que oculta los modales correspondientes
     * al ser activados.
     *
     * @return {void} No retorna ningún valor.
     */
    document.querySelectorAll(".modal-close").forEach((close) => {
        close.addEventListener("click", (e) => {

            modalForceEnrollmentSubjects = document.getElementById("modalForceEnrollmentSubjects");
            if (modalForceEnrollmentSubjects) {
                modalForceEnrollmentSubjects.style.display = "none";
            }
        });
    });
    
});

function modal_open_force_enrollment_subjects( subjects_list = [] ) {

    modal = document.getElementById("modalForceEnrollmentSubjects");
    if (modal) {
        
        ul_subjects_list = document.getElementById('modal-force-enrollment-subjects-list')
        if (ul_subjects_list) {
            ul_subjects_list.innerHTML = '';

            subjects_list.forEach(subject => {
                let li = document.createElement('li');
                li.innerHTML = subject;
                ul_subjects_list.appendChild(li);
            });
        }

        modal.style.display = "block";

        setTimeout(() => {
            window.scrollTo(0, 0);
        }, 100);
    }
}


function confirm_force_enrollment_subjects( button ) {
    
    button.disabled = true;
    document.getElementById('academic_projection_form').submit();
}


