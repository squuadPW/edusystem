// Scroll al top inmediatamente al cargar la página
window.scrollTo(0, 0);

// Opcional: Evitar que el navegador recuerde la posición del scroll al recargar
if (history.scrollRestoration) {
    history.scrollRestoration = 'manual';
}

// Eliminar el bloqueo cuando todo cargue
window.addEventListener('load', function() {
    document.getElementById('scrollLock').remove();
    document.documentElement.style.overflow = '';
    document.body.style.overflow = '';
});