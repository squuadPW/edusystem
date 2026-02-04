document.addEventListener("DOMContentLoaded", () => {
    const tabButtons = document.querySelectorAll('#es-tabs .es-tab-button');
    const tabContents = document.querySelectorAll('#es-list-container .es-tab-content');

    const activateTab = (targetId) => {
        tabButtons.forEach(btn => btn.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));

        const activeButton = document.querySelector(`.es-tab-button[data-target="${targetId}"]`);
        const activeContent = document.getElementById(targetId);

        if (activeButton) activeButton.classList.add('active');
        if (activeContent) activeContent.classList.add('active');
        
        const searchInput = document.getElementById('es-search');
        if (searchInput) {
            filterCurrentTab(searchInput.value.toLowerCase().trim(), activeContent);
        }
    };

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('data-target');
            activateTab(targetId);
        });
    });

    const search = document.getElementById('es-search');

    const filterCurrentTab = (q, activeContent) => {
        if (!activeContent) return;

        const items = activeContent.querySelectorAll('.es-item');
        items.forEach((it) => {
            const text = (it.getAttribute('data-search') || '').toLowerCase();
            it.style.display = (q === '' || text.includes(q)) ? '' : 'none';
        });
    };

    if (search) {
        search.addEventListener('input', (e) => {
            const q = e.target.value.toLowerCase().trim();
            const activeContent = document.querySelector('#es-list-container .es-tab-content.active');
            filterCurrentTab(q, activeContent);
        });
    }

    const enrollAllButton = document.getElementById("enroll-all-button");

    if (enrollAllButton) {
        enrollAllButton.addEventListener("click", () => {
            
            const originalText = enrollAllButton.innerHTML;
            
            enrollAllButton.setAttribute('disabled', 'disabled');
            enrollAllButton.classList.add('is-loading');
            enrollAllButton.innerHTML = 'Enrolling...';

            if (!confirm('Are you sure you want to enroll all listed students?')) {
                enrollAllButton.innerHTML = originalText;
                enrollAllButton.removeAttribute('disabled');
                enrollAllButton.classList.remove('is-loading');
                return;
            }
            
            const XHR = new XMLHttpRequest();
            XHR.open(
                "POST",
                `${ajax_object.ajax_url}?action=auto_enroll_students_bulk`,
                true
            );
            XHR.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            XHR.responseType = "json";

            const params = new URLSearchParams({
                action: "auto_enroll_students_bulk",
                academic_period: document.getElementById('academic_period') ? document.getElementById('academic_period').value : '',
                academic_period_cut: document.getElementById('academic_period_cut') ? document.getElementById('academic_period_cut').value : ''
            });

            const revertButtonState = () => {
                enrollAllButton.innerHTML = originalText;
                enrollAllButton.removeAttribute('disabled');
                enrollAllButton.classList.remove('is-loading');
            };

            XHR.onload = () => {
                if (XHR.status === 200 && XHR.response) {
                    setCookie('message', 'All students have been enrolled successfully.');
                    location.reload();
                } else {
                    revertButtonState();
                    alert('Error during enrollment. Please try again.');
                }
            };
            
            XHR.onerror = () => {
                revertButtonState();
                alert('Network error or connection failed. Please check your connection.');
            };

            XHR.send(params.toString());
        });
    }

    function setCookie(name, value) {
        const date = new Date();
        date.setTime(date.getTime() + 1 * 24 * 60 * 60 * 1000);
        const expires = "; expires=" + date.toGMTString();
        document.cookie = name + "=" + value + expires + "; path=/";
    }

    // Logic for Dynamic Selectors (Period & Cut)
    const periodSelect = document.getElementById('academic_period');
    const cutSelect = document.getElementById('academic_period_cut');
    
    if (periodSelect && cutSelect) {
        const translation = { select_cut: cutSelect.dataset.textoption || 'Select Term' };

        const fetchCuts = (period) => {
            const formData = new FormData();
            formData.append('action', 'get_cuts_by_period');
            formData.append('period', period);

            fetch(ajax_object.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Keep the current value if valid, otherwise reset
                // But generally, when changing period, cuts change entirely, so reset is safer unless we are just reloading options for same period
                // However, the user flow is: Change Period -> Fetch New Cuts.
                // The current selection of cut likely doesn't exist in the new period, so we don't try to preserve it unless it's a reload.
                // But this runs on change.
                
                cutSelect.innerHTML = '';

                // Default Option
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = translation.select_cut;
                cutSelect.appendChild(defaultOption);

                data.forEach(cut => {
                    const option = document.createElement('option');
                    option.value = cut.cut;
                    option.textContent = cut.cut;
                    cutSelect.appendChild(option);
                });
            })
            .catch(err => console.error('Error fetching cuts:', err));
        };

        periodSelect.addEventListener('change', function() {
            const period = this.value;
            if (period) {
                // Reset cut select to loading or empty while fetching
                cutSelect.innerHTML = '<option value="">Loading...</option>';
                fetchCuts(period);
            } else {
                cutSelect.innerHTML = '';
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = translation.select_cut;
                cutSelect.appendChild(defaultOption);
            }
        });
    }
});