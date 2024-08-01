// Get the modal and close elements
var modal = document.getElementById('modal-contraseña');
var save = document.getElementById('save_password');

    if (save) {
        // Agrega un evento de click al botón save
        save.addEventListener('click', function(event) {
            // Evita que el formulario se envíe de manera tradicional
            event.preventDefault();

            // Obtiene los valores de los campos de contraseña y confirmación
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('confirm_password').value;

            if (!password || !confirmPassword) {
                alert('You must enter a password before continuing');
                return;
            }

            // Verifica si las contraseñas coinciden
            if (password !== confirmPassword) {
                alert('Passwords do not match');
                return;
            }

            // Crea un objeto con los datos del formulario
            var datos = {
                'action': 'create_password',
                'password': password,
                'confirm_password': confirmPassword
            };

            // Realiza la solicitud AJAX
            jQuery.ajax({
                type: 'POST',
                url: ajax_object.ajax_url + '?action=create_password',
                data: datos,
                success: function(response) {
                    // Maneja la respuesta del servidor
                    if (response.success) {
                        alert('Password saved successfully');
                        // Cierra el modal
                        modal.style.display = 'none';
                        location.reload();
                    } else {
                        alert('Error saving password');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        });
    }

    if (modal) {
        // Show the modal when the page loads
        window.onload = function() {
            modal.style.display = 'block';
        };
    }

    // Evitar que se cierre el modal al hacer clic fuera de él
    window.onclick = function(event) {
        if (event.target == modal) {
            event.stopPropagation();
        }
    };

    // Evitar que se cierre el modal al presionar la tecla Esc
    window.onkeydown = function(event) {
        if (event.key == 'Escape') {
            event.preventDefault();
        }
    };