document.addEventListener("DOMContentLoaded", () => {
    // --- Lógica de Búsqueda/Filtro (Search/Filter Logic) ---
    const search = document.getElementById('es-search');
    const list = document.getElementById('es-list');
    const items = list ? Array.prototype.slice.call(list.querySelectorAll('.es-item')) : [];

    if (search) {
        search.addEventListener('input', (e) => {
            const q = e.target.value.toLowerCase().trim();
            items.forEach((it) => {
                const text = (it.getAttribute('data-search') || '').toLowerCase();
                it.style.display = (q === '' || text.includes(q)) ? '' : 'none';
            });
        });
    }

    // --- Lógica del Botón de Inscripción Masiva (Bulk Enrollment Button Logic) ---
    const enrollAllButton = document.getElementById("enroll-all-button");

    if (enrollAllButton) {
        // Implementación real de AJAX para la inscripción masiva
        enrollAllButton.addEventListener("click", () => {
            
            // Almacenar el estado original del botón
            const originalText = enrollAllButton.innerHTML;
            
            // 1. DESHABILITAR y establecer clase de carga
            enrollAllButton.setAttribute('disabled', 'disabled'); // Usamos setAttribute
            enrollAllButton.classList.add('is-loading');         // Añadimos clase para CSS
            enrollAllButton.innerHTML = 'Enrolling...';

            // 2. Pedir confirmación al usuario
            if (!confirm('Are you sure you want to enroll all listed students?')) {
                // Si el usuario CANCELA la acción:
                enrollAllButton.innerHTML = originalText; // Revertir el texto
                enrollAllButton.removeAttribute('disabled'); // Quitar atributo de deshabilitado
                enrollAllButton.classList.remove('is-loading'); // Quitar clase
                return;
            }

            // Si el usuario CONFIRMA, el botón ya está deshabilitado.
            
            const XHR = new XMLHttpRequest();
            // ... (Resto de la configuración XHR)
            XHR.open(
                "POST",
                `${ajax_object.ajax_url}?action=auto_enroll_students_bulk`,
                true
            );
            XHR.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            XHR.responseType = "json";

            const params = new URLSearchParams({
                action: "auto_enroll_students_bulk",
            });

            // Función para revertir el estado del botón
            const revertButtonState = () => {
                enrollAllButton.innerHTML = originalText;
                enrollAllButton.removeAttribute('disabled');
                enrollAllButton.classList.remove('is-loading');
            };

            XHR.onload = () => {
                // Revertir el estado del botón                
                if (XHR.status === 200 && XHR.response) {
                    setCookie('message', 'All students have been enrolled successfully.');
                    location.reload();
                } else {
                  revertButtonState();
                  alert('Error during enrollment. Please try again.');
                }
            };
            
            XHR.onerror = () => {
                // Revertir el estado del botón
                revertButtonState();
                alert('Network error or connection failed. Please check your connection.');
            };

            XHR.send(params.toString());
        });
    }

    /**
     * Sets a cookie with the specified name, value, and a 1-day expiration.
     * @param {string} name - The name of the cookie.
     * @param {string} value - The value of the cookie.
     */
    function setCookie(name, value) {
        const date = new Date();
        date.setTime(date.getTime() + 1 * 24 * 60 * 60 * 1000); // 1 day
        const expires = "; expires=" + date.toGMTString();
        document.cookie = name + "=" + value + expires + "; path=/";
    }
});