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
});