document.addEventListener('DOMContentLoaded', function() {
	var openModalBtn = document.getElementById('open-upload-modal');
	var modal = document.getElementById('upload-modal');
	if (openModalBtn && modal) {
		openModalBtn.addEventListener('click', function(e) {
			e.preventDefault();
			modal.style.display = 'block';
		});
	}
	// Cerrar modal con los botones de salida
	var closeBtns = document.querySelectorAll('.modal-close');
	closeBtns.forEach(function(btn) {
		btn.addEventListener('click', function() {
			modal.style.display = 'none';
		});
	});

	if (document.getElementById('program-identificator')) {
        let timeout;
        document.getElementById('program-identificator').addEventListener('change', function(e) {
            clearTimeout(timeout);
            timeout = setTimeout(() => {

                document.getElementById('scholarship-element').style.display = 'none';

                var xhr = new XMLHttpRequest();
                xhr.open('POST',wp_ajax.url,true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var raw;
                        try {
                            raw = JSON.parse(xhr.responseText);
                        } catch (e) {
                            // invalid JSON
                            console.error('Invalid JSON response for payment plans:', e, xhr.responseText);
                            return;
                        }

                        // Normalize response. WordPress AJAX often returns { success: true, data: ... }
                        var plans = null;
                        if (raw === null || raw === undefined) {
                            plans = null;
                        } else if (Array.isArray(raw)) {
                            // array of plans
                            plans = raw;
                        } else if (raw.data && (Array.isArray(raw.data) || typeof raw.data === 'object')) {
                            // { success: true, data: { plans: [...] } } or { success: true, data: [...] }
                            if (raw.data.plans) plans = raw.data.plans;
                            else plans = raw.data;
                        } else if (raw.plans) {
                            plans = raw.plans;
                        } else if (typeof raw === 'object') {
                            // object map like { "1": "Plan A", "2": "Plan B" }
                            plans = raw;
                        }

                        var stateSelect = document.getElementById('payment-plan-identificator');
                        stateSelect.innerHTML = '';

                        var count = 0;
                        if (plans) {
                            // If plans is an array of objects [{id, name}] or array of strings
                            if (Array.isArray(plans)) {
                                for (var i = 0; i < plans.length; i++) {
                                    var p = plans[i];
                                    var option = document.createElement('option');
                                    if (p && typeof p === 'object') {
                                        // Use `identificator` as value when available, fallback to id or index
                                        option.value = p.identificator !== undefined && p.identificator !== '' ? p.identificator : (p.id !== undefined ? p.id : i);
                                        // Label: name (description) if description exists
                                        var name = p.name !== undefined ? p.name : (p.label !== undefined ? p.label : String(option.value));
                                        var desc = p.description !== undefined && p.description !== null && String(p.description).trim() !== '' ? String(p.description).trim() : '';
                                        option.text = desc ? name + ' (' + desc + ')' : name;
                                    } else {
                                        option.value = p;
                                        option.text = p;
                                    }
                                    stateSelect.appendChild(option);
                                    count++;
                                }
                            } else {
                                // object map
                                for (var key in plans) {
                                    if (!Object.prototype.hasOwnProperty.call(plans, key)) continue;
                                    var option = document.createElement('option');
                                    option.value = key;
                                    option.text = plans[key];
                                    stateSelect.appendChild(option);
                                    count++;
                                }
                            }
                        }

                        // Show scholarship element only if we added at least one option
                        if (count > 0) {
                            document.getElementById('scholarship-element').style.display = 'block';
                        } else {
                            document.getElementById('scholarship-element').style.display = 'none';
                        }
                    }
                };
                xhr.send('action=get_payments_plans_by_program&program_id=' + e.target.value);

            }, 50); // Cambia 2000 a 1000 para 1 segundo si lo prefieres
        });
    }
});